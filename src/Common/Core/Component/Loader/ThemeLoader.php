<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Loader;

use Common\ORM\Core\EntityManager;
use Common\ORM\Entity\Theme;

class ThemeLoader
{
    /**
     * The current theme.
     *
     * @var Theme
     */
    protected $theme;

    /**
     * Initializes the InstanceLoader
     *
     * @param EntityManager $em The entity manager service.
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns the current loaded Theme.
     *
     * @return Theme The current loaded Theme.
     */
    public function getTheme() : ?Theme
    {
        return $this->theme;
    }

    /**
     * Loads a theme basing on the theme UUID.
     *
     * @param string $uuid The theme UUID.
     *
     * @return ThemeLoader The current ThemeLoader.
     */
    public function loadThemeByUuid(?string $uuid) : ThemeLoader
    {
        $uuid = 'es.openhost.theme.'
            . str_replace('es.openhost.theme.', '', $uuid);

        $oql = sprintf('uuid = "%s"', $uuid);

        $this->theme = $this->em->getRepository('theme', 'file')->findOneBy($oql);

        return $this;
    }

    /**
     * Returns the list of parents of the current theme.
     *
     * @param Theme $theme The theme to get parents from.
     *
     * @return array The list of parents.
     */
    protected function getParents(Theme $theme) : array
    {
        if (empty($theme)
            || empty($theme->parameters)
            || !array_key_exists('parent', $theme->parameters)
        ) {
            return [];
        }

        $uuids = $theme->parameters['parent'];
        $oql   = sprintf('uuid in ["%s"]', implode('", "', $uuids));

        $themes = $this->em->getRepository('theme', 'file')->findBy($oql);

        foreach ($themes as $t) {
            $parents[$t->uuid] = $t;
        }

        // Keep original order
        $parents = array_merge(array_flip($uuids), $parents);

        return array_values($parents);
    }
}
