<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\Extension;
use Common\Core\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns, saves, modifies and removes extensions.
 */
class ModuleController extends Controller
{
    /**
     * Returns a list of suggestions basing on the query.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function autocompleteAction(Request $request)
    {
        $uuid = $request->query->get('uuid');
        $oql  = 'limit 10';

        if (!empty($uuid)) {
            $oql  = sprintf('uuid ~ "%s" ', $uuid) . $oql;
        }

        $modules = $this->get('orm.manager')->getRepository('Extension')
            ->findBy($oql);

        $modules = array_map(function ($a) {
            return $a->uuid;
        }, $modules);

        return new JsonResponse([ 'extensions' => $modules ]);
    }


    /**
     * Checks if the given UUID is available.
     *
     * @param Request $request The request object.
     * @param string  $uuid    The UUID to check.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_CREATE')
     *     || hasPermission('EXTENSION_UPDATE')")
     */
    public function checkAction(Request $request, $uuid)
    {
        $msg = $this->get('core.messenger');
        $oql = sprintf('uuid = "%s"', $uuid);
        $id  = $request->query->get('id');

        try {
            $module = $this->get('orm.manager')
                ->getRepository('Extension')
                ->findOneBy($oql);

            if ($module->id !== (int) $id) {
                $text = _('A module with the uuid \'%s\' already exists');
                $msg->add(sprintf($text, $uuid), 'error', 409);
            }
        } catch (\Exception $e) {
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes a module.
     *
     * @param integer $id The module id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $module = $em->getRepository('Extension')->find($id);

        $em->remove($module);
        $msg->add(_('Module deleted successfully.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected modules.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $modules = $em->getRepository('Extension')->findBy($oql);

        $deleted = 0;
        foreach ($modules as $module) {
            try {
                $em->remove($module);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s modules deleted successfully.'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of modules.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql   = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Extension');
        $converter  = $this->get('orm.manager')->getConverter('Extension');

        $extensions = $repository->findBy($oql);
        $total      = $repository->countBy($oql);

        $extensions = array_map(function ($a) use ($converter) {
            return $converter->responsify($a->getData());
        }, $extensions);

        return new JsonResponse([
            'extra'   => $this->getExtraData(),
            'results' => $extensions,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the data to create a new module.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_CREATE')")
     */
    public function newAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');
        $data = $em->getConverter('Extension')
            ->objectify($request->request->all());

        $module = $em->getRepository('Extension')->find($id);
        $module->merge($data);

        $em->persist($module);

        $msg->add(_('Module saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('EXTENSION_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em   = $this->get('orm.manager');
        $oql  = sprintf('id in [%s]', implode(',', $ids));
        $data = $em->getConverter('Extension')->objectify($params);

        $modules = $em->getRepository('Extension')->findBy($oql);

        $updated = 0;
        foreach ($modules as $module) {
            try {
                $module->merge($data);
                $em->persist($module);
                $updated++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 409);
            }
        }

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s modules saved successfully'), $updated),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Saves a new module.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('EXTENSION_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $params = [];
        foreach ($request->request->all() as $key => $value) {
            $params[$key] = $value;

            // Decode JSON strings
            if ($key !== 'images') {
                $params[$key] = json_decode($value, true);
            }
        }

        $msg  = $this->get('core.messenger');
        $em   = $this->get('orm.manager');
        $data = $em->getConverter('Extension')
            ->objectify($params);

        $module = new Extension($data);

        $module->created = new \DateTime('now');
        $module->updated = new \DateTime('now');

        $em->persist($module);

        if (!empty($request->files->count())) {
            $module->images = [];

            $fs = new Filesystem();
            if (!$fs->exists(SITE_PATH . 'media/core/modules')) {
                $fs->mkdir(SITE_PATH . 'media/core/modules');
            }

            $i = 1;
            foreach ($request->files as $file) {
                $filename = $module->id . '_' . $i++ . '.'
                    . $file[0]->getClientOriginalExtension();

                $module->images[] = '/media/core/modules/' . $filename;
                $file[0]->move(SITE_PATH . '/media/core/modules', $filename);
            }

            $em->persist($module);
        }

        $msg->add(_('Module saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_module_show',
                [ 'id' => $module->id ]
            )
        );

        return $response;
    }

    /**
     * Returns a module.
     *
     * @param integer $id The module id.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('EXTENSION_UPDATE')")
     */
    public function showAction($id)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Extension');
        $module    = $em->getRepository('Extension')->find($id);

        // Convert prices to float
        if (!empty($module->price)) {
            foreach ($module->price as &$price) {
                $price['value'] = (float) $price['value'];
            }
        }

        return new JsonResponse([
            'extra'  => $this->getExtraData(),
            'module' => $converter->responsify($module->getData())
        ]);
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     *
     * @Security("hasPermission('EXTENSION_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $params = [];
        foreach ($request->request->all() as $key => $value) {
            if ($key !== '_method') {
                $params[$key] = $value;

                // Decode JSON strings
                if ($key !== 'images') {
                    $params[$key] = json_decode($value, true);
                }
            }
        }

        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Extension')->objectify($params);

        $module   = $em ->getRepository('Extension')->find($id);
        $path     = $this->getParameter('paths.extensions_assets_path') . DS;
        $toDelete = empty($module->images) ? [] : $module->images;

        $module->setData($data);
        $module->updated = new \DateTime('now');

        if ($request->files->count() > 0) {
            $module->images = [];

            $fs = new Filesystem();
            if (!$fs->exists(SITE_PATH . $path)) {
                $fs->mkdir(SITE_PATH . $path);
            }

            $i = 1;
            foreach ($request->files as $file) {
                $filename = $module->id . '_' . $i++ . '.'
                    . $file[0]->getClientOriginalExtension();

                $module->images[] = $path . $filename;
                $file[0]->move(SITE_PATH . $path, $filename);
            }

            if (!empty($module->images)) {
                $toDelete = array_diff($toDelete, $module->images);
            }

            foreach ($toDelete as $image) {
                $fs->remove(SITE_PATH . $image);
            }
        }

        $em->persist($module);

        $msg->add(_('Module saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @return array The extra data.
     */
    private function getExtraData()
    {
        $params = [
            'languages' => [
                'en' => _('English'),
                'es' => _('Spanish'),
                'gl' => _('Galician'),
            ]
        ];

        return $params;
    }
}
