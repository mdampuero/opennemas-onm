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
        $items = [];
        $today = date('Y-m-d');

        // Initialize the OQL for events
        $eventsOql['query'] = ' inner join contentmeta as cm1
                on contents.pk_content = cm1.fk_content
                and cm1.meta_name = "event_start_date"
                left join contentmeta as cm2
                on contents.pk_content = cm2.fk_content
                and cm2.meta_name = "event_end_date"
                left join contentmeta as cm3
                on contents.pk_content = cm3.fk_content
                and cm3.meta_name = "event_start_hour"
                left join contentmeta as cm4
                on contents.pk_content = cm4.fk_content
                and cm4.meta_name = "event_end_hour"';

        // Filter for events that are either ongoing or upcoming
        $eventsOql['where'] = ' and ('
            . ' (cm1.meta_value is not null and cm1.meta_value >= "' . $today . '")'
            . ' or (cm1.meta_value <= "' . $today . '" and cm2.meta_value >= "' . $today . '")'
        . ')';

        // If it's a event, we need order by start date, hour, and content ID
        $orderBy = 'order by cm1.meta_value asc,
            cm3.meta_value asc,
            contents.pk_content asc';

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

        $rawItems = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        // $RawItems is an object, we need to convert it to a json
        foreach ($rawItems as $item) {
            $data = $item->getData();

            $mainDomain = $this->container->get('core.instance')->getBaseUrl();
            $url        = $this->generateUrl('frontend_event_show', [
                'slug' => $data['slug']
            ]);

            // Get the category name and author name
            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // If the author name is empty, use the site name as the author
            if (empty($authorName)) {
                $authorName = $this->container->get('orm.manager')->getDataSet('Settings', 'instance')
                    ->get('site_name');
            }

            // Prepare start and end dates and formats
            // If the event has a start date and hour, format it as 'Y-m-dTH:i:s'
            // If the event has only the date, just use the date
            $startDate = !empty($data['event_start_date']) ?
                $data['event_start_date'] . (!empty($data['event_start_hour']) ? 'T' . $data['event_start_hour'] : '') :
                '';

            $endDate = !empty($data['event_end_date']) ?
                $data['event_end_date'] . (!empty($data['event_end_hour']) ? 'T' . $data['event_end_hour'] : '') :
                '';

            // Get the thumbnail
            // Use the photo helper to get the featured media thumbnail
            // If the item has no featured media, it will return an empty string
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');

            // Get the thumbnail path for the featured media
            // If the item has no featured media, it will return an empty string
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
            $items[] = [
                'allDay' => 0,
                'title' => $data['title'] ?? '',
                'url' => $mainDomain . $url,
                'author' => $authorName,
                'longitude' => $data['event_longitude'] ?? '',
                'latitude' => $data['event_latitude'] ?? '',
                'thumbnail' => $thumbnail,
                'content' => $data['description'],
                'subtype' => $categoryName ?? '',
                'nbParticipants' => 0,
                'address' => $data['event_address'] ?? '',
                'type' => $data['content_type_name'],
                'id' => 'event_' . $data['pk_content'] ?? '',
                'date' => $startDate,
                'endDate' => $endDate,
            ];
        }

        // Return the JSON response with the items
        return new JsonResponse([
            'items' => $items
        ]);
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
        // Initialize an empty array to hold the items and the main domain
        $items      = [];
        $mainDomain = $this->container->get('core.instance')->getBaseUrl();

        // Initialize the OQL for articles
        $orderBy = empty($data['type']) ?
            ' order by contents.starttime desc limit 1' :
            ' order by contents.starttime desc,
                contents.pk_content asc';

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
            $orderBy ?? '',
        );

        // Fetch the raw items using the API service
        $rawItems = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        // $RawItems is an object, we need to convert it to a json
        foreach ($rawItems as $item) {
            $data = $item->getData();

            // Variables for the article Json Response
            $categorySlug = $this->container->get('core.helper.category')
                ->getCategorySlug($item);
            $date         = $data['created']->format('Ymd');
            $hour         = $data['created']->format('His');
            $created      = $date . $hour;
            $url          = $this->generateUrl('frontend_article_show', [
                'category_slug' => $categorySlug,
                'slug' => $data['slug'],
                'created' => $created,
                'id' => $data['pk_content']
            ]);

            // Get the category name and author name
            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // If the author name is empty, use the site name as the author
            if (empty($authorName)) {
                $authorName = $this->container->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('site_name');
            }

            // Get the thumbnail
            // Use the photo helper to get the featured media thumbnail
            // If the item has no featured media, it will return an empty string
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');

            // Get the thumbnail path for the featured media
            // If the item has no featured media, it will return an empty string
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
            $items[] = [
                'id' => 'article_' . $data['pk_content'] ?? '',
                'type' => $data['content_type_name'] ?? 'article',
                'title' => $data['title'] ?? '',
                'url' => $mainDomain . $url,
                'date' => $data['created']->format('Y-m-d H:i:s') ?? '',
                'author' => $authorName,
                'subtype' => $categoryName ?? '',
                'summary' => strip_tags($data['description']) ?? '',
                'content' => mb_strimwidth(strip_tags($data['body'] ?? ''), 0, 200, '...'),
                'thumbnail' => $thumbnail,
            ];
        }

        // Return the JSON response with the items
        return new JsonResponse(
            $items,
        );
    }
}
