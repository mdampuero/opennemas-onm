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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentV2Controller extends ContentController
{
    public function listAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse([
                'messages' => [
                    [
                        'id'      => '500',
                        'type'    => 'error',
                        'message' => sprintf(_('Access denied (%s)'), $roles)
                    ]
                ]
            ]);
        }

        $repository = $this->get('orm.manager')->getRepository('Content');
        $oql    = "content_type_name = '$contentType'";

        if (!empty($request->request->get('oql'))) {
            $oql .= 'and ' .  $request->request
                ->filter('oql', '', FILTER_SANITIZE_STRING);
        }

        $results = $repository->findBy($oql);
        $total   = $repository->countBy($oql);
        $extra   = $this->loadExtraData($results);

        foreach ($results as &$result) {
            // TODO: Remove when id replace pk_content in contents table
            $result->id = $result->pk_content;

            $result = $result->getData();
        }

        return new JsonResponse([
            'extra'   => $extra,
            'results' => array_values($results),
            'total'   => $total,
        ]);
    }

    public function patchAction(Request $request, $contentType, $id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');
        $oql = "content_type_name = '%s' and pk_content = %s";

        $entity = $em->getRepository('Content')
            ->findOneBy(sprintf($oql, $contentType, $id));

        $data = $em->getConverter('Content')
            ->objectify($request->request->all());

        $entity->setData($data);
        $em->persist($entity);

        $msg->add(_('Content saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function patchSelectedAction(Request $request, $contentType)
    {
        $em      = $this->get('orm.manager');
        $ids     = $request->request->get('selected');
        $msg     = $this->get('core.messenger');
        $oql     = "content_type_name = '%s' and pk_content in [%s]";
        $updated = 0;

        $entities = $em->getRepository('Content')
            ->findBy(sprintf($oql, $contentType, implode(',', $ids)));

        $data = $em->getConverter('Content')
            ->objectify($request->request->all());

        foreach ($entities as $entity) {
            try {
                $entity->setData($data);
                $em->persist($entity);
                $updated++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 400);
            }
        }

        if ($updated > 0) {
            $message = sprintf(_('%d items updated successfully'), $updated);
            $msg->add($message, 'success');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function showAction($contentType, $id)
    {
        $oql = "content_type_name = '%s' and pk_content = %s";

        $entity = $this->get('orm.manager')->getRepository('Content')
            ->findOneBy(sprintf($oql, $contentType, $id));

        return new JsonResponse($entity->getData());
    }
}
