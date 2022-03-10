<?php

namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
        $params    = $request->query->all();
        $suggested = $this->get('core.helper.content')->getSuggested(
            $params['pk_content'],
            $params['content_type_name'],
            $params['category_id']
        );

        $xtags = [ sprintf('suggested,category-%d', $params['category_id']) ];

        $contents = array_map(function ($item) {
            return sprintf('article-%d', $item->pk_content);
        }, $suggested);

        $xtags[] = implode(',', $contents);

        return $this->render(
            $params['tpl'] ?? 'common/suggested_contents_images.tpl',
            [
                'suggested'   => $suggested,
                'x-tags'      => implode(',', $xtags),
                'x-cacheable' => true,
                'x-cache-for' => '100d'
            ]
        );
    }
}
