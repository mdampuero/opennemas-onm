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
            $sql = 'SELECT * FROM `newsletter_archive` WHERE '.$whereClause. ' ORDER BY '.$order.' '.$limit;
            $rs = $this->dbConn->fetchAll($sql);

            $sql = 'SELECT COUNT(`pk_newsletter`)  FROM `newsletter_archive` WHERE '.$whereClause. ' ORDER BY '.$order;
            $countNm = $this->dbConn->fetchColumn($sql);

            $newsletters = [];
            foreach ($rs as $newsletterData) {
                $obj = new \Newsletter();
                $obj->load($newsletterData);

                $newsletters[] = $obj;
            }
            return array($countNm, $newsletters);
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
            $newsletterContent = array();
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

        // render menu
        $menuManager = new \Menu();
        $menuFrontpage= $menuManager->getMenu('frontpage');
        $this->tpl->assign('menuFrontpage', $menuFrontpage->items);

        // Render ads
        $ads = getService('advertisement_repository')
            ->findByPositionsAndCategory([ 1001, 1009 ], 0);
        $this->tpl->assign('advertisements', $ads);

         // VIERNES 4 DE SEPTIEMBRE 2009
        $days = array(
            'Domingo', 'Lunes', 'Martes', 'Miércoles',
            'Jueves', 'Viernes', 'Sábado'
        );
        $months = array(
            '', 'Enero', 'Febrero', 'Marzo',
            'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre',
            'Octubre', 'Noviembre', 'Diciembre'
        );

        $time = new \DateTime();

        $currentDate = $days[$time->format('w')].' '.
                       $time->format('j').' de '.
                       $months[(int) $time->format('n')].' '.
                       $time->format('Y');

        $this->tpl->assign('current_date', $currentDate);

        $publicUrl = preg_replace(
            '@^http[s]?://(.*?)/$@i',
            'http://$1',
            getService('core.instance')->getMainDomain()
        );

        $this->tpl->assign('URL_PUBLIC', 'http://' . $publicUrl);

        $configurations = s::get([
            'newsletter_maillist',
            'newsletter_subscriptionType',
        ]);

        $this->tpl->assign('conf', $configurations);
        $htmlContent = $this->tpl->fetch('newsletter/newNewsletter.tpl');

        return $htmlContent;
    }
}
