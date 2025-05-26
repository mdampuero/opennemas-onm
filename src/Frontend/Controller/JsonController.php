<?php

namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class JsonController extends FrontendController
{
    /**
     * General JSON response action.
     *
     * This action returns a dummy JSON response.
     *
     * @param Request $request The HTTP request object.
     * @return JsonResponse The JSON response.
     */
    public function generalJsonAction(Request $request): Response
    {
        $type     = htmlspecialchars($request->get('type', 'article'));
        $category = htmlspecialchars($request->get('category', null));
        $tag      = htmlspecialchars($request->get('tag', null));

        if (!empty($category)) {
            $oqlCategory[0]  = ' inner join content_category as cc1 on cc1.content_id = contents.pk_content';
            $oqlCategory[0] .= ' inner join category as c1 on c1.id = cc1.category_id';
            $oqlCategory[1]  = " and c1.name = '" . $category . "'";
        }

        if (!empty($tag)) {
            $oqlTag[0]  = ' inner join contents_tags as ct1 on ct1.content_id = contents.pk_content';
            $oqlTag[0] .= ' inner join tags as t1 on t1.id = ct1.tag_id';
            $oqlTag[1]  = " and t1.slug = '" . $tag . "'";
        }

        $baseOql = sprintf(
            'select * from contents'
            . ' %s %s %s'
            . ' where content_type_name = "%s"'
            . ' %s %s %s',
            null,
            $oqlCategory[0] ?? null,
            $oqlTag[0] ?? null,
            $type,
            null, // specify condition for the type
            $oqlCategory[1] ?? null,
            $oqlTag[1] ?? null,
        );

        $rawItems = $this->get('api.service.content')->getListBySql($baseOql)['items'];

        // $RawItems is an object, we need to convert it to an json
        $items = [];

        foreach ($rawItems as $item) {
            $data = $item->getData();

            $categorySlug = $this->getCategorySlug($item);
            $mainDomain   = $this->container->get('core.instance')->getBaseUrl();

            $date    = $data['created']->format('Ymd');
            $hour    = $data['created']->format('His');
            $created = $date . $hour;
            $url     = $this->generateUrl('frontend_article_show', [
                'category_slug' => $categorySlug,
                'slug' => $data['slug'],
                'created' => $created,
                'id' => $data['pk_content']
            ]);

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

            if ($type === 'event') {
                $url = $this->generateUrl('frontend_event_show', [
                    'slug' => $data['slug']
                ]);
            }

            $startDate = ($type === 'event' && !empty($data['event_start_date']) && !empty($data['event_start_hour']))
                ? $data['event_start_date'] . 'T' . $data['event_start_hour']
                : (($type === 'article' && !empty($data['starttime']))
                    ? $data['starttime']->format('Y-m-d H:i:s')
                    : '');

            $endDate = ($type === 'event' && !empty($data['event_end_date']) && !empty($data['event_end_hour']))
                ? $data['event_end_date'] . 'T' . $data['event_end_hour']
                : '';

            $items[] = [
                'allDay' => 0,
                'title' => $data['title'] ?? '',
                'url' => $mainDomain . $url,
                'author' => $this->getAuthorData($item),
                'longitude' => $data['event_map_longitude'] ?? '',
                'latitude' => $data['event_map_latitude'] ?? '',
                'thumbnail' => $thumbnail,
                'content' => $data['description'] ?? '',
                'subtype' => $this->getCategoryName($item) ?? '',
                'nbParticipants' => 0,
                'address' => $data['event_place'] ?? '',
                'type' => $data['event_type'] ?? '',
                'id' => $type . '_' . ($data['pk_content'] ?? ''),
                'date' => $startDate,
                'endDate' => $endDate,
            ];
        }

        return new JsonResponse([
            'items' => $items
        ]);
    }

    /**
     * Method to retrieve the author information.
     *
     * @param \Content $content The content object.
     *
     * @return string The author information.
     */
    protected function getAuthorData($content)
    {
        $author = $this->container->get('core.helper.author')->getAuthorName($content->fk_author);

        if (empty($author)) {
            $author = $this->container->get('orm.manager')->getDataSet('Settings', 'instance')
                ->get('site_name');
        }

        return $author;
    }

    /**
     * Method to retrieve the name of the category for the provided content.
     *
     * @param \Content $content The content object.
     *
     * @return string The category name or an empty string if not found.
     */
    protected function getCategoryName($content)
    {
        $category = $this->container->get('core.helper.category')
            ->getCategoryName($content);

        return $category ?? '';
    }

    protected function getCategorySlug($content)
    {
        $category = $this->container->get('core.helper.category')
            ->getCategorySlug($content);

        return $category ?? '';
    }
}
