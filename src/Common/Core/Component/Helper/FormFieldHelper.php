<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to translate fields for settings modal.
 */
class FormFieldHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * All collapsible fields in forms
     *
     * @var array
     */
    protected $fields;

    public function __construct($container)
    {
        $this->container = $container;
        $this->fields    = [
            [
                'name' => 'live_blog_posting',
                'title' => _('Live blog post'),
                'module' => 'es.openhost.module.live_blog_posting',
                'available' => [ 'article' ]
            ],
            [
                'name' => 'google_news_showcase',
                'title' => _('Google News Showcase'),
                'module' => 'es.openhost.module.google_news_showcase',
                'available' => [ 'article' ]
            ],
            [
                'name' => 'author',
                'title' => _('Author'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'company',
                    'event',
                    'obituary',
                    'opinion',
                    'poll',
                    'video',
                ]
            ],
            [
                'name' => 'category',
                'title' => _('Category'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'attachment',
                    'event',
                    'newsstand',
                    'poll',
                    'video',
                ]
            ],
            [
                'name' => 'tags',
                'title' => _('Tags'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'company',
                    'event',
                    'letter',
                    'obituary',
                    'opinion',
                    'photo',
                    'poll',
                    'video',
                ]
            ],
            [
                'name' => 'slug',
                'title' => _('Slug'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'author',
                    'company',
                    'event',
                    'letter',
                    'newsstand',
                    'obituary',
                    'opinion',
                    'poll',
                    'staticPage',
                    'user',
                    'video',
                ]
            ],
            [
                'name' => 'bodyLink',
                'title' => _('External link'),
                'module' => false,
                'available' => [ 'article', 'opinion' ]
            ],
            [
                'name' => 'schedule',
                'title' => _('Schedule'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'attachment',
                    'company',
                    'event',
                    'letter',
                    'newsstand',
                    'obituary',
                    'opinion',
                    'poll',
                    'video',
                    'widget',
                ]
            ],
            [
                'name' => 'seo',
                'title' => _('Options for SEO'),
                'module' => false,
                'available' => [
                    'article',
                    'album',
                    'opinion',
                    'poll',
                    'video',
                ]
            ],
            [
                'name' => 'webpush',
                'title' => _('Webpush Notifications'),
                'module' => 'es.openhost.module.webpush_notifications',
                'available' => [ 'article' ]
            ],
            [
                'name' => 'pressclipping',
                'title' => _('PressClipping'),
                'module' => 'es.openhost.module.pressclipping',
                'available' => [ 'article' ]
            ],
            [
                'name' => 'closed',
                'title' => _('Vote end date'),
                'module' => false,
                'available' => [ 'poll' ]
            ],
            [
                'name' => 'date',
                'title' => _('Date'),
                'module' => false,
                'available' => [ 'newsstand' ]
            ],
            [
                'name' => 'subscriptions',
                'title' => _('Lists'),
                'module' => false,
                'available' => [ 'subscriber' ]
            ],
            [
                'name' => 'lists',
                'title' => _('Lists'),
                'module' => 'CONTENT_SUBSCRIPTIONS',
                'available' => [ 'article', 'opinion' ]
            ],
            [
                'name' => 'organizer',
                'title' => _('Organizer data'),
                'module' => false,
                'available' => [ 'event' ]
            ],
            [
                'name' => 'when',
                'title' => _('Event date'),
                'module' => false,
                'available' => [ 'event' ]
            ],
            [
                'name' => 'where',
                'title' => _('Event location'),
                'module' => false,
                'available' => [ 'event' ]
            ],
            [
                'name' => 'external_website',
                'title' => _('External website'),
                'module' => false,
                'available' => [ 'event' ]
            ],
            [
                'name' => 'category',
                'title' => _('Subsection of'),
                'module' => false,
                'available' => [ 'category' ]
            ],
            [
                'name' => 'color',
                'title' => _('Color'),
                'module' => false,
                'available' => [ 'category' ]
            ],
            [
                'name' => 'logo',
                'title' => _('Logo'),
                'module' => false,
                'available' => [ 'category' ]
            ],
            [
                'name' => 'cover',
                'title' => _('Cover'),
                'module' => false,
                'available' => [ 'category' ]
            ],
            [
                'name' => 'type',
                'title' => _('Type'),
                'module' => false,
                'available' => [ 'category' ]
            ],
            [
                'name' => 'company_info',
                'title' => _('Company info'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'timetable',
                'title' => _('Timetable'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'phone',
                'title' => _('Phone'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'email',
                'title' => _('Email'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'maps',
                'title' => _('Google Maps'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'website',
                'title' => _('Website'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'social_media',
                'title' => _('Social network'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'logo',
                'title' => _('Logo'),
                'module' => false,
                'available' => [ 'company' ]
            ],
            [
                'name' => 'date',
                'title' => _('Event date'),
                'module' => false,
                'available' => [ 'obituary' ]
            ],
            [
                'name' => 'mortuary',
                'title' => _('Mortuary'),
                'module' => false,
                'available' => [ 'obituary' ]
            ],
            [
                'name' => 'website',
                'title' => _('Website'),
                'module' => false,
                'available' => [ 'obituary' ]
            ],
            [
                'name' => 'maps',
                'title' => _('Google maps url'),
                'module' => false,
                'available' => [ 'obituary' ]
            ],
            [
                'name' => 'visibility',
                'title' => _('Visibility'),
                'module' => false,
                'available' => [ 'subscription', 'userGroup' ]
            ],
            [
                'name' => 'redirection',
                'title' => _('Redirection'),
                'module' => false,
                'available' => [ 'url' ]
            ],
            [
                'name' => 'request',
                'title' => _('Requests'),
                'module' => false,
                'available' => [ 'subscription' ]
            ],
            [
                'name' => 'user_groups',
                'title' => _('User groups'),
                'module' => false,
                'available' => [ 'user' ]
            ],
            [
                'name' => 'language',
                'title' => _('Language & time'),
                'module' => false,
                'available' => [ 'user' ]
            ],
            [
                'name' => 'featuredFrontpage',
                'title' => _('Featured in frontpage'),
                'module' => false,
                'available' => [
                    'album',
                    'article',
                    'event',
                    'obituary',
                    'opinion',
                    'video',
                ]
            ],
            [
                'name' => 'featuredInner',
                'title' => _('Featured in inner'),
                'module' => false,
                'available' => [
                    'article',
                    'company',
                    'event',
                    'obituary',
                    'opinion',
                    'poll'
                ]
            ],
            [
                'name' => 'relatedFrontpage',
                'title' => _('Related in frontpage'),
                'module' => false,
                'available' => [ 'article' ]
            ],
            [
                'name' => 'relatedInner',
                'title' => _('Related in inner'),
                'module' => false,
                'available' => [ 'article', 'company' ]
            ],
            [
                'name' => 'agency',
                'title' => _('Agency'),
                'module' => false,
                'available' => [ 'album' ]
            ],
        ];

        return $this;
    }

    /**
    * Filters the field to return of the specific module.
    *
    * @param array $fields list of items to translate
    *
    * @return array The translated items
    */
    public function filterFields($module)
    {
        if (empty($module)) {
            return [];
        }

        $cs = $this->container->get('core.security');

        $result = array_filter($this->fields, function ($field) use ($module, $cs) {
            return in_array($module, $field['available'])
                && (empty($field['module']) || $cs->hasExtension($field['module']));
        });

        return array_values($result);
    }
}
