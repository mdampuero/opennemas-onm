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

use Onm\Settings as s;
use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

/**
 * Handles the operations of Newsletters.
 */
class NewsletterManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param DbalWrapper    $dbConn      The database connection.
     * @param CacheInterface $cache       The cache service.
     * @param string         $cachePrefix The cache prefix.
     * @param  Template       $template    The template service.
     */
    public function __construct(
        DbalWrapper $dbConn,
        CacheInterface $cache,
        $cachePrefix,
        $tpl,
        EntityManager $entityManager
    ) {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
        $this->tpl         = $tpl;
        $this->er          = $entityManager;
        $this->cm          = new \ContentManager();
    }

    /**
     * Performs searches in newsletters
     *
     * @param string  $whereClause  The where clause to insert into the search.
     * @param string  $order        The order clause for the search.
     * @param integer $page         The page where start the paginated results.
     * @param integer $itemsPerPage The number of items per page.
     *
     * @return array The newsletters that matches the search criterias.
     */
    public function find(
        $whereClause = '1 = 1',
        $order = 'created DESC',
        $page = null,
        $itemsPerPage = 20
    ) {
        $limit = '';
        if (!is_null($page)) {
            $limit = ' LIMIT ' . ($page - 1) * $itemsPerPage . ', ' . $itemsPerPage;

            if ($page == 1) {
                $limit = ' LIMIT ' . $itemsPerPage;
            }
        }

        try {
            $rs = $this->dbConn->fetchAll(
                'SELECT * FROM `newsletter_archive`'
                . ' WHERE ' . $whereClause . ' ORDER BY ' . $order . ' ' . $limit
            );

            $countNm = $this->dbConn->fetchColumn(
                'SELECT COUNT(`pk_newsletter`) FROM `newsletter_archive` '
                . 'WHERE ' . $whereClause . ' ORDER BY ' . $order
            );

            $newsletters = [];
            foreach ($rs as $newsletterData) {
                $obj = new \Newsletter();
                $obj->load($newsletterData);

                $newsletters[] = $obj;
            }

            return [$countNm, $newsletters];
        } catch (\Exception $e) {
            error_log('Error fetching newsletters: ' . $e->getMessage());
            return;
        }
    }

    /**
     * Renders the newsletter from a list of contents.
     *
     * @param array $contents The list of the contents.
     *
     * @return string The generated html for the newsletter.
     */
    public function render($contents)
    {
        $newsletterContent = $contents;

        if (empty($newsletterContent)) {
            $newsletterContent = [];
        }

        foreach ($newsletterContent as $container) {
            foreach ($container->items as &$item) {
                // if current item do not fullfill the required format
                // then skip it
                if (empty($item->id) || $item->content_type == 'label') {
                    continue;
                }

                $content = $this->er->find($item->content_type, $item->id);

                // if is not a real content, skip this element
                if (!is_object($content) || is_null($content->id)) {
                    continue;
                }

                $item = $this->hydrateContent($content);
            }
        }

        $this->tpl->assign('newsletterContent', $newsletterContent);

        // Fetch and assign the frontpage menu
        $menu = new \Menu();
        $menu = $menu->getMenu('frontpage');
        $this->tpl->assign('menuFrontpage', $menu->items);

        // Fetch and assign newsletter ads
        $positions = getService('core.helper.advertisement')
            ->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $ads       = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);
        $this->tpl->assign('advertisements', $ads);

        // Format and assign the current date.
        // CRAP!
        $days   = [
            'Domingo', 'Lunes', 'Martes', 'Miércoles',
            'Jueves', 'Viernes', 'Sábado'
        ];
        $months = [
            '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $time = new \DateTime();
        $this->tpl->assign(
            'current_date',
            $days[$time->format('w')] . ' ' . $time->format('j') . ' de ' .
            $months[(int) $time->format('n')] . ' ' . $time->format('Y')
        );

        // Process and assign public URL for images and links
        $publicUrl = preg_replace(
            '@^http[s]?://(.*?)/$@i',
            'http://$1',
            getService('core.instance')->getMainDomain()
        );
        $this->tpl->assign('URL_PUBLIC', 'http://' . $publicUrl);

        // Fetch and assign settings
        $configurations = s::get([
            'newsletter_maillist',
            'newsletter_subscriptionType',
        ]);
        $this->tpl->assign('conf', $configurations);
        $this->tpl->assign('render_params', ['ads-format' => 'inline']);

        return $this->tpl->fetch('newsletter/newNewsletter.tpl');
    }

    /**
     * Completes the content information from an object from repository
     *
     * @param Content $item the item to complete
     * @param Content $content the content from the repository
     *
     * @return Content the item completed
     **/
    public function hydrateContent($content)
    {
        $content->cat    = $content->category_name;
        $content->name   = (isset($content->name)) ? $content->name : '';
        $content->image  = (isset($content->cover)) ? $content->cover : '';
        $content->agency = is_array($content->params) && array_key_exists('agencyBulletin', $content->params)
            ? $content->params['agencyBulletin'] : '';
        $content->date   = date(
            'Y-m-d',
            strtotime(str_replace('/', '-', substr($content->created, 6)))
        );

        // Fetch images of articles if exists
        $content->photo = [];
        if (!empty($content->img1)) {
            $content->photo[] = $this->er->find('Photo', $content->img1);
        } elseif (!empty($content->fk_video)) {
            $content->video = $this->er->find('Video', $content->fk_video);
        } elseif (!empty($content->img2)) {
            $content->photo[] = $this->er->find('Photo', $content->img2);
        }

        if (!isset($content->summary)) {
            $content->summary = substr(strip_tags($content->body), 0, 250) . '...';
        }

        // Fetch opinion author photos
        if ($content->content_type == '4') {
            $content->author = $this->er->find('User', $content->fk_author);
        }

        // Fetch video thumbnails
        if ($content->content_type == '9') {
            $content->thumb = $content->getThumb();
        }

        return $content;
    }
}
