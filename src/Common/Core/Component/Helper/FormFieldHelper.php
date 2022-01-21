<?php

namespace Common\Core\Component\Helper;

/**
 * Helper class to translate fields for settings modal.
 */
class FormFieldHelper
{
    /**
     * All collapsible fields in forms
     *
     * @var array
     */
    protected $fields;

    public function __construct()
    {
        $this->fields = [
            [
                'name' => 'author',
                'title' => _('Author'),
                'available' => [
                    'article',
                    'opinion',
                    'event',
                    'poll',
                    'video',
                    'album'
                ]
            ],
            [
                'name' => 'category',
                'title' => _('Category'),
                'available' => [
                    'article',
                    'event',
                    'poll',
                    'attachment',
                    'video',
                    'album',
                    'newsstand'
                ]
            ],
            [
                'name' => 'tags',
                'title' => _('Tags'),
                'available' => [
                    'article',
                    'opinion',
                    'event',
                    'poll',
                    'letter',
                    'photo',
                    'video',
                    'album'
                ]
            ],
            [
                'name' => 'slug',
                'title' => _('Slug'),
                'available' => [
                    'article',
                    'author',
                    'opinion',
                    'event',
                    'poll',
                    'staticPage',
                    'letter',
                    'video',
                    'album',
                    'newsstand',
                    'user'
                ]
            ],
            [
                'name' => 'bodyLink',
                'title' => _('External link'),
                'available' => [
                    'article',
                    'opinion'
                ]
            ],
            [
                'name' => 'schedule',
                'title' => _('Schedule'),
                'available' => [
                    'article',
                    'widget',
                    'opinion',
                    'event',
                    'poll',
                    'letter',
                    'attachment',
                    'video',
                    'album',
                    'newsstand'
                ]
            ],
            [
                'name' => 'closed',
                'title' => _('Vote end date'),
                'available' => [ 'poll' ]
            ],
            [
                'name' => 'date',
                'title' => _('Date'),
                'available' => [ 'newsstand' ]
            ],
            [
                'name' => 'subscriptions',
                'title' => _('Lists'),
                'available' => [ 'subscriber' ]
            ],
            [
                'name' => 'when',
                'title' => _('Event date'),
                'available' => [ 'event' ]
            ],
            [
                'name' => 'where',
                'title' => _('Event location'),
                'available' => [ 'event' ]
            ],
            [
                'name' => 'external_website',
                'title' => _('External website'),
                'available' => [ 'event' ]
            ],
            [
                'name' => 'category',
                'title' => _('Subsection of'),
                'available' => [ 'category' ]
            ],
            [
                'name' => 'color',
                'title' => _('Color'),
                'available' => [ 'category' ]
            ],
            [
                'name' => 'logo',
                'title' => _('Logo'),
                'available' => [ 'category' ]
            ],
            [
                'name' => 'type',
                'title' => _('Type'),
                'available' => [ 'category' ]
            ],
            [
                'name' => 'visibility',
                'title' => _('Visibility'),
                'available' => [
                    'subscription',
                    'userGroup'
                    ]
            ],
            [
                'name' => 'request',
                'title' => _('Requests'),
                'available' => [ 'subscription' ]
            ],
            [
                'name' => 'user_groups',
                'title' => _('User groups'),
                'available' => [ 'user' ]
            ],
            [
                'name' => 'language',
                'title' => _('Language & time'),
                'available' => [ 'user' ]
            ],
            [
                'name' => 'featuredFrontpage',
                'title' => _('Featured in frontpage'),
                'available' => [
                    'article',
                    'opinion',
                    'event',
                    'video',
                    'album'
                ]
            ],
            [
                'name' => 'featuredInner',
                'title' => _('Featured in inner'),
                'available' => [
                    'article',
                    'opinion',
                    'event',
                    'poll'
                ]
            ],
            [
                'name' => 'relatedFrontpage',
                'title' => _('Related in frontpage'),
                'available' => [ 'article' ]
            ],
            [
                'name' => 'relatedInner',
                'title' => _('Related in inner'),
                'available' => [ 'article' ]
            ],
            [
                'name' => 'agency',
                'title' => _('Agency'),
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

        return array_filter($this->fields, function ($field) use ($module) {
            return in_array($module, $field['available']);
        });
    }
}
