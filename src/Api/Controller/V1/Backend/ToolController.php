<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Controller\Controller;
use Common\Core\Annotation\Security;

/**
 * The ToolController provides common actions to parse and transform values
 * and return them to the client.
 */
class ToolController extends Controller
{
    /**
     * Returns a list slug list for the strings passed in the request
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function slugAction(Request $request)
    {
        $slug = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);
        $slug = \Onm\StringUtils::generateSlug($slug);

        return new JsonResponse([ 'slug' => $slug ]);
    }

    /**
     * Returns the information translated to the locale.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('es.openhost.module.multilanguage')
     *          and hasExtension('es.openhost.module.translation')")
     */
    public function translateStringAction(Request $request)
    {
        $from       = $request->get('from');
        $to         = $request->get('to');
        $translator = $request->get('translator');
        $data       = $request->get('data');

        if (empty($from) || empty($to) || is_null($translator) || empty($data)) {
            return new JsonResponse('Invalid request', 400);
        }

        $translator = (int) $translator;

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('translators');

        if (is_array($settings) && array_key_exists($translator, $settings)) {
            $translator = $settings[(int) $translator];
        }

        if (!is_array($translator)) {
            return new JsonResponse('No translators', 404);
        }

        $translator = $this->get('core.factory.translator')->get(
            $translator['translator'],
            $translator['from'],
            $translator['to'],
            $translator['config']
        );

        $data = array_map(function ($a) use ($translator) {
            $value = $translator->translate(htmlentities($a));

            return mb_convert_encoding(html_entity_decode($value), 'UTF-8');
        }, $data);

        return new JsonResponse($data);
    }

    /**
     * Returns the information translated to the locale.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('es.openhost.module.multilanguage')
     *          and hasExtension('es.openhost.module.translation')")
     */
    public function translateContentsAction(Request $request)
    {
        $from       = $request->get('from');
        $to         = $request->get('to');
        $translator = $request->request->getInt('translator');
        $ids        = $request->get('ids');


        if (empty($from) || empty($to) || is_null($translator) || empty($ids)) {
            return new JsonResponse('Invalid request', 400);
        }

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('translators');

        $translator = $settings[$translator];

        if (empty($translator)) {
            return new JsonResponse('No translators', 404);
        }

        $translator = $this->get('core.factory.translator')->get(
            $translator['translator'],
            $translator['from'],
            $translator['to'],
            $translator['config']
        );
        $properties = \Article::getL10nKeys();

        $updates = [];
        foreach ($ids as $id) {
            $article = new \Article($id);

            $propertyValueMap = [];

            foreach ($properties as $property) {
                $updates[$id][$property] = [];

                $propertyValueMap[$property] = $article->{$property};

                // Ensure the original property is localized
                $propertyValueMap[$property] = $this->get('data.manager.filter')
                    ->set($propertyValueMap[$property])
                    ->filter('unlocalize')
                    ->get();

                // Ensure the original property is valid
                if (!is_array($propertyValueMap[$property]) || !array_key_exists($from, $propertyValueMap[$property])) {
                    $propertyValueMap[$property][$from] = '';
                }

                // Only translate if the original property is not empty
                if (!empty($propertyValueMap[$property][$from])) {
                    $value = $translator->translate(htmlentities(
                        $propertyValueMap[$property][$from]
                    ));
                    $value = mb_convert_encoding(html_entity_decode($value), 'UTF-8');
                } else {
                    $propertyValueMap[$property][$to] = $propertyValueMap[$property][$from];
                }

                $propertyValueMap[$property][$to] = $value;
            }

            $this->saveContent($article, $propertyValueMap);
        }

        return new JsonResponse(['type' => 'success', 'message' => _('Contents translated successfully')], 200);
    }

    /**
     * Updates an article from the object itself and an array or properties to update
     *
     * @param Article $content the article object instance to update
     * @param array $propertiesToUpdate the list of properties and its values
     *
     * @return boolean true if the article was created successfully
     **/
    private function saveContent($content, $propertiesToUpdate)
    {
        $relatedSrv = $this->get('related_contents');

        $data = [
            'agency'         => $content->agency,
            'category'       => $content->category,
            'content_status' => $content->content_status,
            'description'    => $content->description,
            'endtime'        => $content->endtime,
            'fk_author'      => $content->fk_author,
            'fk_video'       => $content->fk_video,
            'fk_video2'      => $content->fk_video2,
            'footer_video'   => $content->footer_video,
            'footer_video2'  => $content->footer_video2,
            'frontpage'      => $content->frontpage,
            'id'             => $content->id,
            'img1'           => $content->img1,
            'img2'           => $content->img2,
            'metadata'       => $content->metadata,
            'relatedFront'   => array_map(function ($el) {
                return $el[1];
            }, $relatedSrv->getRelations($content->id, 'frontpage')),
            'relatedHome'    => array_map(function ($el) {
                return $el[1];
            }, $relatedSrv->getRelations($content->id, 'home')),
            'relatedInner'   => array_map(function ($el) {
                return $el[1];
            }, $relatedSrv->getRelations($content->id, 'inner')),
            'slug'           => $content->slug,
            'starttime'      => $content->starttime,
            'with_comment'   => $content->with_comment,
            'params'         => $content->params,
        ];

        $data = array_merge($data, $propertiesToUpdate);

        return $content->update($data);
    }
}
