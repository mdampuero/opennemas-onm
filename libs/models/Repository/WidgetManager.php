<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Repository;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;
use Symfony\Component\Finder\Finder;

/**
 * An EntityRepository serves as a repository for entities with generic as well
 * as business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate
 * entities.
 *
 * @package Repository
 */
class WidgetManager extends EntityManager
{
    /**
     * The array of widget paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Searches for widgets given a criteria
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The current page.
     * @param  integer      $offset          The offset to start with.
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0, &$count = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);
        $orderBySQL = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents`, `widgets`
            WHERE $filterSQL AND pk_content=pk_widget
            ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);

        $contentIdentifiers = []
        foreach ($rs as $resultElement) {
            $contentIdentifiers[] = [$resultElement['content_type_name'], $resultElement['pk_content']];
        }

        $contents = $this->findMulti($contentIdentifiers);

        return $contents;
    }

    /**
     * Counts widgets given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return array                  The number of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content) FROM `contents`, `widgets`"
            . " WHERE $filterSQL AND pk_content=pk_widget";
        $rs  = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Adds a path to the list of paths.
     *
     * @param string $path The path to add.
     */
    public function addPath($path)
    {
        $this->paths[] = $path;
    }

    /**
     * Returns the list of all available widgets.
     *
     * @return array The list of all intelligent widgets.
     */
    public function getWidgets()
    {
        $finder  = new Finder();
        $widgets = [];

        foreach ($this->paths as $path) {
            if (is_dir($path)) {
                $files = $finder->followLinks()->files()->in($path)
                    ->name('/[Ww]idget.*(\.class)?[^\.form]\.(php|tpl)/');

                foreach ($files as $file) {
                    $widgets[] = $this->parseWidgetName($file->getFileName());
                }
            }
        }

        $widgets = array_unique($widgets);
        sort($widgets);

        return $widgets;
    }

    /**
     * Loads a widget given its name.
     *
     * @param string $widgetName The widget name.
     */
    public function loadWidget($widgetName)
    {
        $widgetName = 'Widget' . str_replace('Widget', '', $widgetName);
        $filename   = \underscore($widgetName);

        foreach ($this->paths as $path) {
            if (file_exists($path . DS . $filename . '.class.php')) {
                include_once $path . DS . $filename . '.class.php';
                return;
            }

            if (file_exists($path . DS . $widgetName . '.php')) {
                include_once $path . DS . $widgetName . '.php';
                return;
            }
        }
    }

    /**
     * Parses and returns the widget name.
     *
     * @param string $name The widget name.
     *
     * @return string The parsed widget name.
     */
    protected function parseWidgetName($name)
    {
        $name = preg_replace('/(.class)?\.(php|tpl)/', '', $name);
        $name = preg_replace('/[wW]idget/', '', $name);
        $name = ucfirst(preg_replace_callback('/_([a-z])/', function ($matches) {
            return ucfirst($matches[1]);
        }, $name));

        return $name;
    }
}
