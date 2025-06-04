<?php

namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends FrontendController
{
    /**
     * General JSON action.
     *
     * This method handles the request and returns a JSON response based on the
     * type, category, and tag parameters.
     *
     * @param Request $request The HTTP request object containing parameters.
     * @return Response The JSON response containing the requested data.
     */
    public function generalJsonAction(Request $request): Response
    {
        // Variables to hold the data from the request
        $data = [
            'type' => $request->get('type', null),
            'category' => $request->get('category', null),
            'tag' => $request->get('tag', null),
        ];

        // Initialize OQL queries for category and tag
        if (!empty($data['category'])) {
            $data['oqlCategory']['query'] = implode(' ', [
                'inner join content_category as cc1',
                'on cc1.content_id = contents.pk_content',
                'inner join category as c1',
                'on c1.id = cc1.category_id',
            ]);

            $data['oqlCategory']['where'] = sprintf(
                " and c1.name = '%s'",
                $data['category']
            );
        }

        if (!empty($data['tag'])) {
            $data['oqlTag']['query'] = implode(' ', [
                'inner join contents_tags as ct1',
                'on ct1.content_id = contents.pk_content',
                'inner join tags as t1',
                'on t1.id = ct1.tag_id',
            ]);

            $data['oqlTag']['where'] = sprintf(
                " and t1.slug = '%s'",
                $data['tag']
            );
        }

        // Separate the logic for events and articles
        if ($data['type'] === 'event') {
            return $this->hydrateEvents($data);
        }

        // Default to articles if no type is specified or if is not an event
        return $this->hydrateArticles($data);
    }

    /**
     * Hydrate events method.
     *
     * This method retrieves events based on the provided data and returns them as a JSON response.
     *
     * @param array $data The data containing filters and parameters for retrieving events.
     * @return Response The JSON response containing the events.
     */
    protected function hydrateEvents($data) : Response
    {
        // Initialize an empty array to hold the items and the current date
        $today = date('Y-m-d');
        $limit = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('elements_in_rss', 10);

        // Initialize the OQL for events
        $eventsOql['query'] = ' inner join contentmeta as start_date_meta
                on contents.pk_content = start_date_meta.fk_content
                and start_date_meta.meta_name = "event_start_date"
                left join contentmeta as start_hour_meta
                on contents.pk_content = start_hour_meta.fk_content
                and start_hour_meta.meta_name = "event_start_hour"
                left join contentmeta as end_date_meta
                on contents.pk_content = end_date_meta.fk_content
                and end_date_meta.meta_name = "event_end_date"';

        // Filter for events that are either ongoing or upcoming
        $eventsOql['where'] = sprintf(
            ' and content_status=1 and in_litter=0 and (
                (start_date_meta.meta_value is not null and start_date_meta.meta_value >= "%s")
                or (start_date_meta.meta_value <= "%s" and end_date_meta.meta_value >= "%s")
            )',
            $today,
            $today,
            $today
        );

        // If it's a event, we need order by start date, hour, and content ID
        $orderBy = sprintf(
            'order by start_date_meta.meta_value asc,
            start_hour_meta.meta_value asc,
            contents.pk_content asc limit %d',
            $limit
        );

        // Construct the OQL for event, tag, and category filtering
        $baseOql = sprintf(
            'select * from contents'
            . ' %s %s %s'
            . ' where content_type_name = "%s"'
            . ' %s %s %s'
            . ' %s',
            $eventsOql['query'],
            $data['oqlCategory']['query'] ?? null,
            $data['oqlTag']['query'] ?? null,
            $data['type'],
            $eventsOql['where'],
            $data['oqlCategory']['where'] ?? null,
            $data['oqlTag']['where'] ?? null,
            $orderBy,
        );

        // Fetch the raw items using the API service
        $items = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        // $items is an object, we need to convert it to a json
        foreach ($items as $item) {
            $url = $this->container->get('core.helper.url_generator')->generate($item, [
                'absolute' => true,
            ]);

            // Get the category name and author name
            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // Prepare start and end dates and formats
            // If the event has a start date and hour, format it as 'Y-m-dTH:i:s'
            // If the event has only the date, just use the date
            $startDate = !empty($item->event_start_date) ?
                $item->event_start_date . (!empty($item->event_start_hour) ? 'T' . $item->event_start_hour : '') :
                '';

            $endDate = !empty($item->event_end_date) ?
                $item->event_end_date . (!empty($item->event_end_hour) ? 'T' . $item->event_end_hour : '') :
                '';

            // Get the thumbnail
            // Use the photo helper to get the featured media thumbnail
            // If the item has no featured media, it will return an empty string
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');

            $thumbnail = $photoHelper->getPhotoPath(
                $featuredHelper->getFeaturedMedia(
                    $item,
                    'inner'
                ),
                null,
                [],
                true
            );

            // Build the item array
            $events['items'][] = [
                'allDay' => 0,
                'title' => $item->title ?? '',
                'url' => $url,
                'author' => $authorName ?? 'Redacción',
                'longitude' => $item->event_longitude ?? '',
                'latitude' => $item->event_latitude ?? '',
                'thumbnail' => $thumbnail,
                'content' => strip_tags($item->body ?? ''),
                'subtype' => $categoryName ?? '',
                'nbParticipants' => 0,
                'address' => $item->event_address ?? '',
                'type' => $item->content_type_name,
                'id' => 'event_' . $item->pk_content ?? '',
                'date' => $startDate,
                'endDate' => $endDate,
            ];
        }

        // Return the JSON response with the items
        return new JsonResponse($events);
    }

    /**
     * Hydrate articles method.
     *
     * This method retrieves articles based on the provided data and returns them as a JSON response.
     *
     * @param array $data The data containing filters and parameters for retrieving articles.
     * @return Response The JSON response containing the articles.
     */
    protected function hydrateArticles($data) : Response
    {
        // Initialize the OQL for articles
        $limit = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('elements_in_rss', 10);

        $orderBy = sprintf(
            ' order by contents.starttime desc,
            contents.pk_content asc limit %d',
            $limit
        );

        $baseOql = sprintf(
            'select * from contents'
            . ' %s %s'
            . ' where content_type_name = "%s"'
            . ' %s %s'
            . ' %s',
            $data['oqlCategory']['query'] ?? null,
            $data['oqlTag']['query'] ?? null,
            empty($data['type']) ? 'article' : $data['type'],
            $data['oqlCategory']['where'] ?? null,
            $data['oqlTag']['where'] ?? null,
            $orderBy,
        );

        // Fetch the raw items using the API service
        $items = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        // $items is an object, we need to convert it to a json
        foreach ($items as $item) {
            $url = $this->container->get('core.helper.url_generator')->generate($item, [
                'absolute' => true,
            ]);

            // Get the category name and author name
            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // Get the thumbnail
            // Use the photo helper to get the featured media thumbnail
            // If the item has no featured media, it will return an empty string
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');

            // Get Thumbnail for the inner and frontpage featured images
            $thumbnail = $photoHelper->getPhotoPath(
                $featuredHelper->getFeaturedMedia($item, 'frontpage'),
                null,
                [],
                true
            );

            // Build the json response for the article
            $articles['items'][] = [
                'id' => 'article_' . $item->pk_content ?? '',
                'type' => $item->content_type_name ?? 'article',
                'title' => $item->title ?? '',
                'url' => $url,
                'date' => isset($item->starttime) ? $item->starttime->format('Y-m-d\TH:i:sO') : '',
                'author' => $authorName ?? 'Redacción',
                'categories' => [
                    $categoryName ?? '',
                ],
                'subtype' => 'Opennemas',
                'summary' => strip_tags($item->description) ?? '',
                'content' => $item->body ?? '',
                'smallThumbnail' => $thumbnail ?? '',
                'thumbnail' => $thumbnail ?? '',
                'largeThumbnail' => $thumbnail ?? '',
                'images' => [],
            ];
        }

        // Return the JSON response with the items
        return new JsonResponse($articles);
    }
}
