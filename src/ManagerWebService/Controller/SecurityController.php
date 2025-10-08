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
        $twoFactorFilter = $request->query->get('two_factor_enabled');

        $oqlFixer = $this->get('orm.oql.fixer')->fix($oql);

        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oqlFixer->addCondition($condition);
        }

        if ($twoFactorFilter !== null && $twoFactorFilter !== '') {
            $truthyPatterns = $this->getSerializedBooleanPatterns(true);

            if ($this->toBoolean($twoFactorFilter)) {
                $oqlFixer->addCondition($this->buildSerializedPresenceCondition($truthyPatterns));
            } elseif ($this->isFalseLike($twoFactorFilter)) {
                $absenceCondition = $this->buildSerializedAbsenceCondition($truthyPatterns);
                $falsyPatterns    = $this->getSerializedBooleanPatterns(false);
                $explicitFalse    = $this->buildSerializedPresenceCondition($falsyPatterns);

                $conditions = array_filter([
                    'settings is null',
                    $explicitFalse,
                    $absenceCondition,
                ]);

                $oqlFixer->addCondition('(' . implode(' or ', $conditions) . ')');
            }
        }

        $oql = $oqlFixer->getOql();

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

        $em            = $this->get('orm.manager');
        $repository    = $em->getRepository('Instance');
        $sessionRedis  = $this->get('api.service.redis_session');
        $instances     = [];

        foreach ($ids as $id) {
            $instance = $repository->find($id);

            if (!$instance) {
                $msg->add(_('Instance not found'), 'error', 404);

                return new JsonResponse($msg->getMessages(), $msg->getCode());
            }

            if (!$this->get('core.security')->hasInstance($instance->internal_name)) {
                throw new AccessDeniedException();
            }

            $instances[] = $instance;
        }

        try {
            foreach ($instances as $instance) {
                $pattern = sprintf('PHPREDIS_SESSION:__%s__*', $instance->internal_name);

                $sessionRedis->deleteByPattern($pattern);
            }
        } catch (\Exception $exception) {
            $msg->add(_('There was an error deleting the two-factor sessions'), 'error', 500);

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

    private function isFalseLike($value): bool
    {
        if (is_bool($value)) {
            return !$value;
        }

        $value = strtolower((string) $value);

        return in_array($value, ['0', 'false', 'no', 'off'], true);
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

    /**
     * @param bool $value
     *
     * @return string[]
     */
    private function getSerializedBooleanPatterns(bool $value): array
    {
        $keyLength    = strlen(self::TWO_FACTOR_SETTINGS_KEY);
        $serializedKey = sprintf('s:%d:"%s";', $keyLength, self::TWO_FACTOR_SETTINGS_KEY);

        if ($value) {
            return [
                $serializedKey . 'b:1;',
                $serializedKey . 'i:1;',
                $serializedKey . 's:1:"1";',
                sprintf('\\"%s\\":true', self::TWO_FACTOR_SETTINGS_KEY),
                sprintf('\\"%s\\":1', self::TWO_FACTOR_SETTINGS_KEY),
            ];
        }

        return [
            $serializedKey . 'b:0;',
            $serializedKey . 'i:0;',
            $serializedKey . 's:1:"0";',
            sprintf('\\"%s\\":false', self::TWO_FACTOR_SETTINGS_KEY),
            sprintf('\\"%s\\":0', self::TWO_FACTOR_SETTINGS_KEY),
        ];
    }

    /**
     * @param string[] $patterns
     */
    private function buildSerializedPresenceCondition(array $patterns): string
    {
        $patterns = array_filter($patterns);

        if (empty($patterns)) {
            return '';
        }

        $conditions = array_map(function ($pattern) {
            return sprintf('settings ~ "%s"', $this->escapeOqlPattern($pattern));
        }, $patterns);

        if (count($conditions) === 1) {
            return $conditions[0];
        }

        return '(' . implode(' or ', $conditions) . ')';
    }

    /**
     * @param string[] $patterns
     */
    private function buildSerializedAbsenceCondition(array $patterns): string
    {
        $patterns = array_filter($patterns);

        if (empty($patterns)) {
            return '';
        }

        $conditions = array_map(function ($pattern) {
            return sprintf('settings !~ "%s"', $this->escapeOqlPattern($pattern));
        }, $patterns);

        if (count($conditions) === 1) {
            return $conditions[0];
        }

        return '(' . implode(' and ', $conditions) . ')';
    }

    private function escapeOqlPattern(string $pattern): string
    {
        return addcslashes($pattern, "\\\"");
    }
}
