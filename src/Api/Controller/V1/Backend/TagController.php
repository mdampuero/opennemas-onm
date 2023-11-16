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
use League\Csv\Writer;
use Common\Core\Component\Validator\Validator;
use Common\Model\Entity\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lists and displays tags.
 */
class TagController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.tags';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_tag_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'TAG_CREATE',
        'delete' => 'TAG_DELETE',
        'list'   => 'TAG_ADMIN',
        'patch'  => 'TAG_UPDATE',
        'save'   => 'TAG_CREATE',
        'show'   => 'TAG_UPDATE',
        'update' => 'TAG_UPDATE',
        'move'   => 'TAG_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.tag';

    /**
     * Get the tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get([ 'tags_maxItems', 'tags_maxResults' ]);

        $extra = [ 'blacklist_tag' => $this->get('core.validator')->getConfig(Validator::BLACKLIST_RULESET_TAGS) ];

        if (!empty($extra['blacklist_tag'])) {
            $settings = array_merge($settings, $extra);
        }

        if (array_key_exists('tags_maxItems', $settings)) {
            $settings['tags_maxItems'] = (int) $settings['tags_maxItems'];
        }

        if (array_key_exists('tags_maxResults', $settings)) {
            $settings['tags_maxResults'] = (int) $settings['tags_maxResults'];
        }

        return new JsonResponse($settings);
    }

        /**
     * Downloads the list of tags.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function getReportAction()
    {
        // Get information

        $tagService = $this->get($this->service);
        $tags       = $tagService->getList();
        $extraData  = $this->getExtraData($tags['items']);
        $tags       = $tagService->responsify($tags['items']);

        // Prepare contents for CSV
        $headers = [
            _('Name'),
            _('Slug'),
            _('Description'),
            _('Contents')
        ];

        if ($this->container->get('core.instance')->hasMultilanguage()) {
            $headers[] = _('Locale');
        }

        $data = [];
        foreach ($tags as $tag) {
            $tagInfo = [
                $tag['name'],
                $tag['slug'],
                $tag['description'],
                $extraData['stats'][$tag['id']] ?? 0
            ];

            if ($this->container->get('core.instance')->hasMultilanguage()) {
                $tagInfo[] = $tag['locale'] ?? '';
            }

            $data[] = $tagInfo;
        }

        // Prepare the CSV content
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $writer->insertOne($headers);
        $writer->insertAll($data);

        $response = new Response($writer, 200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Tags list Export');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=tags-' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Moves all contents assigned to the tag to the target tag.
     *
     * @param Request $request The request object.
     * @param integer $id      The tag id.
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
     * Saves configuration for tags.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $settings = $request->request->all();

        if (!array_key_exists('blacklist_tag', $settings)
            || empty($settings['blacklist_tag'])
        ) {
            $settings['blacklist_tag'] = null;
        }

        $msg = $this->get('core.messenger');

        try {
            $this->get('core.validator')->setConfig(Validator::BLACKLIST_RULESET_TAGS, $settings['blacklist_tag']);
            $this->get('orm.manager')->getDataSet('Settings', 'instance')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Checks if the information in the request is valid to create a new Tag.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function validateItemAction(Request $request)
    {
        $msg  = $this->get('core.messenger');
        $data = $request->query->all();

        $data['slug'] = $this->get('data.manager.filter')
            ->set($data['name'])
            ->filter('slug')
            ->get();

        $item = new Tag($data);

        try {
            $this->get('api.validator.tag')->validate($item);
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = [])
    {
        $extraData = [
            'stats'   => $this->get('api.service.tag')->getStats($items),
            'locale'  => $this->get('core.helper.locale')->getConfiguration(),
        ];

        return $extraData;
    }
}
