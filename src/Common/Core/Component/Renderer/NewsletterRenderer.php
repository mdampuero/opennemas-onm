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
use Api\Exception\GetItemException;

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
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->tpl       = $container->get('core.template.frontend');
    }

    /**
     * Returns the HTML for a newsletter.
     *
     * @param Newsletter $item The newsletter to render.
     *
     * @return string The HTML for the newsletter.
     */
    public function render($newsletter)
    {
        $newsletterContent = $this->hydrateContainers($newsletter);

        $menuService = $this->container->get('api.service.menu');

        try {
            $menu = $menuService->getItemLocaleBy(' position = "newsletter" ');
        } catch (GetItemException $e) {
            $menu = [];
        }

        try {
            $menu = empty($menu) ?
                $menuService->getItemLocaleBy(' name = "frontpage" ') :
                $menu;
        } catch (GetItemException $e) {
            $menu             = new \stdClass();
            $menu->menu_items = [];
        }

        $menuHelper = $this->container->get('core.helper.menu');

        $positions      = $this->container->get('core.helper.advertisement')
            ->getPositionsForGroup('newsletter', [ 1001, 1009 ]);
        $advertisements = $this->container->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, 0);

        $this->container->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);

        $this->tpl->assign('ads_format', 'newsletter');

        // Process public URL for images and links
        $publicUrl = preg_replace(
            '@^http[s]?://(.*?)/$@i',
            'http://$1',
            $this->container->get('core.globals')->getInstance()->getMainDomain()
        );

        $time = new \DateTime(null, $this->container->get('core.locale')->getTimeZone());

        $newsletter->title = !empty($newsletter->params['append_title']) ?
            $this->updateTitle($newsletter, $newsletterContent) :
            sprintf('%s [%s]', $newsletter->title, $time->format('d/m/Y'));

        return $this->tpl->fetch('newsletter/newsletter.tpl', [
            'item'              => $newsletter,
            'newsletterContent' => $newsletterContent,
            'menuFrontpage'     => $menuHelper->castToObjectFlat($menu->menu_items, false),
            'current_date'      => new \DateTime(),
            'URL_PUBLIC'        => 'http://' . $publicUrl,
        ]);
    }

    private function updateTitle($newsletter, $content)
    {
        $result = trim($newsletter->title);
        if ($content[0] && $content[0]['items'][0]) {
            $result .= " " . trim($content[0]['items'][0]['title']);
        }
        return $result;
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
        if (empty($criteria)) {
            return $contents;
        }

        $total   = ($criteria['epp'] > 0) ? $criteria['epp'] : 5;
        $orderBy = [ 'starttime' => 'desc' ];
        $date    = new \DateTime(null, $this->container->get('core.locale')
            ->getTimeZone('frontend'));

        $searchCriteria = [
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 0 ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator'  => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if ($criteria['filter'] === 'in_last_day') {
            $date->sub(new \DateInterval('P1D'));

            $searchCriteria = array_merge($searchCriteria, [
                'starttime'         => [
                    [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
                    [ 'value' => $date->format('Y-m-d H:i:s'), 'operator' => '>=' ],
                ],
            ]);
        }

        if ($criteria['filter'] === 'most_viewed') {
            $date->sub(new \DateInterval('P3D'));

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
                    [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
                    [ 'value' => $date->format('Y-m-d H:i:s'), 'operator' => '>=' ],
                ],
            ]);

            $orderBy = [ 'views' => 'desc', 'starttime' => 'desc' ];
        }

        $searchCriteria['content_type_name'] = !empty($criteria['content_type'])
            ? [[ 'value' => $criteria['content_type']]]
            : [[ 'value' => [
                'album',
                'article',
                'attachment',
                'kiosko',
                'letter' ,
                'opinion',
                'poll',
                'static_page',
                'video'
            ], 'operator' => 'IN' ]];

        if (!empty($criteria['category'])
            && !in_array($criteria['content_type'], [ 'opinion', 'letter', 'static_page' ])
        ) {
            $searchCriteria['category_id'] = [
                [ 'value' => $criteria['category'], 'operator' => 'IN' ]
            ];
        }

        if (!empty($criteria['tag'])
            && !in_array($criteria['content_type'], [ 'static_page' ])
        ) {
            $searchCriteria['tag_id'] = [
                [ 'value' => $criteria['tag'], 'operator' => 'IN' ]
            ];
        }

        if ($criteria['content_type'] === 'opinion' && !empty($criteria['opinion_type'])) {
            $bloggers   = $this->container->get('api.service.author')->getList('is_blog=1')['items'];
            $bloggersId = array_map(function ($item) {
                return $item->id;
            }, $bloggers);

            $operator = $criteria['opinion_type'] === 'blog' ? 'IN' : 'NOT IN';

            if (!empty($bloggersId)) {
                $searchCriteria['contents.fk_author'] = [
                    [ 'value' => $bloggersId, 'operator' => $operator ]
                ];
            }
        }

        return array_map(function ($a) {
            return [
                'id'                        => $a->id,
                'content_type'              => $a->content_type,
                'content_type_l10n_name'    => $a->content_type_l10n_name,
                'title'                     => $a->title,
                'content'                   => $a
            ];
        }, $this->container->get('entity_repository')
            ->findBy($searchCriteria, $orderBy, $total, 1));
    }

    /**
     * Returns the list of containers and contents based on the newsletter
     * configuration.
     *
     * @param Newsletter $newsletter The newsletter.
     *
     * @return array The list of containers and contents.
     */
    protected function hydrateContainers($newsletter)
    {
        $containers = $newsletter->contents;

        foreach ($containers as $index => &$container) {
            if (!array_key_exists('id', $container)) {
                $container['id'] = $index + 1;
            }

            foreach ($container['items'] as $index => &$item) {
                // If current item do not fullfill the required format then skip it
                if ($item['content_type'] === 'label') {
                    continue;
                }

                if ($item['content_type'] === 'list') {
                    $contents = $this->getContents($item['criteria']);
                    unset($container['items'][$index]);

                    $container['items'] = array_merge($container['items'], $contents);
                    continue;
                }

                $content = $this->container->get('entity_repository')
                    ->find(classify($item['content_type']), $item['id']);

                // if is not a real content, skip this element
                if (!is_object($content) || is_null($content->id)) {
                    continue;
                }

                $item['content'] = $content;
            }
        }

        return $containers;
    }
}
