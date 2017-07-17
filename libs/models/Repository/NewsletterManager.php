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
     * @pram  Template       $template    The template service.
     */
    public function __construct(DbalWrapper $dbConn, CacheInterface $cache, $cachePrefix, $tpl)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
        $this->tpl         = $tpl;
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
        if (!is_null($page)) {
            if ($page == 1) {
                $limit = ' LIMIT '. $itemsPerPage;
            } else {
                $limit = ' LIMIT '.($page-1) * $itemsPerPage.', '.$itemsPerPage;
            }
        } else {
            $limit = '';
        }

        try {
            $sql = 'SELECT * FROM `newsletter_archive`'
                .' WHERE '.$whereClause. ' ORDER BY '.$order.' '.$limit;
            $rs = $this->dbConn->fetchAll($sql);

            $sql = 'SELECT COUNT(`pk_newsletter`) FROM `newsletter_archive` '
                .'WHERE '.$whereClause. ' ORDER BY '.$order;
            $countNm = $this->dbConn->fetchColumn($sql);

            $newsletters = [];
            foreach ($rs as $newsletterData) {
                $obj = new \Newsletter();
                $obj->load($newsletterData);

                $newsletters[] = $obj;
            }
            return [$countNm, $newsletters];
        } catch (\Exception $e) {
            error_log('Error fetching newsletters: '.$e->getMessage());
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
        $cm  = new \ContentManager();

        $newsletterContent = $contents;

        if (empty($newsletterContent)) {
            $newsletterContent = [];
        }

        $er = getService('entity_repository');
        foreach ($newsletterContent as $container) {
            foreach ($container->items as &$item) {
                if (!empty($item->id) && $item->content_type !='label') {
                    $content = $er->find($item->content_type, $item->id);
                    $content = new $item->content_type($item->id);

                    //if is a real content include it in the contents array
                    if (is_object($content) && !is_null($content->id)) {
                        $content = $content->get($item->id);
                        $item->content_type = $content->content_type;
                        $item->title        = $content->title;
                        $item->slug         = $content->slug;
                        $item->uri          = $content->uri;
                        $item->subtitle     = $content->subtitle;
                        $item->date         = date(
                            'Y-m-d',
                            strtotime(str_replace('/', '-', substr($content->created, 6)))
                        );
                        $item->cat          = $content->category_name;
                        $item->agency       = '';
                        if (is_array($content->params)
                            && array_key_exists('agencyBulletin', $content->params)
                        ) {
                            $item->agency   = $content->params['agencyBulletin'];
                        }
                        $item->name         = (isset($content->name))?$content->name:'';
                        $item->image        = (isset($content->cover))?$content->cover:'';

                        // Fetch images of articles if exists
                        if (!empty($content->img1)) {
                            $item->photo = $cm->find('Photo', 'pk_content ='.$content->img1);
                        } elseif (!empty($content->fk_video)) {
                            $item->video = $er->find('Video', $content->fk_video);
                        } elseif (!empty($content->img2)) {
                            $item->photo = $cm->find('Photo', 'pk_content ='.$content->img2);
                        }

                        if (isset($content->summary)) {
                            $item->summary  = $content->summary;
                        } else {
                            $item->summary = substr(strip_tags($content->body), 0, 250).'...';
                        }
                        if (isset($content->description)) {
                            $item->description  = $content->description;
                        }
                        //Fetch opinion author photos
                        if ($content->content_type == '4') {
                            $item->author = new \User($content->fk_author);
                        }
                        //Fetch video thumbnails
                        if ($content->content_type == '9') {
                            $item->thumb = $content->getThumb();
                        }
                    }
                }
            }
        }

        $this->tpl->assign('newsletterContent', $newsletterContent);

        // Fetch and assign the frontpage menu
        $menuManager = new \Menu();
        $menuFrontpage = $menuManager->getMenu('frontpage');
        $this->tpl->assign('menuFrontpage', $menuFrontpage->items);

        // Fetch and assign newsletter ads
        $positions = getService('core.helper.advertisement')
            ->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $ads = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);
        $this->tpl->assign('advertisements', $ads);

        // Format and assign the current date.
        // CRAP!
        $days = [
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
            $days[$time->format('w')].' '. $time->format('j').' de '.
            $months[(int) $time->format('n')].' '.$time->format('Y')
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

        return $this->tpl->fetch('newsletter/newNewsletter.tpl');
    }
}
