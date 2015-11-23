<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Loader;

use Framework\ORM\Entity\Theme;
use Framework\ORM\Validator\ThemeValidator;

class ThemeLoader
{
    /**
     * The theme validator.
     *
     * @var ThemeValidator
     */
    protected $validator;

    /**
     * Initializes the theme loader
     *
     * @param ThemeValidator $validator The theme validator.
     */
    public function __construct(ThemeValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Loads a theme from a configuration.
     *
     * @param array $config The theme configuration.
     *
     * @return Theme The loaded theme.
     *
     * @throws InvalidThemeException If the theme configuration is not valid.
     */
    public function load($config)
    {
        $theme = new Theme($config);

        $this->validator->validate($theme);

        return $theme;
    }
}
