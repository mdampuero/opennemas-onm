<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Api\Exception\GetListException;

class MetaHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The advertisement group name for a page.
     *
     * @var string
     */
    protected $group;

    protected $parsedPrefix = [
        'Album' => 'Galleries',
        'Author' => 'Authors',
        'Blog' => 'Blogs,',
        'Copmany' => 'Companies',
        'Event' => 'Events',
        'Poll' => 'Polls',
        'Tag' => 'Tags',
        'Video' => 'Videos'
    ];

    /**
     * Initializes the AdvertisementHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->conn      = $container->get('orm.connection.instance');
        $this->globals   = $container->get('core.globals');
        $this->settings  = $container->get('orm.manager')->getDataSet('Settings');

        $this->categoryHelper = $container->get('core.helper.category');
        $this->authorHelper = $container->get('core.helper.author');

        $this->siteTitle       = $this->settings->get('site_title');
        $this->siteKeywords    = $this->settings->get('site_keywords');
        $this->siteDescription = $this->settings->get('site_description');

        $this->customActions = [
            'show | frontpages' => [
                'title' => '%s' . $this->siteTitle,
                'keywords' => _('Frontpage') . ',%s' . $this->siteKeywords,
                'description' => _('Frontpage') . ' %s' . $this->siteDescription,
            ],
            'login | authentication' => [
                'title' => _('Access your account') . ' - ' . $this->siteTitle,
                'keywords' => 'Access, Account, ' . $this->siteKeywords,
                'description' => _('Access your account') . ' , ' . $this->siteDescription,
            ],
            'show | user' => [
                'title' => _('Edit my profile') . ' - ' . $this->siteTitle,
                'keywords' => 'Edit, Profile, ' . $this->siteKeywords,
                'description' => _('Edit my profile') . ' , ' . $this->siteDescription,
            ],
            'register | user' => [
                'title' => _('Create your user account') . ' - ' . $this->siteTitle,
                'keywords' => 'Create, User, Account, ' . $this->siteKeywords,
                'description' => _('Create your user account') . ' , ' . $this->siteDescription,
            ],
            'save | user' => [
                'title' => _('Create your user account') . ' - ' . $this->siteTitle,
                'keywords' => 'Create, User, Account, ' . $this->siteKeywords,
                'description' => _('Create your user account') . ' , ' . $this->siteDescription,
            ]
        ];
    }

    public function generateMetas($content, $page)
    {
        $data        = $this->generateData($content, $page);
        $title       = $this->generateTitle($data);
        $keywords    = $this->generateKeywords($data);
        $description = $this->generateDescription($data);

        return $title . '\n' . $description . '\n' . $keywords;
    }

    public function generateData($content, $page)
    {
        $data      = [];
        $extension = $this->globals->getExtension();
        $action    = $this->globals->getAction();
        $key       = $action . ' | ' . $extension;
        if (array_key_exists($key, $this->customActions)) {
            $data = array_merge($data, $this->customActions[$key]);
        }
        $data['page']   = $page && is_int($page) && $page > 1 ? _('Page') . ' ' . $page . ' ' : '';
        $data['prefix'] = $this->parsedPrefix[ucfirst($extension)] ?? ucfirst($extension);

        if ($content && $content instanceof \Common\Model\Entity\Content) {
            $description = trim(
                strip_tags(
                    $content->seo_description ??
                    $this->container->get('core.helper.content')->getSummary($content)
                )
            );

            $data['content_description'] = (strlen($description) > 160) ?
                substr($description, 0, 157) . '...' : $description;

            $title = $content->seo_title ?? $content->title_int ?? $content->title ?? '';
            $title = empty($title) ? $this->siteTitle : trim(strip_tags($title));

            $data['content_title'] = (strlen($title) > 90) ? substr($title, 0, 87) . '...' : $title;
            if ($content->tags && !empty($content->tags)) {
                try {
                    $tags = $this->container->get('api.service.tag')->getListByIds($content->tags)['items'];
                    $tags = array_map(function ($tag) {
                        return strip_tags($tag->name);
                    }, $tags);

                    $data['content_tags'] = implode(',', $tags);
                } catch (GetListException $e) {
                    unset($data['content_tags']);
                }
            }
            return $data;
        }
        $data['data_description'] = trim($this->authorHelper->getAuthorBioSummary($content));
        $data['data_name']        = $this->authorHelper->getAuthorName($content) ??
            $this->categoryHelper->getCategoryName($content) ??
            $content->name ??
            '';
        return $data;
    }

    public function generateDescription($data)
    {
        $description = $data['description'] ?? $data['content_description'] ?? $data['data_description'];
        if (!empty($description)) {
            $replacement = $data['data_name'] ? $data['data_name'] . ', ' : '';
            $description = sprintf($description, $replacement);
            return '<meta name="description" content="' . $description . '" />';
        }

        $description  = '';
        $description .= !empty($data['prefix']) ? _($data['prefix']) . ' ' : '';
        $description .= !empty($data['data_name']) ? $data['data_name'] . ' ' : '';
        $description .= !empty($data['page']) ? ' ' . $data['page'] : '';
        return '<meta name="description" content="' . trim($description) . '" />';
    }

    public function generateKeywords($data)
    {
        $keywords = $data['keywords'] ?? $data['content_tags'];
        if (!empty($keywords)) {
            $replacement = $data['data_name'] ? $data['data_name'] . ',' : '';
            $keywords    = sprintf($keywords, $replacement);
            return '<meta name="keywords" content="' . $keywords . '" />';
        }

        $keywords   = [];
        $keywords[] = !empty($data['prefix']) ? _($data['prefix']) : '';
        $keywords[] = !empty($data['data_name']) ? $data['data_name'] : '';
        $keywords[] = !empty($data['page']) ? $data['page'] : '';

        $keywords = array_filter($keywords, 'strlen');

        return '<meta name="keywords" content="' . implode(',', $keywords) . '" />';
    }

    protected function generateTitle($data)
    {
        $title = $data['title'] ?? $data['content_title'];
        if (!empty($title)) {
            $replacement = $data['data_name'] ? $data['data_name'] . ' - ' : '';
            $title       = sprintf($title, $replacement);
            return '<title>' . $title . '</title>';
        }

        $title  = '';
        $title .= !empty($data['prefix']) ? '- ' . _($data['prefix']) . ' ' : '';
        $title .= !empty($data['data_name']) ? '- ' . $data['data_name'] . ' ' : '';
        $title .= !empty($data['page']) ? '- ' . $data['page'] . ' ' : '';
        return '<title>' . substr(ltrim($title), 1) . '</title>';
    }
}
