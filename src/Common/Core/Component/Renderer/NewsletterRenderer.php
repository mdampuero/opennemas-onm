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
     * @param AuthorService       $authorService   The author service.
     * @param SettingRepository   $settinManager   The settings repository.
     * @param AdvertisementHelper $adsHelper       The advertisement helper.
     * @param adsRepository       $adsRepository   The advertisement repository.
     * @param Instance            $instance        The current instance.
     */
    public function __construct(
        $tpl,
        EntityManager $entityManager,
        AuthorService $authorService,
        $em,
        $adsHelper,
        $adsRepository,
        $instance
    ) {
        $this->tpl      = $tpl;
        $this->er       = $entityManager;
        $this->as       = $authorService;
        $this->ds       = $em->getDataSet('Settings', 'instance');
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
                // If current item do not fullfill the required format then skip it
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

        $menu = new \Menu();
        $menu = $menu->getMenu('frontpage');

        $positions = $this->adHelper->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $ads       = $this->ar->findByPositionsAndCategory($positions, 0);

        $this->tpl->assign([
            'advertisements' => $ads,
            'ads_positions'  => $positions,
            'ads_format'     => 'newsletter',
        ]);

        // Process public URL for images and links
        $publicUrl = preg_replace(
            '@^http[s]?://(.*?)/$@i',
            'http://$1',
            $this->instance->getMainDomain()
        );

        $configurations = $this->ds->get([
            'newsletter_maillist',
            'newsletter_subscriptionType',
        ]);

        return $this->tpl->fetch('newsletter/newsletter.tpl', [
            'newsletterContent' => $newsletterContent,
            'menuFrontpage'     => $menu->items,
            'current_date'      => new \DateTime(),
            'conf'              => $configurations,
            'URL_PUBLIC'        => 'http://' . $publicUrl,
        ]);
    }

    /**
     * Completes the content information from an object from repository
     *
     * @param Content $item    The item to complete
     * @param Content $content The content from the repository
     *
     * @return Content The complete item
     */
    public function hydrateContent($content)
    {
        $content->cat    = $content->category_name;
        $content->name   = (isset($content->name)) ? $content->name : '';
        $content->image  = (isset($content->cover)) ? $content->cover : '';
        $content->agency = $content->params['agencyBulletin'] ?? '';
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
     */
    public function getContents($criteria)
    {
        $contents = [];
        if (!is_object($criteria)) {
            return $contents;
        }

        $total   = ($criteria->epp > 0) ? $criteria->epp : 5;
        $orderBy = [ 'starttime' => 'desc' ];

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

        if ($criteria->filter === 'most_viewed') {
            $threeDaysAgo = new \DateTime(null, getService('core.locale')->getTimeZone('frontend'));
            $threeDaysAgo->sub(new \DateInterval('P3D'));

            $searchCriteria = array_merge($searchCriteria, [
                'join' => [
                    [
                        'type'       => 'INNER',
                        'table'      => 'content_views',
                        'contents.pk_content' => [
                            [ 'value' => 'content_views.pk_fk_content', 'field' => true ]
                        ]
                    ]
                ],
                'starttime' => [
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                    [ 'value' => $threeDaysAgo->format('Y-m-d H:i:s'), 'operator' => '>=' ],
                ],
            ]);

            $orderBy = [ 'views' => 'desc', 'starttime' => 'desc' ];
        }

        // article, attachment, opinion, album, video, poll, static_page, kiosko, letter
        $searchCriteria['fk_content_type'] = [
            [ 'value' => [ 1, 3, 4, 7, 9, 11, 13, 14, 17 ], 'operator' => 'IN' ]
        ];

        if (!empty($criteria->content_type)) {
            $searchCriteria['fk_content_type'] = [
                [ 'value' => (int) \ContentManager::getContentTypeIdFromName($criteria->content_type) ]
            ];
        }

        if (!empty($criteria->category)
            && !in_array($criteria->content_type, [ 'opinion', 'letter', 'static_page' ])
        ) {
            $searchCriteria['pk_fk_content_category'] = [
                [ 'value' => $criteria->category, 'operator' => 'IN' ]
            ];
        }

        if ($criteria->content_type === 'opinion' && !empty($criteria->opinion_type)) {
            $bloggers   = $this->as->getList('is_blog=1')['items'];
            $bloggersId = array_map(function ($item) {
                return $item->id;
            }, $bloggers);

            $operator = $criteria->opinion_type === 'blog' ? 'IN' : 'NOT IN';

            if (!empty($bloggersId)) {
                $searchCriteria['contents.fk_author'] = [
                    [ 'value' => $bloggersId, 'operator' => $operator ]
                ];
            }
        }

        $contents = $this->er->findBy($searchCriteria, $orderBy, $total, 1);

        foreach ($contents as &$content) {
            $content = $this->hydrateContent($content);
        }

        return $contents;
    }
}
