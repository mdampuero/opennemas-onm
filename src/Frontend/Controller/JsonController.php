<?php

namespace Frontend\Controller;

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
        $category = $request->get('category', null);
        $tag      = $request->get('tag', null);
        $type     = $request->get('type', 'article');

        // Initialize OQL queries for category and tag
        $categoryJoin = '';
        if (!empty($category)) {
            $categoryJoin = 'inner join content_category as cc1 '
                . 'on cc1.content_id = contents.pk_content '
                . 'inner join category as c1 '
                . 'on c1.id = cc1.category_id '
                . 'and c1.name = "' . $category . '"';
        }

        $tagJoin = '';
        if (!empty($tag)) {
            $tagJoin = 'inner join contents_tags as ct1 '
                . 'on ct1.content_id = contents.pk_content '
                . 'inner join tags as t1 '
                . 'on t1.id = ct1.tag_id '
                . 'and t1.slug = "' . $tag . '"';
        }

        $data = [
            'categoryJoin' => $categoryJoin,
            'tagJoin'      => $tagJoin
        ];

        // Separate the logic for events and articles
        if ($type === 'event') {
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

        // If it's a event, we need order by start date, hour, and content ID
        $orderBy = sprintf(
            'order by start_date_meta.meta_value asc,
            start_hour_meta.meta_value asc,
            contents.pk_content asc limit %d',
            $limit
        );

        // Construct the OQL for event, tag, and category filtering
        $baseOql = sprintf(
            'select * from contents '
            . 'inner join contentmeta as start_date_meta on contents.pk_content = start_date_meta.fk_content '
            . 'and start_date_meta.meta_name = "event_start_date" '
            . 'left join contentmeta as start_hour_meta on contents.pk_content = start_hour_meta.fk_content '
            . 'and start_hour_meta.meta_name = "event_start_hour" '
            . 'left join contentmeta as end_date_meta on contents.pk_content = end_date_meta.fk_content '
            . 'and end_date_meta.meta_name = "event_end_date" '
            . '%s %s '
            . 'where content_type_name = "event" and content_status=1 and in_litter=0 '
            . 'and ((start_date_meta.meta_value is not null and start_date_meta.meta_value >= "%s") '
            . 'or (start_date_meta.meta_value <= "%s" and end_date_meta.meta_value >= "%s")) '
            . '%s',
            $data['categoryJoin'],
            $data['tagJoin'],
            $today,
            $today,
            $today,
            $orderBy
        );

        $items = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        $events = [];
        foreach ($items as $item) {
            $url = $this->container->get('core.helper.url_generator')->generate($item, [
                'absolute' => true,
            ]);

            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // Create a DateTime object for event dates and use extended ISO 8601 format
            $start = $item->event_start_date . ' ' . $item->event_start_hour ?? '00:00';
            $end   = $item->event_end_date . ' ' . $item->event_end_hour ?? '00:00';

            $startDate = new \DateTime($start, new \DateTimeZone('Europe/Madrid'));
            $endDate   = new \DateTime($end, new \DateTimeZone('Europe/Madrid'));
            $startDate = $startDate->format('Y-m-d\TH:i:sO');
            $endDate   = $endDate->format('Y-m-d\TH:i:sO');

            // Get the image url
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');
            $imageUrl       = $photoHelper->getPhotoPath(
                $featuredHelper->getFeaturedMedia($item, 'frontpage'),
                null,
                [],
                true
            );

            // Add image at the beggining of the body
            if (!empty($imageUrl)) {
                $item->body = sprintf(
                    '<img src="%s" alt="%s" style="max-width:100%%; height:auto; margin-bottom:20px;" /> %s',
                    $imageUrl,
                    $item->title ?? '',
                    $item->body
                );
            }

            // Build the item array
            $events['items'][] = [
                'allDay'         => 0,
                'title'          => $item->title,
                'url'            => $url,
                'author'         => $authorName ?? 'Redacción',
                'longitude'      => $item->event_longitude,
                'latitude'       => $item->event_latitude,
                'thumbnail'      => $imageUrl,
                'content'        => $item->body,
                'subtype'        => $categoryName,
                'nbParticipants' => 0,
                'address'        => $item->event_address,
                'type'           => $item->content_type_name,
                'id'             => 'event_' . $item->pk_content,
                'date'           => $startDate,
                'endDate'        => $endDate,
            ];
        }

        // Return the JSON response with the items
        $response = new Response();
        $response->setContent(
            json_encode(
                $events,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_PRETTY_PRINT
            )
        );

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('x-cacheable', true);
        $response->headers->set('x-tags', 'rss-event');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        if (!empty($expire)) {
            $response->headers->set('x-cache-for', $expire);
        }

        return $response;
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
        $limit = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('elements_in_rss', 10);

        $orderBy = ' order by contents.starttime desc limit ' . $limit;
        $baseOql = sprintf(
            'select * from contents'
            . ' %s %s'
            . ' where content_type_name = "article"'
            . ' %s',
            $data['categoryJoin'],
            $data['tagJoin'],
            $orderBy,
        );

        $items = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        $articles = [];
        foreach ($items as $item) {
            $url = $this->container->get('core.helper.url_generator')->generate($item, [
                'absolute' => true,
            ]);

            $categoryName = $this->container->get('core.helper.category')
                ->getCategoryName($item);
            $authorName   = $this->container->get('core.helper.author')
                ->getAuthorName($item->fk_author);

            // Get the image url
            $photoHelper    = $this->container->get('core.helper.photo');
            $featuredHelper = $this->container->get('core.helper.featured_media');
            $imageUrl       = $photoHelper->getPhotoPath(
                $featuredHelper->getFeaturedMedia($item, 'frontpage'),
                null,
                [],
                true
            );

            // Add image at the beggining of the body
            if (!empty($imageUrl)) {
                $item->body = sprintf(
                    '<img src="%s" alt="%s" style="max-width:100%%; height:auto; margin-bottom:20px;" /> %s',
                    $imageUrl,
                    $item->title ?? '',
                    $item->body
                );
            }

            $articles['items'][] = [
                'id'             => 'article_' . $item->pk_content,
                'type'           => $item->content_type_name,
                'title'          => $item->title,
                'url'            => $url,
                'date'           => $item->starttime->format('Y-m-d\TH:i:sO'),
                'author'         => $authorName ?? 'Redacción',
                'categories'     => [ $categoryName ],
                'subtype'        => 'Opennemas',
                'summary'        => strip_tags($item->description),
                'content'        => $item->body,
                'smallThumbnail' => $imageUrl ?? '',
                'thumbnail'      => $imageUrl ?? '',
                'largeThumbnail' => $imageUrl ?? '',
                'images'         => [],
            ];
        }

        // Return the JSON response with the items
        $response = new Response();
        $response->setContent(
            json_encode(
                $articles,
                JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES |
                JSON_PRETTY_PRINT
            )
        );

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('x-cacheable', true);
        $response->headers->set('x-tags', 'rss-article');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        if (!empty($expire)) {
            $response->headers->set('x-cache-for', $expire);
        }

        return $response;
    }
}
