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

use Common\Model\Entity\Theme;
use Symfony\Component\Finder\Finder;

class WidgetLoader
{
    /**
     * The current theme.
     *
     * @var Theme
     */
    protected $theme;

    /**
     * The list of available widgets where key is the widget name and value is
     * the path to PHP file with the logic of the widget.
     *
     * @var array
     */
    protected $widgets = [];

    /**
     * Sets the current theme.
     *
     * @param Theme $theme The current theme.
     *
     * @return WidgetLoader The current WidgetLoader.
     */
    public function addTheme(Theme $theme) : WidgetLoader
    {
        $widgets = $theme->isMultiRepo()
            ? $this->getWidgetsFromConfig($theme)
            : $this->getWidgetsFromPath($theme->realpath . '/tpl/widgets');

        $this->widgets = array_merge($this->widgets, $widgets);

        ksort($this->widgets);

        return $this;
    }

    /**
     * Returns the list of available widgets.
     *
     * @return array The list of available widgets.
     */
    public function getWidgets() : array
    {
        return array_keys($this->widgets);
    }

    /**
     * Loads a widget basing on the widget name..
     *
     * @param string $name The widget name.
     *
     * @return WidgetLoader The current WidgetLoader.
     *
     * @codeCoverageIgnore
     */
    public function loadWidget(string $name) : WidgetLoader
    {
        if (array_key_exists($name, $this->widgets)) {
            include_once $this->widgets[$name];
        }

        return $this;
    }

    /**
     * Returns the widget name basing on the template filename.
     *
     * @param string $template The template filename.
     *
     * @return string The Widget name.
     */
    protected function getWidgetName(string $template) : string
    {
        $name = preg_replace('/(.class)?\.(php|tpl)/', '', $template);
        $name = preg_replace('/[wW]idget/', '', $name);
        $name = ucfirst(preg_replace_callback('/_([a-z])/', function ($matches) {
            return ucfirst($matches[1]);
        }, $name));

        return $name;
    }

    /**
     * Returns the list of widgets defined in theme configuration file.
     *
     * @param Theme $theme The theme to get widgets from.
     *
     * @return array The list of widgets.
     */
    protected function getWidgetsFromConfig(Theme $theme) : array
    {
        if (array_key_exists('widgets', $theme->parameters)) {
            return $theme->parameters['widgets'];
        }

        return [];
    }

    /**
     * Returns the list of widgets found in theme source files.
     *
     * @return array The list of widgets.
     *
     * @codeCoverageIgnore
     */
    protected function getWidgetsFromPath(string $path) : array
    {
        $widgets = [];
        $finder  = new Finder();

        if (!is_dir($path)) {
            return $widgets;
        }

        $files = $finder
            ->followLinks()
            ->files()
            ->in($path)
            ->name('/[Ww]idget.*\.php/');

        foreach ($files as $file) {
            $widgets[$this->getWidgetName($file->getFileName())] =
                $file->getRealPath();
        }

        return $widgets;
    }
}
