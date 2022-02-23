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

        return $this->render(
            'common/suggested_contents_images.tpl',
            [ 'suggested' => $suggested ]
        );
    }
}
