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

        $params = $request->request->get('params');
        $inrss  = ($params && array_key_exists('inrss', $params) && $params['inrss'] === '1') ? 1 : 0;

        $data = [
            'id'                  => $request->request->getDigits('id', 0),
            'name'                => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'inmenu'              => $request->request->getDigits('inmenu', 0),
            'subcategory'         => $request->request->getDigits('subcategory', 0),
            'internal_category'   => $request->request->getDigits('internal_category'),
            'logo_path'           => $request->request->filter('logoPath', '', FILTER_SANITIZE_STRING),
            'color'               => $request->request->filter('color', '', FILTER_SANITIZE_STRING),
            'params'  => [
                'inrss'           => $inrss,
            ],
        ];

        // Check if at least have the default language for the title
        $locale = $this->get('core.locale')->setContext('frontend')->getLocale();

        if (isset($data['title']) &&
            empty($data['title']) ||
            (
                is_array($data['title']) &&
                (!isset($data['title'][$locale]) || empty($data['title'][$locale]))
            )
        ) {
            $msg->add(
                _('The title and slug are required.'),
                'error',
                400
            );
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $logoPath = '';
        if (!empty($_FILES) && isset($_FILES['logo_path'])) {
            $nameFile  = $_FILES['logo_path']['name'];
            $uploadDir = MEDIA_PATH . '/sections/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir);
            }

            if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploadDir . $nameFile)) {
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
            if (empty($data['logo_path'])) {
                $data['logo_path'] = '1';
            }
        }

        if ($category->{$execMethod}($data)) {
            dispatchEventWithParams('category.' . $execMethod, ['category' => $category]);
            $msg->add(
                sprintf(_('Category "%s" ' . $execMethod . 'd successfully.'), $data['id']),
                'success',
                201
            );
            $data['id'] = $category->pk_content_category;
            return new JsonResponse(['message' => $msg->getMessages(), 'category' => $data['id']], $msg->getCode());
        }

        return new JsonResponse(
            ['message' => _('Oups! Seems that we had an unknown problem while trying to run your request.')],
            500
        );
    }
}
