<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Extension;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends Controller
{
    /**
     * Creates a new module from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function createAction(Request $request)
    {
        $module = new Extension();

        foreach ($request->request as $key => $value) {
            if (!is_null($value)) {
                $module->{$key} =
                    $request->request->filter($key, null, FILTER_SANITIZE_STRING);
            }
        }

        $module->created = date('Y-m-d H:i:s');
        $module->updated = date('Y-m-d H:i:s');

        $this->get('orm.manager')->persist($module);

        $response = new JsonResponse(_('Module saved successfully'), 201);

        // Add permanent URL for the current module
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
     * Deletes a module.
     *
     * @param integer $id The module id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $em = $this->get('orm.manager');
        $module = $em->getRepository('manager.module')->find($id);

        $em->remove($module);

        return new JsonResponse(_('Module deleted successfully.'));
    }

    /**
     * Deletes the selected instances.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find the instances for the given criteria'),
                404
            );
        }

        $em = $this->get('orm.manager');

        $criteria = [
            'id' => [
                [ 'value' => $selected, 'operator' => 'IN']
            ]
        ];

        $modules = $em->getRepository('manager.module')
            ->findBy($criteria);

        foreach ($modules as $module) {
            try {
                $em->remove($module);
                $updated++;
            } catch (EntityNotFoundException $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                    'type'    => 'error'
                ];
            } catch (\Exception $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error'
                ];
            }
        }

        if (count($updated) > 0) {
            $messages = [
                'message' => sprintf(_('%s modules deleted successfully.'), count($updated)),
                'type'    => 'success'
            ];
        }

        // Return the proper status code
        if (count($error) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages ],
            $statusCode
        );
    }

    /**
     * Returns the list of instances as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        if (!empty($criteria)) {
            $criteria['union'] = 'OR';
        }

        $nr = $this->get('orm.manager')->getRepository('manager.extension');

        $modules = $nr->findBy($criteria, $order, $epp, $page);

        $ids = [];
        foreach ($modules as &$module) {
            $ids[] = $module->instance_id;

            $module = $module->getData();
        }

        $ids       = array_unique(array_diff($ids, [ -1, 0 ]));
        $instances = [];
        if (!empty($ids)) {
            $instances = $this->get('instance_manager')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
            ]);
        }

        $extra['instances'] = [
            '-1' => [ 'name' => _('Manager'), 'value' => -1 ],
            '0'  => [ 'name' => _('All'), 'value' => 0 ]
        ];

        foreach ($instances as $instance) {
            $extra['instances'][$instance->id] = [
                'name'  => $instance->internal_name,
                'value' => $instance->id,
            ];
        }

        $total = $nr->countBy($criteria);

        return new JsonResponse([
            'epp'     => $epp,
            'extra'   => $extra,
            'page'    => $page,
            'results' => $modules,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the data to create a new module.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        $extra = $this->getTemplateParams();

        unset($extra['types']['-1']);
        unset($extra['styles']['-1']);

        return new JsonResponse([ 'extra' => $extra ]);
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction(Request $request, $id)
    {
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
    }

    /**
     * Returns an instance as JSON.
     *
     * @param integer  $id The instance id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        try {
            $module = $this->get('orm.manager')
                ->getRepository('manager.extension')
                ->find($id);

            $extra = $this->getTemplateParams();

            unset($extra['types']['-1']);

            return new JsonResponse([
                'extra'        => $extra,
                'module' => $module->getData()
            ]);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the entity with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     */
    public function updateAction(Request $request, $id)
    {
        try {
            $em = $this->get('orm.manager');
            $module = $em ->getRepository('manager.extension')
                ->find($id);

            $keys = array_unique(array_merge(
                array_keys($request->request->all()),
                array_keys($module->getData())
            ));

            foreach ($keys as $key) {
                if ($request->request->get($key)
                    && !is_null($request->request->get($key))
                ) {
                    $module->{$key} =
                        $request->request->filter($key, null, FILTER_SANITIZE_STRING);
                } else {
                    $module->{$key} = null;
                }
            }

            $module->updated = date('Y-m-d H:i:s');

            $em->persist($module);

            return new JsonResponse(_('Module saved successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getTemplateParams()
    {
        $params = [
            'styles' => [
                '-1'      => [ 'name' => _('All'), 'value' => '-1' ],
                'error'   => [ 'name' => _('Error'), 'value' => 'error' ],
                'info'    => [ 'name' => _('Information'), 'value' => 'info' ],
                'success' => [ 'name' => _('Success'), 'value' => 'success' ],
                'warning' => [ 'name' => _('Warning'), 'value' => 'warning' ]
            ],
            'types' => [
                '-1'      => [ 'name' => _('All'), 'value' => '-1' ],
                'comment' => [ 'name' => _('Comments'), 'value' => 'comment' ],
                'email'   => [ 'name' => _('Email'), 'value' => 'email' ],
                'help'    => [ 'name' => _('Help'), 'value' => 'help' ],
                'info'    => [ 'name' => _('Information'), 'value' => 'info' ],
                'media'   => [ 'name' => _('Media'), 'value' => 'media' ],
                'user'    => [ 'name' => _('Users'), 'value' => 'user' ]
            ]
        ];

        $instances = $this->get('instance_manager')->findBy([]);

        $params['instances'] = [
            '-1' => [ 'name' => 'Manager', 'value' => '-1' ],
            '0'  => [ 'name' => _('All'), 'value' => '0' ]
        ];

        foreach ($instances as $instance) {
            $params['instances'][$instance->id] = [
                'name'  => $instance->internal_name,
                'value' => $instance->id
            ];
        }

        return $params;
    }
}
