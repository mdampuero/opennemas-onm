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

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\Response;

/**
 * Displays, saves, modifies and removes users.
 */
class UserController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'USER_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_user_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'USER_CREATE',
        'delete' => 'USER_DELETE',
        'list'   => 'USER_ADMIN',
        'patch'  => 'USER_UPDATE',
        'save'   => 'USER_CREATE',
        'show'   => 'USER_UPDATE',
        'update' => 'USER_UPDATE',
        'move'   => 'USER_UPDATE',
    ];

    protected $module = 'user';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.user';

    /**
     * {@inheritdoc}
     *
     * This action is overwritten because it's used in edit profile action that
     * should be available to all users with or without having users module
     * activated.
     */
    public function updateItemAction(Request $request, $id)
    {
        if ($id != $this->getUser()->id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $msg = $this->get('core.messenger');

        $this->get('api.service.user')
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->get('core.locale')->getAvailableLocales()
        );

        return [
            'client'      => $this->getClient(),
            'countries'   => $this->get('core.geo')->getCountries(),
            'provinces'   => $this->get('core.geo')->getRegions('ES'),
            'languages'   => $languages,
            'photos'      => $this->getPhotos($items),
            'taxes'       => $this->get('vat')->getTaxes(),
            'user_groups' => $this->getUserGroups(),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ];
    }

    /**
     * Moves all contents assigned to the user to the target user.
     *
     * @param Request $request The request object.
     * @param integer $id      The user id.
     *
     * @return JsonResponse The response object.
     */
    public function moveItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('move'));
        $target = $request->request->get('target', null);
        $msg    = $this->get('core.messenger');
        $this->get($this->service)->moveItem($id, $target);
        $msg->add(_('Item saved successfully'), 'success');
        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Downloads the list of users.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function getReportAction()
    {
        // Get information
        $users      = $this->get('api.service.user')->getReport();
        $userGroups = $this->getUserGroups();
        dump($users);
        die();

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

        foreach ($users as $user) {
            $groupNames = [];
            foreach ($user['user_groups'] as $group) {
                $groupId = $group['user_group_id'];
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
        $response->headers->set('Content-Description', 'users list Export');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=users-' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        return $response;
    }

    /**
     * Returns the list of photos for all items in the list.
     *
     * @param mixed $items The item or the list of items.
     *
     * @return array The list of photos.
     */
    protected function getPhotos($items = null) : array
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

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

            return $this->get('api.service.content')->responsify($photos);
        } catch (GetItemException $e) {
        }
    }

    /**
     * Returns the list of user groups.
     *
     * @return array The list of user groups.
     */
    protected function getUserGroups() : array
    {
        $ugs = $this->get('api.service.user_group');

        $userGroups = $ugs->getList()['items'];

        return $ugs->responsify($this->get('data.manager.filter')
            ->set($userGroups)
            ->filter('mapify', [ 'key' => 'pk_user_group'])
            ->get());
    }
}
