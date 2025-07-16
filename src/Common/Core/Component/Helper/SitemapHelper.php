<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Instance;
use Opennemas\Orm\Core\Connection;
use Opennemas\Orm\Core\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Helper class to retrieve sitemaps data.
 */
class SitemapHelper
{
    /**
     * The list of needed extensions per action.
     *
     * @const array
     */
    const EXTENSIONS = [
        'album'    => 'ALBUM_MANAGER',
        'article'  => 'ARTICLE_MANAGER',
        'company'  => 'es.openhost.module.companies',
        'event'    => 'es.openhost.module.events',
        'kiosko'   => 'KIOSKO_MANAGER',
        'letter'   => 'LETTER_MANAGER',
        'obituary' => 'es.openhost.module.obituaries',
        'opinion'  => 'OPINION_MANAGER',
        'poll'     => 'POLL_MANAGER',
        'tag'      => '',
        'video'    => 'VIDEO_MANAGER',
    ];

    /**
     * The service container.
     *
     * @var Container
     */
    protected $container;

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
     * The filesystem component.
     *
     * @var FileSystem
     */
    protected $fs;

    /**
     * The path to the public directory.
     *
     * @var string
     */
    protected $publicDir;

    /**
     * The configuration of the sitemap.
     *
     * @var array
     */
    protected $settings;

