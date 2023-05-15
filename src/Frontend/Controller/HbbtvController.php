<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays hbbtv.
 */
class HbbtvController extends Controller
{
    /**
     * Shows the frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @throws ResourceNotFoundException If the frontpage doesn't exist.
     */
    public function showAction(Request $request)
    {
        $module = $this->container->get('core.security')
            ->hasExtension('es.openhost.module.hbbtv');

        if (!$module) {
            return new ResourceNotFoundException();
        }

        $catService       = $this->container->get('api.service.category');
        $defaultThumbnail = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('logo_default');

        $defaultThumbnail = $this->container->get('core.helper.photo')
            ->getPhotoPath(intval($defaultThumbnail));

        $finalResult = [];

        $oql   = "params ~ '%\"type\";s:10:\"multimedia%' and cover_id !is null";
        $items = $catService
            ->setCount(false)
            ->getList($oql)['items'];

        $sql = "SELECT * FROM `contents`"
            . " INNER JOIN content_category ON content_category.content_id = contents.pk_content"
            . " WHERE content_type_name = 'video' AND category_id = %s AND in_litter = 0"
            . " AND content_status = 1"
            . " ORDER BY created DESC"
            . " LIMIT 15;";

        foreach ($items as $item) {
            if ($item->name == 'a-la-carta') {
                continue;
            }

            $videos = $this->container->get('api.service.content')->getListBySql(sprintf($sql, $item->id))['items'];

            if (!empty($videos)) {
                $videos = array_map(function ($element) use ($defaultThumbnail) {
                    if ($element->type && $element->type == 'Globalmest') {
                        $lastPart = explode('/', $element->path);
                        $id = str_replace('.html', '', end($lastPart));
                        $element->src = sprintf('https://vod-dd.globalmest.com/8RK9QO/%s/%s_vR5voJ.mp4', $id, $id);
                    }

                    $element->thumbnail = $element->information['thumbnail'] ?? $defaultThumbnail;
                    return $element;
                }, $videos);

                $finalResult[] = [
                    'categoryItem' => $item,
                    'videos' => $videos
                ];
            }
        }

        return $this->render('hbbtv/hbbtv.tpl', [
            'categoryTree' => $finalResult,
            'logo'         => $defaultThumbnail
        ]);
    }
}
