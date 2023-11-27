<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

/**
 * Displays, saves, modifies and removes authors.
 */
class AuthorController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_author_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'AUTHOR_CREATE',
        'delete' => 'AUTHOR_DELETE',
        'list'   => 'AUTHOR_ADMIN',
        'patch'  => 'AUTHOR_UPDATE',
        'save'   => 'AUTHOR_CREATE',
        'show'   => 'AUTHOR_UPDATE',
        'update' => 'AUTHOR_UPDATE',
    ];

    protected $module = 'author';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.author';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $response = [
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ];
        if (empty($items)) {
            return $response;
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $photos = [];

        $ids = array_filter(array_map(function ($user) {
            return $user->avatar_img_id;
        }, $items), function ($photo) {
                return !empty($photo);
        });

        try {
            $photos = $this->get('api.service.content')->getListByIds($ids)['items'];
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_content' ])
                ->get();

            $photos = [ 'photos' => $this->get('api.service.content')->responsify($photos) ];
            return array_merge($response, $photos);
        } catch (GetItemException $e) {
        }
        $photos = [ 'photos' => $photos, ];
        return array_merge($response, $photos);
    }

    /**
     * Downloads the list of authors.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function getReportAction()
    {
        // Get information
        $authors    = $this->get('api.service.author')->getReport();
        $userGroups = $this->getUserGroups();

        // Prepare contents for CSV
        $headers = [
            _('Name'),
            _('Email'),
            _('Username'),
            _('User groups'),
            _('Social'),
            _('Enabled')
        ];

        $data = [];

        foreach ($authors as $user) {
            $groupNames = [];
            $userGroupsArray = explode(',', $user['user_groups']);

            foreach ($userGroupsArray as $groupId) {
                if (isset($userGroups[$groupId])) {
                    $groupNames[] = $userGroups[$groupId]['name'];
                }
            }

            $userGroupNames = implode(', ', $groupNames);

            $userInfo = [
                $user['name'],
                $user['email'],
                $user['username'],
                $userGroupNames,
                $user['twitter'] ?? '',
                $user['activated'] ? '✓' : '✗'
            ];

            $data[] = $userInfo;
        }
        // Prepare the CSV content
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $writer->insertOne($headers);
        $writer->insertAll($data);
        $response = new Response($writer, 200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'authors list Export');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=authors-' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }
}
