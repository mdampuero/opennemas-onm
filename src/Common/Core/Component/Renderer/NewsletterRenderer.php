<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Renderer;

use Repository\EntityManager;
use Repository\CategoryManager;
use Api\Service\V1\AuthorService;

/**
 * The AdvertisementRenderer service provides methods to generate the HTML code
 * for advertisements basing on the advertisements information.
 */
class NewsletterRenderer
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the newsletter renderer.
     *
     * @param Template            $template        The template service.
     * @param EntityRepository    $entityManager   The entity manager.
     * @param CategoryRepository  $categoryManager The category manager.
     * @param AuthorService       $authorService   The author service.
     * @param SettingRepository   $settinManager   The settings repository.
     * @param AdvertisementHelper $adsHelper       The advertisement helper.
     * @param adsRepository       $adsRepository   The advertisement repository.
     * @param Instance            $instance        The current instance.
     */
    public function __construct(
        $tpl,
        EntityManager $entityManager,
        CategoryManager $categoryManager,
        AuthorService $authorService,
        $settingManager,
        $adsHelper,
        $adsRepository,
        $instance
    ) {
        $this->tpl      = $tpl;
        $this->er       = $entityManager;
        $this->cr       = $categoryManager;
        $this->as       = $authorService;
        $this->sr       = $settingManager;
        $this->adHelper = $adsHelper;
        $this->ar       = $adsRepository;
        $this->instance = $instance;
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

        // HACK: Force object conversion, not proud of this, please look other side
        $newsletterContent = json_decode(json_encode($newsletterContent), false);

        $index = 1;
        foreach ($newsletterContent as &$container) {
            if (!property_exists($container, 'id')) {
                $container->id = $index;
            }

            foreach ($container->items as $index => &$item) {
                // if current item do not fullfill the required format
                // then skip it
                if ($item->content_type === 'label') {
                    continue;
                } elseif ($item->content_type === 'list') {
                    $contents = $this->getContents($item->criteria);
                    unset($container->items[$index]);

                    $container->items = array_merge($container->items, $contents);
                } else {
                    $content = $this->er->find(classify($item->content_type), $item->id);

                    // if is not a real content, skip this element
                    if (!is_object($content) || is_null($content->id)) {
                        continue;
                    }

                    $item = $this->hydrateContent($content);
                }
            }

            $index++;
        }

        $this->tpl->assign('newsletterContent', $newsletterContent);

        // Fetch and assign the frontpage menu
        $menu = new \Menu();
        $menu = $menu->getMenu('frontpage');
        $this->tpl->assign('menuFrontpage', $menu->items);

        // Fetch and assign newsletter ads
        $positions = $this->adHelper->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $ads       = $this->ar->findByPositionsAndCategory($positions, 0);
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
            $this->instance->getMainDomain()
        );
        $this->tpl->assign('URL_PUBLIC', 'http://' . $publicUrl);

        // Fetch and assign settings
        $configurations = $this->sr->get([
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

    /**
     * Returns the list of contents
     *
     * @param array $criteria the list of properties required to find contents
     *
     * @return array the list of contents
     **/
    public function getContents($criteria)
    {
        $contents = [];

        if (!is_object($criteria)) {
            return $contents;
        }

        $total   = ($criteria->epp > 0) ? $criteria->epp : 5;
        $orderBy = [ 'starttime' => 'desc' ];

        // Calculate the SQL to fetch contents
        // Criteria has: content_type, category, filter, epp and sortBy elements

        $searchCriteria = [
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        // Implementation for: in_las_day filter
        if ($criteria->filter === 'in_last_day') {
            $yesterday = new \DateTime(null, getService('core.locale')->getTimeZone('frontend'));
            $yesterday->sub(new \DateInterval('P1D'));

            $searchCriteria = array_merge($searchCriteria, [
                'starttime'         => [
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                    [ 'value' => $yesterday->format('Y-m-d H:i:s'), 'operator' => '>=' ],
                ],
            ]);

            $orderBy = [ 'starttime' => 'desc' ];
        }

        // Implementation for: most_viewed
        if ($criteria->filter === 'most_viewed') {
            $searchCriteria = array_merge($searchCriteria, [
                'join' => [
                    [
                        'type'       => 'INNER',
                        'table'      => 'content_views',
                        'contents.pk_content' => [ [ 'value' => 'content_views.pk_fk_content', 'field' => true ] ]
                    ]
                ],
            ]);

            $orderBy = [ 'views' => 'desc', 'starttime' => 'desc' ];
        }

        // Implementation for: blogs in 24hours filter
        if ($criteria->filter === 'blogs') {
            $users      = $this->as->getList('is_blog=1')['items'];
            $userBlogID = array_map(function ($el) {
                return $el->id;
            }, $users);

            $searchCriteria = array_merge($searchCriteria, [
                'contents.fk_author' => [[ 'value' => $userBlogID, 'operator' => 'IN' ]],
                'contents.in_home'   => [[ 'value' => 1 ]],
            ]);
        }

        if (!empty($criteria->content_type)) {
            $contentTypeId                     = \ContentManager::getContentTypeIdFromName($criteria->content_type);
            $searchCriteria['fk_content_type'] = [ ['value' => (int) $contentTypeId ] ];
        } else {
            // ['frontpage', 'schedule', 'photo', 'event', 'advertisement', 'widget'];
            $excludedTypes                     = [18, 16, 8, 5, 2, 12];
            $searchCriteria['fk_content_type'] = [ [ 'value' => $excludedTypes, 'operator' => 'NOT IN' ] ];
        }

        if (!empty($criteria->category)) {
            $searchCriteria['pk_fk_content_category'] = [[ 'value' => (int) $criteria->category, ]];
        }

        $contents = $this->er->findBy($searchCriteria, $orderBy, $total, 1);

        foreach ($contents as &$content) {
            $content = $this->hydrateContent($content);
        }

        return $contents;
    }
}
