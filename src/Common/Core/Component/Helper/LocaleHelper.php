<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Common\Core\Component\Locale\Locale;
use Common\Core\Component\Security\Security;
use Common\ORM\Core\EntityManager;
use Common\ORM\Entity\Instance;
use Symfony\Component\HttpFoundation\RequestStack;

class LocaleHelper
{
    /**
     * The Entity Manager service.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The Locale service.
     *
     * @var Locale
     */
    protected $locale;

    /**
     * The RequestStack service.
     *
     * @var RequestStack
     */
    protected $rs;

    /**
     * The Sercurity service.
     *
     * @var Security
     */
    protected $security;

    /**
     * Initializes the LocaleHelper.
     *
     * @param Security $security The Security service.
     */
    public function __construct(
        EntityManager $em,
        ?Instance $instance,
        Locale $locale,
        RequestStack $rs,
        Security $security
    ) {
        $this->em       = $em;
        $this->instance = $instance;
        $this->locale   = $locale;
        $this->rs       = $rs;
        $this->security = $security;
    }

    /**
     * Returns the locale-related configuration for controllers.
     *
     * @return array The locale-related configuration.
     */
    public function getConfiguration()
    {
        return [
            'available'     => $this->locale->getAvailableLocales('frontend'),
            'default'       => $this->locale->getLocale('frontend'),
            'multilanguage' => $this->hasMultilanguage(),
            'selected'      => $this->getSelectedLocale(),
            'translators'   => $this->getTranslators($this->locale->getLocale('frontend'))
        ];
    }

    /**
     * Checks if multilanguage is enabled.
     *
     * @return boolean True if multilanguage is enabled. False otherwise.
     */
    public function hasMultilanguage()
    {
        return !empty($this->instance)
            ? $this->instance->hasMultilanguage()
            : false;
    }

    /**
     * Returns the selected locale for the current request.
     *
     * @return string The selected locale.
     */
    public function getSelectedLocale()
    {
        $request = $this->rs->getCurrentRequest();

        if (!empty($request) && $request->query->get('locale')) {
            return $request->query->get('locale');
        }

        return $this->locale->getLocale('frontend');
    }

    /**
     * Returns the list of available translators to translate the locale
     * provided as parameter.
     *
     * @param string $locale The locale to translate.
     *
     * @return array The list of translators.
     */
    public function getTranslators($locale)
    {
        if (!$this->security->hasExtension('es.openhost.module.translation')) {
            return [];
        }

        $translators = $this->em
            ->getDataSet('Settings', 'instance')
            ->get('translators');

        if (empty($translators)) {
            $translators = [];
        }

        $translators = array_filter($translators, function ($a) use ($locale) {
            return $a['from'] === $locale;
        });

        return array_values($translators);
    }
}
