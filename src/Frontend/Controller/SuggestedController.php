<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the frontend controller for the suggested.
 */
class SuggestedController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request)
    {
        try {
            $params    = $request->query->all();
            $suggested = $this->get('core.helper.content')->getSuggested(
                $params['content_type_name'],
                $params['category_id'],
                $params['pk_content'] ?? null
            );

            $xtags = [ sprintf('suggested,category-%d', $params['category_id']) ];

            $contents = array_map(function ($item) {
                return sprintf('article-%d', $item->pk_content);
            }, $suggested);

            if (!empty($contents)) {
                $xtags[] = implode(',', $contents);
            }

            return $this->render(
                $params['tpl'] ?? 'common/suggested_contents_images.tpl',
                array_merge(
                    $params,
                    [
                        'suggested'   => $suggested,
                        'x-tags'      => implode(',', $xtags),
                        'x-cacheable' => true,
                        'x-cache-for' => '100d'
                    ]
                )
            );
        } catch (\Exception $e) {
            return new Response();
        }
    }
}
