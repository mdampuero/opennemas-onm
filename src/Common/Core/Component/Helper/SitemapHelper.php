<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Instance;
use Opennemas\Orm\Core\Connection;
use Opennemas\Orm\Core\EntityManager;
use Symfony\Component\Finder\Finder;

/**
 * Helper class to retrieve sitemaps data.
 */
class SitemapHelper
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * The finder component.
     *
     * @var Finder
     */
    protected $finder;

    /**
     * The path to the public directory.
     *
     * @var string
     */
    protected $publicDir;


    public function __construct(
        Instance $instance,
        EntityManager $entityManager,
        Connection $connection,
        string $publicDir
    ) {
        $this->instance      = $instance;
        $this->entityManager = $entityManager;
        $this->connection    = $connection;
        $this->finder        = new Finder();
        $this->publicDir     = $publicDir;
    }

    /**
     * Returns the settings of the sitemap.
     *
     * @return array The sitemap settings.
     */
    public function getSettings()
    {
        return $this->entityManager->getDataSet('Settings', 'instance')->get('sitemap')
            ?? $this->entityManager->getDataSet('Settings', 'manager')->get('sitemap');
    }

    /**
     * Returns the dates range for the specified content_type_names.
     *
     * @param string $types A comma separated string with content_type_names.
     *
     * @return array $dates The array of dates.
     */
    public function getDates($types)
    {
        if (empty($types)) {
            return [];
        }

        $result = $this->connection->fetchAll(
            sprintf(
                'SELECT CONCAT(CONVERT(year(changed), NCHAR),\'-\', LPAD(month(changed),2,"0")) as \'dates\''
                . 'FROM `contents` WHERE year(changed) is not null '
                . 'AND `content_type_name` IN (%s) '
                . 'group by dates order by dates',
                $types
            )
        );

        return array_map(function ($a) {
            return $a['dates'];
        }, $result);
    }

    /**
     * Returns the lastmod date of the sitemap or the current date if doesn't exists.
     *
     * @param string $filename The name of the file.
     *
     * @return string The creation date of the current sitemap.
     */
    public function getSitemapDate($filename)
    {
        $path = $this->publicDir .
            '/' . $this->instance->getSitemapShortPath() .
            '/' . $filename;

        if (file_exists($path)) {
            return date("Y-m-d H:i:s", filemtime($path));
        }

        return date("Y-m-d H:i:s");
    }
}
