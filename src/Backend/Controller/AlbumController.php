<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

class AlbumController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'ALBUM_CREATE',
        'update' => 'ALBUM_UPDATE',
        'list'   => 'ALBUM_ADMIN',
        'show'   => 'ALBUM_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'album_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'album';

    /**
     * Render the content provider for albums.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Album');

        $filters = [
            'content_type_name' => [ [ 'value' => 'album' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $countAlbums = true;
        $albums      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countAlbums);

        $this->get('core.locale')->setContext('frontend');

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countAlbums,
            'route'       => [
                'name'   => 'backend_albums_content_provider',
                'params' => ['category' => $categoryId]
            ],
        ]);

        return $this->render('album/content-provider.tpl', [
            'albums'     => $albums,
            'pagination' => $pagination,
        ]);
    }
}
