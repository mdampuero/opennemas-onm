<?php

namespace ManagerWebService\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityController extends Controller
{
    private const TWO_FACTOR_SETTINGS_KEY = 'two_factor_enabled';

    public function twoFactorAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition($condition)->getOql();
        }

        $repository = $this->get('orm.manager')->getRepository('Instance');
        $converter  = $this->get('orm.manager')->getConverter('Instance');

        $instances = $repository->findBy($oql);
        $total     = $repository->countBy($oql);

        $results = array_map(function ($instance) use ($converter) {
            $data = $converter->responsify($instance->getData());
            $data['two_factor_enabled'] = $this->isTwoFactorEnabled($instance);

            return $data;
        }, $instances);

        return new JsonResponse([
            'total'   => $total,
            'results' => $results,
            'extra'   => [],
            'oql'     => $oql,
        ]);
    }

    public function twoFactorSaveAction(Request $request)
    {
        $params = $request->query->all();
        $msg    = $this->get('core.messenger');

        $id = (int) ($params['id'] ?? 0);

        if ($id <= 0) {
            $msg->add(_('Invalid instance identifier'), 'error', 400);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $enabled = filter_var($params['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $enabled = (bool) ($enabled ?? false);

        $em       = $this->get('orm.manager');
        $instance = $em->getRepository('Instance')->find($id);

        if (!$instance) {
            $msg->add(_('Instance not found'), 'error', 404);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
            throw new AccessDeniedException();
        }

        $this->persistTwoFactorSetting($em, $instance, $enabled);

        $this->get('core.dispatcher')
            ->dispatch('instance.update', [ 'instance' => $instance ]);

        if ($enabled) {
            $msg->add(_('Two-factor authentication enabled successfully'), 'success');
        } else {
            $msg->add(_('Two-factor authentication disabled successfully'), 'success');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    public function twoFactorDeleteSessionAction(Request $request)
    {
        $msg    = $this->get('core.messenger');
        $params = array_merge($request->request->all(), $request->query->all());

        $ids = $params['ids'] ?? [];

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $ids = array_values(array_filter(array_map('intval', $ids)));

        if (empty($ids)) {
            $msg->add(_('Invalid instance identifiers'), 'error', 400);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if (count($ids) > 1) {
            $msg->add(_('Two-factor sessions deleted successfully'), 'success');
        } else {
            $msg->add(_('Two-factor session deleted successfully'), 'success');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    private function persistTwoFactorSetting($em, $instance, bool $enabled): void
    {
        $settings = $instance->settings;

        if (!is_array($settings)) {
            $settings = [];
        }

        $settings[self::TWO_FACTOR_SETTINGS_KEY] = $enabled;

        $instance->merge(['settings' => $settings]);

        $em->persist($instance);
    }

    private function toBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower((string) $value);

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function isTwoFactorEnabled($instance): bool
    {
        $settings = $instance->settings;

        if (!is_array($settings)
            || !array_key_exists(self::TWO_FACTOR_SETTINGS_KEY, $settings)
        ) {
            return false;
        }

        return $this->toBoolean($settings[self::TWO_FACTOR_SETTINGS_KEY]);
    }
}
