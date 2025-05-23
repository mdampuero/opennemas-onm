<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
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
            null, // specify condition for the content type
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

            $items[] = [
                "allDay"         => 0,
                "title"          => $data['title'] ?? '',
                "url"            => 'https://www.laguiago.com/?post_type=go_event&p=' . ($data['pk_content'] ?? ''),
                "author"         => 'Editor',
                "longitude"      => '',
                "latitude"       => '',
                "thumbnail"      => 'https://www.laguiago.com/wp-content/uploads/2025/05/Untitled-12.png',
                "content"        => $data['description'] ?? '',
                "subtype"        => $data['categories'][0]['name'] ?? '',
                "nbParticipants" => 0,
                "address"        => $data['event_place'] ?? '',
                "type"           => "event",
                "id"             => 'event_' . ($data['pk_content'] ?? ''),
                "date"           => isset($data['event_start_date'], $data['event_start_hour']) ?
                                    $data['event_start_date'] . 'T' . $data['event_start_hour'] . ':00+0200' : '',
                "endDate"        => isset($data['event_end_date'], $data['event_end_hour']) ?
                                    $data['event_end_date'] . 'T' . $data['event_end_hour'] . ':59+0200' : '',
            ];
        }
        return new JsonResponse([
            'items' => $items
        ]);
    }
}