    /**
     * Initializes the sitemap helper
     *
     * @param Instance           $contentHelper The content helper.
     * @param EntityManager      $entityManager The current instance.
     * @param Connection         $connection    The database connection.
     * @param string             $publicDir     The path to the public directory.
     */
    public function __construct(
        Container $container,
        Instance $instance,
        EntityManager $entityManager,
        Connection $connection,
        string $publicDir
    ) {
        $this->connection    = $connection;
        $this->container     = $container;
        $this->entityManager = $entityManager;
        $this->finder        = new Finder();
        $this->fs            = new Filesystem();
        $this->instance      = $instance;
        $this->publicDir     = $publicDir;
        $this->settings      = $this->getSettings();
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
     * @return array $dates The array of dates.
     */
    public function getDates()
    {
        $types = $this->getTypes($this->settings, [ 'tag' ], true);

        if (empty($types)) {
            return [];
        }

        $result = $this->connection->fetchAll(
            sprintf(
                'SELECT DISTINCT year(changed) as "year", month(changed) as "month" '
                . 'FROM `contents` WHERE changed is not null '
                . 'AND changed <= CURRENT_TIMESTAMP() '
                . 'AND `content_type_name` IN (%s) '
                . 'order by year,month',
                $types
            )
        );

        return array_map(function ($a) {
            return $a['year'] . "-" . str_pad($a['month'], 2, '0', STR_PAD_LEFT);
        }, $result);
    }

    /**
     * Returns the sitemaps info for the selectors.
     *
     * @return array The array with the sitemaps info.
     */
    public function getSitemapsInfo()
    {
        $types = $this->getTypes($this->settings, [ 'tag' ], true);
        $years = !empty($types) ? $this->getYears($types) : [];

        return [
            'items'    => $this->getSitemaps(),
            'years'    => $years,
        ];
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

        $date = @filemtime($path);

        return !empty($date) ? date("Y-m-d H:i:s", $date) : $date;
    }

    /**
     * Returns an array with all the sitemaps saved on disk.
     *
     * @return array The array with the name of the sitemaps.
     */
    public function getSitemaps($sitemapPath = null)
    {
        $sitemapPath = $sitemapPath ?? $this->instance->getSitemapShortPath();
        $path        = $this->publicDir . '/' . $sitemapPath . '/';
        $files       = [];

        try {
            $this->finder->files()->in($path);
        } catch (\Exception $e) {
            return $files;
        }

        if (!$this->finder->hasResults()) {
            return $files;
        }

        foreach ($this->finder as $file) {
            $files[] = $file->getRelativePathName();
        }

        return $files;
    }

    /**
     * Method for recover get types
     *
     * @param array $settings The sitemap settings
     * @param array $ommit    An array of types to ommit.
     *
     * @return Array The list of types
     */
    public function getTypes($settings, $ommit = [], $asString = false)
    {
        $types = array_keys(array_filter(self::EXTENSIONS, function ($value, $key) use ($settings, $ommit) {
            return !in_array($key, $ommit)
                && array_key_exists($key, $settings)
                && !empty($settings[$key])
                && ($this->container->get('core.security')->hasExtension($value) || empty($value));
        }, ARRAY_FILTER_USE_BOTH));

        if (!$asString) {
            return $types;
        }

        return implode(',', array_map(function ($a) {
            return '"' . $a . '"';
        }, $types));
    }

    /**
     * Returns the contents for an specific month.
     *
     * @param string  $date    The date of the contents.
     * @param integer $perpage The numnber of items per page.
     * @param array   $types   The types of the contents to filter.
     *
     * @return mixed $items The elements or the number of elements depending on the number.
     */
    public function getContents($date, $types, $perpage = null, $page = 1)
    {
        $em = $this->container->get('entity_repository');

        $filters = [
            'content_type_name' => [[ 'value' => $types, 'operator' => 'IN' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'params'            => [
                'union' => 'OR',
                [ 'value' => '%bodyLink%', 'operator' => 'NOT LIKE' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ],
            'starttime'         => [
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'changed ' => [
                [
                    'value' => sprintf(
                        '"%s" AND DATE_ADD("%s", INTERVAL 1 MONTH)',
                        date('Y-m-01 00:00:00', strtotime($date)),
                        date('Y-m-01 00:00:00', strtotime($date))
                    ),
                    'field' => true,
                    'operator' => 'BETWEEN'
                ]
            ],
        ];

        if (empty($perpage)) {
            return $em->countBy($filters);
        }

        $contents = $em->findBy($filters, ['changed' => 'asc'], $perpage, $page);

        if (empty($contents)) {
            throw new NotFoundHttpException();
        }

        return $contents;
    }

    /**
     * Returns the list of letters with tags in the instance.
     *
     * @return array The array of tags.
     */
    public function getTags()
    {
        $letters = $this->connection->fetchAll(
            'SELECT DISTINCT SUBSTR(CAST(CONVERT(slug USING utf8) as binary),1,1) as "letter"' .
            'FROM `tags` WHERE `slug` IS NOT NULL'
        );

        return array_filter($letters, function ($a) {
            return ctype_graph($a['letter']);
        });
    }

    /**
     * Remove the indicated sitemaps.
     *
     * @param integer $year  The year of the sitemaps.
     * @param integer $month The month of the sitemaps.
     * @param integer $page  The page of the sitemap.
     *
     * @return array An array of the removed sitemaps.
     */
    public function deleteSitemaps($parameters = [], $sitemapPath = null)
    {
        $sitemapPath = $sitemapPath ?? $this->instance->getSitemapShortPath();
        $removed     = [];
        $path        = $this->publicDir . '/' . $sitemapPath . '/';
        $sitemaps    = $this->getSitemaps($sitemapPath);

        foreach ($sitemaps as $sitemap) {
            $toRemove = true;
            $params   = array_slice(explode('.', $sitemap), 1, 3);

            $item['year']  = $params[0];
            $item['month'] = $params[1];
            $item['page']  = $params[2];

            foreach ($parameters as $key => $value) {
                if (!empty($value) && $item[$key] !== $value) {
                    $toRemove = false;
                    continue;
                }
            }

            if ($toRemove && unlink($path . $sitemap)) {
                $removed[] = $sitemap;
            }
        }

        return $removed;
    }

    /**
     * Remove sitemaps by patterm
     *
     * @param string $year  The year to match.
     * @param string $month The month to match.
     *
     */
    public function removeSitemapsByPattern($year = '????', $month = '??')
    {
        if (empty($year) && empty($month)) {
            return;
        }

        $path    = $this->publicDir . '/' . $this->instance->getSitemapShortPath() . '/';
        $pattern = sprintf('sitemap.%s.%s*.xml.gz', $year, $month);

        array_map('unlink', glob($path . $pattern));
    }

    /**
     * Save the sitemap and returns the length.
     *
     * @param string $path     The path of the sitemap.
     * @param string $contents The array of contents to persist.
     *
     * @return integer The length of the sitemap.
     */
    public function saveSitemap($path, $contents)
    {
        $dirPath = dirname($path);

        if (!$this->fs->exists($dirPath)) {
            $this->fs->mkdir($dirPath);
        }

        return file_put_contents($path, gzencode($contents, 9));
    }

    /**
     * Returns the years of the sitemaps.
     *
     * @param string $types The allowed types.
     *
     * @return array The years of the sitemap.
     */
    protected function getYears($types)
    {
        $result = $this->connection->fetchAll(
            sprintf(
                'SELECT year(changed) as "year"'
                . 'FROM `contents` WHERE changed is not null '
                . 'AND `content_type_name` IN (%s) '
                . 'group by year order by year',
                $types
            )
        );

        return array_map(function ($a) {
            return strval($a['year']);
        }, $result);
    }
}
