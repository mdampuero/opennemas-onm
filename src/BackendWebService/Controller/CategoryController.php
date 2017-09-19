<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackendWebService\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends ContentController
{

    /**
     * Returns a list of available locales by name.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveAction(Request $request)
    {
        $msg = $this->get('core.messenger');
        if (count($request->request) < 1) {
            $msg->add(
                _('Category data send not valid.'),
                'error',
                400
            );

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $data = array(
            'id'                  => $request->request->getDigits('id', 0),
            'name'                => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'inmenu'              => $request->request->getDigits('inmenu', 0),
            'subcategory'         => $request->request->getDigits('subcategory', 0),
            'internal_category'   => $request->request->getDigits('internal_category'),
            'logo_path'           => $request->request->filter('logo_path', '', FILTER_SANITIZE_STRING),
            'color'               => $request->request->filter('color', '', FILTER_SANITIZE_STRING),
            'params'  => array(
                'inrss' => $inrss,
            ),
        );

        $logoPath = '';
        if (!empty($_FILES) && isset($_FILES['logo_path'])) {
            $nameFile  = $_FILES['logo_path']['name'];
            $uploaddir = MEDIA_PATH . '/sections/' . $nameFile;

            if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                $data['logo_path'] = $nameFile;
            }
        }

        $category   = null;
        $execMethod = 'update';
        if (empty($data['id']) || $data['id'] === 0) {
            $category   = new \ContentCategory();
            $execMethod = 'create';
        } else {
            $category = new \ContentCategory($data['id']);
        }

        if ($category->{$execMethod}($data)) {
            dispatchEventWithParams('category.' . $method, ['category' => $category]);
            $msg->add(
                sprintf(_('Category "%s" ' . $method . 'd successfully.'), $data['title']),
                'success',
                200
            );
            $data['id'] = $category->pk_content_category;
            return new JsonResponse(['message' => $msg->getMessages(), 'category' => $data], $msg->getCode());
        }

        return new JsonResponse(_('Oups! Seems that we had an unknown problem while trying to run your request.'), 500);
    }
}
