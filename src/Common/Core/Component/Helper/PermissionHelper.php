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

class PermissionHelper
{
    /**
     * The list of available permissions.
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Initializes the PermissionsHelper.
     *
     * @param Security $security The Security service.
     */
    public function __construct($security)
    {
        $this->security = $security;
    }

    /**
     * Returns the list of available permissions.
     *
     * @return array The list of available permissions.
     */
    public function getAvailable() : array
    {
        $security = $this->security;

        return array_filter($this->getPermissions(), function ($a) use ($security) {
            return array_key_exists('enabled', $a) && $a['enabled'] === 1
                || $security->hasPermission('MASTER');
        });
    }

    /**
     * Returns a list of permissions groupd by module name.
     *
     * @return array The list of permissions groupd by module name.
     */
    public function getByModule() : array
    {
        $grouped = [];

        foreach ($this->getAvailable() as $permission) {
            if (!array_key_exists($permission['module'], $grouped)) {
                $grouped[$permission['module']] = [];
            }

            $grouped[$permission['module']][] = $permission;
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * Returns the list of permission names basing on the list of permissions
     * ids.
     *
     * @param array The list of permission ids.
     *
     * @return array The list of permission names.
     */
    public function getNames(array $ids) : array
    {
        $names       = [];
        $permissions = $this->getPermissions();

        foreach ($ids as $id) {
            if (array_key_exists($id, $permissions)) {
                $names[$id] = $permissions[$id]['name'];
            }
        }

        return $names;
    }

    /**
     * Returns the list of all permissions.
     *
     * @return array The list of permissions.
     */
    public function getPermissions() : array
    {
        return [
            1 => [
                'id'          => 1,
                'name'        => 'CATEGORY_ADMIN',
                'description' => _('List'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            2 => [
                'id'          => 2,
                'name'        => 'CATEGORY_AVAILABLE',
                'description' => _('Activate/deactivate'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            3 => [
                'id'          => 3,
                'name'        => 'CATEGORY_UPDATE',
                'description' => _('Edit'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            4 => [
                'id'          => 4,
                'name'        => 'CATEGORY_DELETE',
                'description' => _('Remove'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            5 => [
                'id'          => 5,
                'name'        => 'CATEGORY_CREATE',
                'description' => _('Create'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            6 => [
                'id'          => 6,
                'name'        => 'ARTICLE_ADMIN',
                'description' => _('List'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            7 => [
                'id'          => 7,
                'name'        => 'ARTICLE_FRONTPAGE',
                'description' => _('Manage frontpages'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            8 => [
                'id'          => 8,
                'name'        => 'ARTICLE_PENDINGS',
                'description' => _('List pending articles'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            9 => [
                'id'          => 9,
                'name'        => 'ARTICLE_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            10 => [
                'id'          => 10,
                'name'        => 'ARTICLE_UPDATE',
                'description' => _('Edit'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            11 => [
                'id'          => 11,
                'name'        => 'ARTICLE_DELETE',
                'description' => _('Delete'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            12 => [
                'id'          => 12,
                'name'        => 'ARTICLE_CREATE',
                'description' => _('Create'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            13 => [
                'id'          => 13,
                'name'        => 'ARTICLE_ARCHIVE',
                'description' => _('Arquive/unarquive'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            14 => [
                'id'          => 14,
                'name'        => 'ARTICLE_FAVORITE',
                'description' => _('Manager favorite flag'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            15 => [
                'id'          => 15,
                'name'        => 'ARTICLE_HOME',
                'description' => _('Manage home frontpage'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            16 => [
                'id'          => 16,
                'name'        => 'ARTICLE_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            17 => [
                'id'          => 17,
                'name'        => 'ARTICLE_ARCHIVE_ADMI',
                'description' => _('List articles in arquive'),
                'module'      => 'ARTICLE_MANAGER',
                'enabled'     => 1
            ],
            18 => [
                'id'          => 18,
                'name'        => 'ADVERTISEMENT_ADMIN',
                'description' => _('List'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            19 => [
                'id'          => 19,
                'name'        => 'ADVERTISEMENT_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            20 => [
                'id'          => 20,
                'name'        => 'ADVERTISEMENT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            21 => [
                'id'          => 21,
                'name'        => 'ADVERTISEMENT_DELETE',
                'description' => _('Delete'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            22 => [
                'id'          => 22,
                'name'        => 'ADVERTISEMENT_CREATE',
                'description' => _('Create'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            23 => [
                'id'          => 23,
                'name'        => 'ADVERTISEMENT_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            24 => [
                'id'          => 24,
                'name'        => 'ADVERTISEMENT_HOME',
                'description' => _('Manage advertisements for homepage'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            25 => [
                'id'          => 25,
                'name'        => 'ADVERTISEMENT_FAVORITE',
                'description' => _('Manage favorite flag'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            26 => [
                'id'          => 26,
                'name'        => 'OPINION_ADMIN',
                'description' => _('List'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            28 => [
                'id'          => 28,
                'name'        => 'OPINION_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            29 => [
                'id'          => 29,
                'name'        => 'OPINION_UPDATE',
                'description' => _('Edit'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            30 => [
                'id'          => 30,
                'name'        => 'OPINION_HOME',
                'description' => _('Administrate opinion widget'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            31 => [
                'id'          => 31,
                'name'        => 'OPINION_DELETE',
                'description' => _('Delete'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            32 => [
                'id'          => 32,
                'name'        => 'OPINION_CREATE',
                'description' => _('Create'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            33 => [
                'id'          => 33,
                'name'        => 'OPINION_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            34 => [
                'id'          => 34,
                'name'        => 'COMMENT_ADMIN',
                'description' => _('List'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            37 => [
                'id'          => 37,
                'name'        => 'COMMENT_AVAILABLE',
                'description' => _('Approve/reject'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            38 => [
                'id'          => 38,
                'name'        => 'COMMENT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            39 => [
                'id'          => 39,
                'name'        => 'COMMENT_DELETE',
                'description' => _('Delete'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            40 => [
                'id'          => 40,
                'name'        => 'COMMENT_CREATE',
                'description' => _('Create'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            42 => [
                'id'          => 42,
                'name'        => 'ALBUM_ADMIN',
                'description' => _('List'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            43 => [
                'id'          => 43,
                'name'        => 'ALBUM_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            44 => [
                'id'          => 44,
                'name'        => 'ALBUM_UPDATE',
                'description' => _('Edit'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            45 => [
                'id'          => 45,
                'name'        => 'ALBUM_DELETE',
                'description' => _('Delete'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            46 => [
                'id'          => 46,
                'name'        => 'ALBUM_CREATE',
                'description' => _('Create'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            47 => [
                'id'          => 47,
                'name'        => 'ALBUM_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            48 => [
                'id'          => 48,
                'name'        => 'VIDEO_ADMIN',
                'description' => _('List'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            49 => [
                'id'          => 49,
                'name'        => 'VIDEO_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            50 => [
                'id'          => 50,
                'name'        => 'VIDEO_UPDATE',
                'description' => _('Edit'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            51 => [
                'id'          => 51,
                'name'        => 'VIDEO_DELETE',
                'description' => _('Delete'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            52 => [
                'id'          => 52,
                'name'        => 'VIDEO_CREATE',
                'description' => _('Create'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            53 => [
                'id'          => 53,
                'name'        => 'VIDEO_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            60 => [
                'id'          => 60,
                'name'        => 'PHOTO_ADMIN',
                'description' => _('List'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            61 => [
                'id'          => 61,
                'name'        => 'PHOTO_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            62 => [
                'id'          => 62,
                'name'        => 'PHOTO_UPDATE',
                'description' => _('Edit'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            63 => [
                'id'          => 63,
                'name'        => 'PHOTO_DELETE',
                'description' => _('Delete'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            64 => [
                'id'          => 64,
                'name'        => 'PHOTO_CREATE',
                'description' => _('Create/upload'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            65 => [
                'id'          => 65,
                'name'        => 'PHOTO_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'IMAGE_MANAGER',
                'enabled'     => 1
            ],
            66 => [
                'id'          => 66,
                'name'        => 'STATIC_PAGE_ADMIN',
                'description' => _('List'),
                'module'      => 'STATIC_PAGES_MANAGER',
                'enabled'     => 1
            ],
            67 => [
                'id'          => 67,
                'name'        => 'STATIC_PAGE_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'STATIC_PAGES_MANAGER',
                'enabled'     => 1
            ],
            68 => [
                'id'          => 68,
                'name'        => 'STATIC_PAGE_UPDATE',
                'description' => _('Edit'),
                'module'      => 'STATIC_PAGES_MANAGER',
                'enabled'     => 1
            ],
            69 => [
                'id'          => 69,
                'name'        => 'STATIC_PAGE_DELETE',
                'description' => _('Delete'),
                'module'      => 'STATIC_PAGES_MANAGER',
                'enabled'     => 1
            ],
            70 => [
                'id'          => 70,
                'name'        => 'STATIC_PAGE_CREATE',
                'description' => _('Create'),
                'module'      => 'STATIC_PAGES_MANAGER',
                'enabled'     => 1
            ],
            71 => [
                'id'          => 71,
                'name'        => 'KIOSKO_ADMIN',
                'description' => _('List'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            72 => [
                'id'          => 72,
                'name'        => 'KIOSKO_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            73 => [
                'id'          => 73,
                'name'        => 'KIOSKO_UPDATE',
                'description' => _('Edit'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            74 => [
                'id'          => 74,
                'name'        => 'KIOSKO_DELETE',
                'description' => _('Delete'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            75 => [
                'id'          => 75,
                'name'        => 'KIOSKO_CREATE',
                'description' => _('Create'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            76 => [
                'id'          => 76,
                'name'        => 'KIOSKO_HOME',
                'description' => _('Manage frontpage'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            77 => [
                'id'          => 77,
                'name'        => 'POLL_ADMIN',
                'description' => _('List'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            78 => [
                'id'          => 78,
                'name'        => 'POLL_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            79 => [
                'id'          => 79,
                'name'        => 'POLL_UPDATE',
                'description' => _('Edit'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            80 => [
                'id'          => 80,
                'name'        => 'POLL_DELETE',
                'description' => _('Delete'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            81 => [
                'id'          => 81,
                'name'        => 'POLL_CREATE',
                'description' => _('Create'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            82 => [
                'id'          => 82,
                'name'        => 'AUTHOR_ADMIN',
                'description' => _('List'),
                'module'      => 'AUTHOR',
                'enabled'     => 1
            ],
            83 => [
                'id'          => 83,
                'name'        => 'AUTHOR_UPDATE',
                'description' => _('Edit'),
                'module'      => 'AUTHOR',
                'enabled'     => 1
            ],
            84 => [
                'id'          => 84,
                'name'        => 'AUTHOR_DELETE',
                'description' => _('Delete'),
                'module'      => 'AUTHOR',
                'enabled'     => 1
            ],
            85 => [
                'id'          => 85,
                'name'        => 'AUTHOR_CREATE',
                'description' => _('Create'),
                'module'      => 'AUTHOR',
                'enabled'     => 1
            ],
            86 => [
                'id'          => 86,
                'name'        => 'USER_ADMIN',
                'description' => _('List'),
                'module'      => 'USER_MANAGER',
                'enabled'     => 1
            ],
            87 => [
                'id'          => 87,
                'name'        => 'USER_UPDATE',
                'description' => _('Edit'),
                'module'      => 'USER_MANAGER',
                'enabled'     => 1
            ],
            88 => [
                'id'          => 88,
                'name'        => 'USER_DELETE',
                'description' => _('Delete'),
                'module'      => 'USER_MANAGER',
                'enabled'     => 1
            ],
            89 => [
                'id'          => 89,
                'name'        => 'USER_CREATE',
                'description' => _('Create'),
                'module'      => 'USER_MANAGER',
                'enabled'     => 1
            ],
            90 => [
                'id'          => 90,
                'name'        => 'KEYWORD_ADMIN',
                'description' => _('List'),
                'module'      => 'KEYWORD_MANAGER',
                'enabled'     => 1
            ],
            91 => [
                'id'          => 91,
                'name'        => 'KEYWORD_UPDATE',
                'description' => _('Edit'),
                'module'      => 'KEYWORD_MANAGER',
                'enabled'     => 1
            ],
            92 => [
                'id'          => 92,
                'name'        => 'KEYWORD_DELETE',
                'description' => _('Delete'),
                'module'      => 'KEYWORD_MANAGER',
                'enabled'     => 1
            ],
            93 => [
                'id'          => 93,
                'name'        => 'KEYWORD_CREATE',
                'description' => _('Create'),
                'module'      => 'KEYWORD_MANAGER',
                'enabled'     => 1
            ],
            95 => [
                'id'          => 95,
                'name'        => 'GROUP_ADMIN',
                'description' => _('List'),
                'module'      => 'USER_GROUP_MANAGER',
                'enabled'     => 1
            ],
            96 => [
                'id'          => 96,
                'name'        => 'GROUP_UPDATE',
                'description' => _('Edit'),
                'module'      => 'USER_GROUP_MANAGER',
                'enabled'     => 1
            ],
            97 => [
                'id'          => 97,
                'name'        => 'GROUP_DELETE',
                'description' => _('Delete'),
                'module'      => 'USER_GROUP_MANAGER',
                'enabled'     => 1
            ],
            99 => [
                'id'          => 99,
                'name'        => 'GROUP_CREATE',
                'description' => _('Create'),
                'module'      => 'USER_GROUP_MANAGER',
                'enabled'     => 1
            ],
            104 => [
                'id'          => 104,
                'name'        => 'ATTACHMENT_ADMIN',
                'description' => _('List'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            105 => [
                'id'          => 105,
                'name'        => 'ATTACHMENT_FRONTS',
                'description' => _('File Fronts'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            106 => [
                'id'          => 106,
                'name'        => 'ATTACHMENT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            107 => [
                'id'          => 107,
                'name'        => 'ATTACHMENT_DELETE',
                'description' => _('Delete'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            108 => [
                'id'          => 108,
                'name'        => 'ATTACHMENT_CREATE',
                'description' => _('Create'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            112 => [
                'id'          => 112,
                'name'        => 'NEWSLETTER_ADMIN',
                'description' => _('Manage Newsletter'),
                'module'      => 'NEWSLETTER_MANAGER',
                'enabled'     => 1
            ],
            113 => [
                'id'          => 113,
                'name'        => 'WEBPUSH_ADMIN',
                'description' => _('Manage Web Push notifications'),
                'module'      => 'es.openhost.module.webpush_notifications',
                'enabled'     => 1
            ],
            114 => [
                'id'          => 114,
                'name'        => 'CACHE_TPL_ADMIN',
                'description' => _('Manage caches'),
                'module'      => 'CACHE_MANAGER',
                'enabled'     => 1
            ],
            115 => [
                'id'          => 115,
                'name'        => 'SEARCH_ADMIN',
                'description' => _('Use search'),
                'module'      => 'ADVANCED_SEARCH',
                'enabled'     => 1
            ],
            116 => [
                'id'          => 116,
                'name'        => 'TRASH_ADMIN',
                'description' => _('List trashed elementes'),
                'module'      => 'TRASH_MANAGER',
                'enabled'     => 1
            ],
            117 => [
                'id'          => 117,
                'name'        => 'WIDGET_ADMIN',
                'description' => _('List'),
                'module'      => 'WIDGET_MANAGER',
                'enabled'     => 1
            ],
            118 => [
                'id'          => 118,
                'name'        => 'WIDGET_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'WIDGET_MANAGER',
                'enabled'     => 1
            ],
            119 => [
                'id'          => 119,
                'name'        => 'WIDGET_UPDATE',
                'description' => _('Edit'),
                'module'      => 'WIDGET_MANAGER',
                'enabled'     => 1
            ],
            120 => [
                'id'          => 120,
                'name'        => 'WIDGET_DELETE',
                'description' => _('Delete'),
                'module'      => 'WIDGET_MANAGER',
                'enabled'     => 1
            ],
            121 => [
                'id'          => 121,
                'name'        => 'WIDGET_CREATE',
                'description' => _('Create'),
                'module'      => 'WIDGET_MANAGER',
                'enabled'     => 1
            ],
            122 => [
                'id'          => 122,
                'name'        => 'MENU_ADMIN',
                'description' => _('List'),
                'module'      => 'MENU_MANAGER',
                'enabled'     => 1
            ],
            123 => [
                'id'          => 123,
                'name'        => 'MENU_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'MENU_MANAGER',
                'enabled'     => 1
            ],
            124 => [
                'id'          => 124,
                'name'        => 'MENU_UPDATE',
                'description' => _('Edit'),
                'module'      => 'MENU_MANAGER',
                'enabled'     => 1
            ],
            125 => [
                'id'          => 125,
                'name'        => 'IMPORT_ADMIN',
                'description' => _('Import news from agency'),
                'module'      => 'NEWS_AGENCY_IMPORTER',
                'enabled'     => 1
            ],
            132 => [
                'id'          => 132,
                'name'        => 'CONTENT_OTHER_UPDATE',
                'description' => _('Modify other users\'s content'),
                'module'      => 'CONTENT',
                'enabled'     => 1
            ],
            133 => [
                'id'          => 133,
                'name'        => 'CONTENT_OTHER_DELETE',
                'description' => _('Delete other users\'s content'),
                'module'      => 'CONTENT',
                'enabled'     => 1
            ],
            134 => [
                'id'          => 134,
                'name'        => 'ONM_SETTINGS',
                'description' => _('Configure system-wide settings'),
                'module'      => 'ONM',
                'enabled'     => 1
            ],
            135 => [
                'id'          => 135,
                'name'        => 'GROUP_CHANGE',
                'description' => _('Change the user group from one user'),
                'module'      => 'USER_GROUP_MANAGER',
                'enabled'     => 1
            ],
            155 => [
                'id'          => 155,
                'name'        => 'VIDEO_HOME',
                'description' => _('Manage frontpage'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            156 => [
                'id'          => 156,
                'name'        => 'VIDEO_FAVORITE',
                'description' => _('Manage favorite flag'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            157 => [
                'id'          => 157,
                'name'        => 'ALBUM_HOME',
                'description' => _('Manage frontpage'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            158 => [
                'id'          => 158,
                'name'        => 'ALBUM_FAVORITE',
                'description' => _('Manage favorite flag'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            159 => [
                'id'          => 159,
                'name'        => 'ALBUM_SETTINGS',
                'description' => _('Manage module setting'),
                'module'      => 'ALBUM_MANAGER',
                'enabled'     => 1
            ],
            160 => [
                'id'          => 160,
                'name'        => 'POLL_SETTINGS',
                'description' => _('Manage module setting'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            161 => [
                'id'          => 161,
                'name'        => 'OPINION_SETTINGS',
                'description' => _('Manage module setting'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            162 => [
                'id'          => 162,
                'name'        => 'CATEGORY_SETTINGS',
                'description' => _('Manage module settings'),
                'module'      => 'CATEGORY_MANAGER',
                'enabled'     => 1
            ],
            163 => [
                'id'          => 163,
                'name'        => 'VIDEO_SETTINGS',
                'description' => _('Manage module settings'),
                'module'      => 'VIDEO_MANAGER',
                'enabled'     => 1
            ],
            179 => [
                'id'          => 179,
                'name'        => 'MENU_CREATE',
                'description' => _('Create'),
                'module'      => 'MENU_MANAGER',
                'enabled'     => 1
            ],
            164 => [
                'id'          => 164,
                'name'        => 'MENU_DELETE',
                'description' => _('Delete'),
                'module'      => 'MENU_MANAGER',
                'enabled'     => 1
            ],
            166 => [
                'id'          => 166,
                'name'        => 'LETTER_TRASH',
                'description' => _('Send to trash and restore'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            167 => [
                'id'          => 167,
                'name'        => 'LETTER_DELETE',
                'description' => _('Delete'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            168 => [
                'id'          => 168,
                'name'        => 'LETTER_UPDATE',
                'description' => _('Edit'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            169 => [
                'id'          => 169,
                'name'        => 'LETTER_SETTINGS',
                'description' => _('Manage module settings'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            170 => [
                'id'          => 170,
                'name'        => 'LETTER_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            171 => [
                'id'          => 171,
                'name'        => 'LETTER_FAVORITE',
                'description' => _('Manage widget'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            172 => [
                'id'          => 172,
                'name'        => 'LETTER_CREATE',
                'description' => _('Create'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            173 => [
                'id'          => 173,
                'name'        => 'LETTER_ADMIN',
                'description' => _('List'),
                'module'      => 'LETTER_MANAGER',
                'enabled'     => 1
            ],
            174 => [
                'id'          => 174,
                'name'        => 'POLL_FAVORITE',
                'description' => _('Manage favourite flag'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            175 => [
                'id'          => 175,
                'name'        => 'POLL_HOME',
                'description' => _('Manage frontpage'),
                'module'      => 'POLL_MANAGER',
                'enabled'     => 1
            ],
            176 => [
                'id'          => 176,
                'name'        => 'PRESSCLIPPING_ADMIN',
                'description' => _('Manage Pressclipping module'),
                'module'      => 'es.openhost.module.pressclipping',
                'enabled'     => 1
            ],
            177 => [
                'id'          => 177,
                'name'        => 'IMPORT_NEWS_AGENCY_CONFIG',
                'description' => _('Config News Agency importer'),
                'module'      => 'IMPORT',
                'enabled'     => 1
            ],
            180 => [
                'id'          => 180,
                'name'        => 'INSTANCE_SYNC_ADMIN',
                'description' => _('Administer synchronization between Opennemas'),
                'module'      => 'SYNC_MANAGER',
                'enabled'     => 1
            ],
            182 => [
                'id'          => 182,
                'name'        => 'PAYWALL_ADMIN',
                'description' => _('Administer paywall'),
                'module'      => 'PAYWALL',
                'enabled'     => 1
            ],
            183 => [
                'id'          => 183,
                'name'        => 'ATTACHMENT_AVAILABLE',
                'description' => _('Publish/unpublish'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            184 => [
                'id'          => 184,
                'name'        => 'ATTACHMENT_HOME',
                'description' => _('Manage frontpage'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            185 => [
                'id'          => 185,
                'name'        => 'ATTACHMENT_FAVORITE',
                'description' => _('Manage favourite flag'),
                'module'      => 'FILE_MANAGER',
                'enabled'     => 1
            ],
            186 => [
                'id'          => 186,
                'name'        => 'KIOSKO_FAVORITE',
                'description' => _('Manage favourite flag'),
                'module'      => 'KIOSKO_MANAGER',
                'enabled'     => 1
            ],
            187 => [
                'id'          => 187,
                'name'        => 'OPINION_FAVORITE',
                'description' => _('Manage favourite flag'),
                'module'      => 'OPINION_MANAGER',
                'enabled'     => 1
            ],
            188 => [
                'id'          => 188,
                'name'        => 'USER_EDIT_OWN_PROFILE',
                'description' => _('Edit user own profile'),
                'module'      => 'USER_MANAGER',
                'enabled'     => 1
            ],
            189 => [
                'id'          => 189,
                'name'        => 'ADVERTISEMENT_SETTINGS',
                'description' => _('Manage module settings'),
                'module'      => 'ADS_MANAGER',
                'enabled'     => 1
            ],
            // All existing permissions
            190 => [
                'id'          => 190,
                'name'        => 'MASTER',
                'description' => _('Authenticate as a MASTER'),
                'module'      => 'SECURITY',
                'enabled'     => 0
            ],
            // All existing permissions for owned instances and some edition
            // permissions in manager
            191 => [
                'id'          => 191,
                'name'        => 'PARTNER',
                'description' => _('Authenticate as a PARTNER'),
                'module'      => 'SECURITY',
                'enabled'     => 0
            ],
            // All permissions for active extensions in instance
            192 => [
                'id'          => 192,
                'name'        => 'ADMIN',
                'description' => _('Authenticate as an ADMINISTRATOR'),
                'module'      => 'SECURITY',
                'enabled'     => 1
            ],
            193 => [
                'id'          => 193,
                'name'        => 'INSTANCE_LIST',
                'description' => _('List'),
                'module'      => 'INSTANCE',
                'enabled'     => 1
            ],
            194 => [
                'id'          => 194,
                'name'        => 'INSTANCE_CREATE',
                'description' => _('Create'),
                'module'      => 'INSTANCE',
                'enabled'     => 1
            ],
            195 => [
                'id'          => 195,
                'name'        => 'INSTANCE_UPDATE',
                'description' => _('Edit'),
                'module'      => 'INSTANCE',
                'enabled'     => 1
            ],
            196 => [
                'id'          => 196,
                'name'        => 'INSTANCE_DELETE',
                'description' => _('Delete'),
                'module'      => 'INSTANCE',
                'enabled'     => 1
            ],
            197 => [
                'id'          => 197,
                'name'        => 'INSTANCE_REPORT',
                'description' => _('Report'),
                'module'      => 'INSTANCE',
                'enabled'     => 1
            ],
            198 => [
                'id'          => 198,
                'name'        => 'EXTENSION_LIST',
                'description' => _('List'),
                'module'      => 'EXTENSION',
                'enabled'     => 1
            ],
            199 => [
                'id'          => 199,
                'name'        => 'EXTENSION_CREATE',
                'description' => _('Create'),
                'module'      => 'EXTENSION',
                'enabled'     => 1
            ],
            200 => [
                'id'          => 200,
                'name'        => 'EXTENSION_UPDATE',
                'description' => _('Edit'),
                'module'      => 'EXTENSION',
                'enabled'     => 1
            ],
            201 => [
                'id'          => 201,
                'name'        => 'EXTENSION_DELETE',
                'description' => _('Delete'),
                'module'      => 'EXTENSION',
                'enabled'     => 1
            ],
            202 => [
                'id'          => 202,
                'name'        => 'EXTENSION_REPORT',
                'description' => _('Report'),
                'module'      => 'EXTENSION',
                'enabled'     => 1
            ],
            203 => [
                'id'          => 203,
                'name'        => 'NOTIFICATION_LIST',
                'description' => _('List'),
                'module'      => 'NOTIFICATION',
                'enabled'     => 1
            ],
            204 => [
                'id'          => 204,
                'name'        => 'NOTIFICATION_CREATE',
                'description' => _('Create'),
                'module'      => 'NOTIFICATION',
                'enabled'     => 1
            ],
            205 => [
                'id'          => 205,
                'name'        => 'NOTIFICATION_UPDATE',
                'description' => _('Edit'),
                'module'      => 'NOTIFICATION',
                'enabled'     => 1
            ],
            206 => [
                'id'          => 206,
                'name'        => 'NOTIFICATION_DELETE',
                'description' => _('Delete'),
                'module'      => 'NOTIFICATION',
                'enabled'     => 1
            ],
            207 => [
                'id'          => 207,
                'name'        => 'NOTIFICATION_REPORT',
                'description' => _('Report'),
                'module'      => 'NOTIFICATION',
                'enabled'     => 1
            ],
            208 => [
                'id'          => 208,
                'name'        => 'CLIENT_LIST',
                'description' => _('List'),
                'module'      => 'CLIENT',
                'enabled'     => 1
            ],
            209 => [
                'id'          => 209,
                'name'        => 'CLIENT_CREATE',
                'description' => _('Create'),
                'module'      => 'CLIENT',
                'enabled'     => 1
            ],
            210 => [
                'id'          => 210,
                'name'        => 'CLIENT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'CLIENT',
                'enabled'     => 1
            ],
            211 => [
                'id'          => 211,
                'name'        => 'CLIENT_DELETE',
                'description' => _('Delete'),
                'module'      => 'CLIENT',
                'enabled'     => 1
            ],
            212 => [
                'id'          => 212,
                'name'        => 'CLIENT_REPORT',
                'description' => _('Report'),
                'module'      => 'CLIENT',
                'enabled'     => 1
            ],
            213 => [
                'id'          => 213,
                'name'        => 'PURCHASE_LIST',
                'description' => _('List'),
                'module'      => 'PURCHASE',
                'enabled'     => 1
            ],
            214 => [
                'id'          => 214,
                'name'        => 'PURCHASE_CREATE',
                'description' => _('Create'),
                'module'      => 'PURCHASE',
                'enabled'     => 1
            ],
            215 => [
                'id'          => 215,
                'name'        => 'PURCHASE_UPDATE',
                'description' => _('Edit'),
                'module'      => 'PURCHASE',
                'enabled'     => 1
            ],
            216 => [
                'id'          => 216,
                'name'        => 'PURCHASE_DELETE',
                'description' => _('Delete'),
                'module'      => 'PURCHASE',
                'enabled'     => 1
            ],
            217 => [
                'id'          => 217,
                'name'        => 'PURCHASE_REPORT',
                'description' => _('Report'),
                'module'      => 'PURCHASE',
                'enabled'     => 1
            ],
            218 => [
                'id'          => 218,
                'name'        => 'REPORT_LIST',
                'description' => _('List'),
                'module'      => 'REPORT',
                'enabled'     => 1
            ],
            219 => [
                'id'          => 219,
                'name'        => 'REPORT_DOWNLOAD',
                'description' => _('Download'),
                'module'      => 'REPORT',
                'enabled'     => 1
            ],
            220 => [
                'id'          => 220,
                'name'        => 'COMMAND_LIST',
                'description' => _('List'),
                'module'      => 'COMMAND',
                'enabled'     => 1
            ],
            221 => [
                'id'          => 221,
                'name'        => 'COMMAND_EXECUTE',
                'description' => _('Execute'),
                'module'      => 'COMMAND',
                'enabled'     => 1
            ],
            222 => [
                'id'          => 222,
                'name'        => 'OPCACHE_LIST',
                'description' => _('List'),
                'module'      => 'OPCACHE',
                'enabled'     => 1
            ],
            223 => [
                'id'          => 223,
                'name'        => 'GROUP_PUBLIC',
                'description' => _('Marks this group as public'),
                'module'      => 'INTERNAL',
                'enabled'     => 1
            ],
            224 => [
                'id'          => 224,
                'name'        => 'MEMBER_SEND_NEWSLETTER',
                'description' => _('Send newsletter'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            225 => [
                'id'          => 225,
                'name'        => 'MEMBER_HIDE_ADVERTISEMENTS',
                'description' => _('Hide advertisements'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            226 => [
                'id'          => 226,
                'name'        => 'MEMBER_REQUIRES_PAYMENT',
                'description' => _('Requires payment'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            227 => [
                'id'          => 227,
                'name'        => 'MEMBER_HIDE_PRINT',
                'description' => _('Hide print button'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            228 => [
                'id'          => 228,
                'name'        => 'MEMBER_HIDE_SOCIAL',
                'description' => _('Hide social networks buttons'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            229 => [
                'id'          => 229,
                'name'        => 'MEMBER_BLOCK_BROWSER',
                'description' => _('Block browser actions (cut, copy,...)'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            230 => [
                'id'          => 230,
                'name'        => 'NON_MEMBER_BLOCK_ACCESS',
                'description' => _('Block access to content'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            232 => [
                'id'          => 232,
                'name'        => 'NON_MEMBER_HIDE_SUMMARY',
                'description' => _('Hide summary'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            233 => [
                'id'          => 233,
                'name'        => 'NON_MEMBER_HIDE_BODY',
                'description' => _('Hide body'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            234 => [
                'id'          => 234,
                'name'        => 'NON_MEMBER_HIDE_PRETITLE',
                'description' => _('Hide pretitle'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            235 => [
                'id'          => 235,
                'name'        => 'NON_MEMBER_HIDE_MEDIA',
                'description' => _('Hide media'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            236 => [
                'id'          => 236,
                'name'        => 'NON_MEMBER_HIDE_RELATED_CONTENTS',
                'description' => _('Hide related contents'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            237 => [
                'id'          => 237,
                'name'        => 'NON_MEMBER_HIDE_INFO',
                'description' => _('Hide content information'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            238 => [
                'id'          => 238,
                'name'        => 'NON_MEMBER_HIDE_TAGS',
                'description' => _('Hide tags'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            239 => [
                'id'          => 239,
                'name'        => 'NON_MEMBER_HIDE_PRINT',
                'description' => _('Hide print button'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            240 => [
                'id'          => 240,
                'name'        => 'NON_MEMBER_HIDE_SOCIAL',
                'description' => _('Hide social networks buttons'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            241 => [
                'id'          => 241,
                'name'        => 'NON_MEMBER_BLOCK_BROWSER',
                'description' => _('Block browser actions (cut, copy,...)'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            242 => [
                'id'          => 242,
                'name'        => 'NON_MEMBER_NO_INDEX',
                'description' => _('Prevent search engine indexation'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            243 => [
                'id'          => 243,
                'name'        => 'TAG_ADMIN',
                'description' => _('List'),
                'module'      => 'es.openhost.module.tags',
                'enabled'     => 1
            ],
            244 => [
                'id'          => 244,
                'name'        => 'TAG_UPDATE',
                'description' => _('Edit'),
                'module'      => 'es.openhost.module.tags',
                'enabled'     => 1
            ],
            245 => [
                'id'          => 245,
                'name'        => 'TAG_DELETE',
                'description' => _('Delete'),
                'module'      => 'es.openhost.module.tags',
                'enabled'     => 1
            ],
            246 => [
                'id'          => 246,
                'name'        => 'TAG_CREATE',
                'description' => _('Create'),
                'module'      => 'es.openhost.module.tags',
                'enabled'     => 1
            ],
            247 => [
                'id'          => 247,
                'name'        => 'EVENT_ADMIN',
                'description' => _('List'),
                'module'      => 'es.openhost.module.events',
                'enabled'     => 1
            ],
            248 => [
                'id'          => 248,
                'name'        => 'EVENT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'es.openhost.module.events',
                'enabled'     => 1
            ],
            249 => [
                'id'          => 249,
                'name'        => 'EVENT_DELETE',
                'description' => _('Delete'),
                'module'      => 'es.openhost.module.events',
                'enabled'     => 1
            ],
            250 => [
                'id'          => 250,
                'name'        => 'EVENT_CREATE',
                'description' => _('Create'),
                'module'      => 'es.openhost.module.events',
                'enabled'     => 1
            ],
            251 => [
                'id'          => 251,
                'name'        => 'NON_MEMBER_HIDE_ADVERTISEMENTS',
                'description' => _('Hide advertisements'),
                'module'      => 'FRONTEND',
                'enabled'     => 1
            ],
            252 => [
                'id'          => 252,
                'name'        => 'COMMENT_SETTINGS',
                'description' => _('Manage module setting'),
                'module'      => 'COMMENT_MANAGER',
                'enabled'     => 1
            ],
            253 => [
                'id'          => 253,
                'name'        => 'OBITUARY_ADMIN',
                'description' => _('List'),
                'module'      => 'es.openhost.module.obituaries',
                'enabled'     => 1
            ],
            254 => [
                'id'          => 254,
                'name'        => 'OBITUARY_UPDATE',
                'description' => _('Edit'),
                'module'      => 'es.openhost.module.obituaries',
                'enabled'     => 1
            ],
            255 => [
                'id'          => 255,
                'name'        => 'OBITUARY_DELETE',
                'description' => _('Delete'),
                'module'      => 'es.openhost.module.obituaries',
                'enabled'     => 1
            ],
            256 => [
                'id'          => 256,
                'name'        => 'OBITUARY_CREATE',
                'description' => _('Create'),
                'module'      => 'es.openhost.module.obituaries',
                'enabled'     => 1
            ],
            257 => [
                'id'          => 257,
                'name'        => 'COMPANY_ADMIN',
                'description' => _('List'),
                'module'      => 'es.openhost.module.companies',
                'enabled'     => 1
            ],
            258 => [
                'id'          => 258,
                'name'        => 'COMPANY_UPDATE',
                'description' => _('Edit'),
                'module'      => 'es.openhost.module.companies',
                'enabled'     => 1
            ],
            259 => [
                'id'          => 259,
                'name'        => 'COMPANY_DELETE',
                'description' => _('Delete'),
                'module'      => 'es.openhost.module.companies',
                'enabled'     => 1
            ],
            260 => [
                'id'          => 260,
                'name'        => 'COMPANY_CREATE',
                'description' => _('Create'),
                'module'      => 'es.openhost.module.companies',
                'enabled'     => 1
            ],
            261 => [
                'id'          => 261,
                'name'        => 'COMPANY_CONFIG',
                'description' => _('Configuration'),
                'module'      => 'es.openhost.module.companies',
                'enabled'     => 1
            ],
            262 => [
                'id'          => 262,
                'name'        => 'EVENT_SETTINGS',
                'description' => _('Manage module setting'),
                'module'      => 'es.openhost.module.events',
                'enabled'     => 1
            ],
            263 => [
                'id'          => 263,
                'name'        => 'PROMPT_ADMIN',
                'description' => _('List'),
                'module'      => 'es.openhost.module.openai',
                'enabled'     => 1
            ],
            264 => [
                'id'          => 264,
                'name'        => 'PROMPT_UPDATE',
                'description' => _('Edit'),
                'module'      => 'es.openhost.module.openai',
                'enabled'     => 1
            ],
            265 => [
                'id'          => 265,
                'name'        => 'PROMPT_DELETE',
                'description' => _('Delete'),
                'module'      => 'es.openhost.module.openai',
                'enabled'     => 1
            ],
            266 => [
                'id'          => 266,
                'name'        => 'PROMPT_CREATE',
                'description' => _('Create'),
                'module'      => 'es.openhost.module.openai',
                'enabled'     => 1
            ]
        ];
    }
}
