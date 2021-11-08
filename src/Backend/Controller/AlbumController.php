<?php

namespace Backend\Controller;

use Api\Exception\GetListException;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $version  = $request->query->getDigits('frontpage_version_id', 1);
        $epp      = 8;
        $oql      = 'content_type_name = "album" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'album');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        $oql .= ' order by created desc limit ' . $epp;

        if ($page > 1) {
            $oql .= ' offset ' . ($page - 1) * $epp;
        }

        try {
            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.content')->getList($oql);
            $albums   = $response['items'];
            $total    = $response['total'];

            $this->get('core.locale')->setContext($context);

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name' => 'backend_albums_content_provider',
                ],
            ]);

            return $this->render('album/content-provider.tpl', [
                'albums'     => $albums,
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }
}
