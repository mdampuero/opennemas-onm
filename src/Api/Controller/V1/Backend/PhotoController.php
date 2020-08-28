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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PhotoController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'IMAGE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_photo_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.photo';


    /**
     * {@inheritdoc}
     */
    public function saveItemAction(Request $request)
    {
        try {
            $this->checkSecurity($this->extension, $this->getActionPermission('save'));
            $files = $request->files->all();
            $file  = array_pop($files);
            $data  = $request->request->all();
            $item  = $this->get($this->service)->createItem($data, $file);

            return new JsonResponse($item, 201);
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('photo');
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        $years   = [];
        $conn    = $this->get('orm.manager')->getConnection('instance');
        $results = $conn->fetchAll(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%m')) as date_month FROM contents
            WHERE fk_content_type = 8 AND created IS NOT NULL ORDER BY date_month DESC"
        );

        foreach ($results as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt  = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            if (!is_null($fmt)) {
                $years[$date->format('Y')]['name']     = $date->format('Y');
                $years[$date->format('Y')]['months'][] = [
                    'name'  => ucfirst($fmt->format($date)),
                    'value' => $value['date_month']
                ];
            }
        }

        return array_merge(parent::getExtraData($items), [ 'years' => array_values($years) ]);
    }
}
