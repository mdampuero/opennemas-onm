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
        $this->security    = $security;
        $this->permissions = [
            1 => [
                'pk_permission' => 1,
                'name'         => 'CATEGORY_ADMIN',
                'description'  => _('List'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            2 => [
                'pk_permission' => 2,
                'name'         => 'CATEGORY_AVAILABLE',
                'description'  => _('Activate/deactivate'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            3 => [
                'pk_permission' => 3,
                'name'         => 'CATEGORY_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            4 => [
                'pk_permission' => 4,
                'name'         => 'CATEGORY_DELETE',
                'description'  => _('Remove'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            5 => [
                'pk_permission' => 5,
                'name'         => 'CATEGORY_CREATE',
                'description'  => _('Create'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            6 => [
                'pk_permission' => 6,
                'name'         => 'ARTICLE_ADMIN',
                'description'  => _('List'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            7 => [
                'pk_permission' => 7,
                'name'         => 'ARTICLE_FRONTPAGE',
                'description'  => _('Manage frontpages'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            8 => [
                'pk_permission' => 8,
                'name'         => 'ARTICLE_PENDINGS',
                'description'  => _('List pending articles'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            9 => [
                'pk_permission' => 9,
                'name'         => 'ARTICLE_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            10 => [
                'pk_permission' => 10,
                'name'         => 'ARTICLE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            11 => [
                'pk_permission' => 11,
                'name'         => 'ARTICLE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            12 => [
                'pk_permission' => 12,
                'name'         => 'ARTICLE_CREATE',
                'description'  => _('Create'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            13 => [
                'pk_permission' => 13,
                'name'         => 'ARTICLE_ARCHIVE',
                'description'  => _('Arquive/unarquive'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            14 => [
                'pk_permission' => 14,
                'name'         => 'ARTICLE_FAVORITE',
                'description'  => _('Manager favorite flag'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            15 => [
                'pk_permission' => 15,
                'name'         => 'ARTICLE_HOME',
                'description'  => _('Manage home frontpage'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            16 => [
                'pk_permission' => 16,
                'name'         => 'ARTICLE_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            17 => [
                'pk_permission' => 17,
                'name'         => 'ARTICLE_ARCHIVE_ADMI',
                'description'  => _('List articles in arquive'),
                'module'       => 'ARTICLE_MANAGER',
                'enabled'      => 1
            ],
            18 => [
                'pk_permission' => 18,
                'name'         => 'ADVERTISEMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            19 => [
                'pk_permission' => 19,
                'name'         => 'ADVERTISEMENT_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            20 => [
                'pk_permission' => 20,
                'name'         => 'ADVERTISEMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            21 => [
                'pk_permission' => 21,
                'name'         => 'ADVERTISEMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            22 => [
                'pk_permission' => 22,
                'name'         => 'ADVERTISEMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            23 => [
                'pk_permission' => 23,
                'name'         => 'ADVERTISEMENT_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            24 => [
                'pk_permission' => 24,
                'name'         => 'ADVERTISEMENT_HOME',
                'description'  => _('Manage advertisements for homepage'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            25 => [
                'pk_permission' => 25,
                'name'         => 'ADVERTISEMENT_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            26 => [
                'pk_permission' => 26,
                'name'         => 'OPINION_ADMIN',
                'description'  => _('List'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            28 => [
                'pk_permission' => 28,
                'name'         => 'OPINION_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            29 => [
                'pk_permission' => 29,
                'name'         => 'OPINION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            30 => [
                'pk_permission' => 30,
                'name'         => 'OPINION_HOME',
                'description'  => _('Administrate opinion widget'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            31 => [
                'pk_permission' => 31,
                'name'         => 'OPINION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            32 => [
                'pk_permission' => 32,
                'name'         => 'OPINION_CREATE',
                'description'  => _('Create'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            33 => [
                'pk_permission' => 33,
                'name'         => 'OPINION_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            34 => [
                'pk_permission' => 34,
                'name'         => 'COMMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            35 => [
                'pk_permission' => 35,
                'name'         => 'COMMENT_POLL',
                'description'  => _('Manage poll comments'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            37 => [
                'pk_permission' => 37,
                'name'         => 'COMMENT_AVAILABLE',
                'description'  => _('Approve/reject'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            38 => [
                'pk_permission' => 38,
                'name'         => 'COMMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            39 => [
                'pk_permission' => 39,
                'name'         => 'COMMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            40 => [
                'pk_permission' => 40,
                'name'         => 'COMMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            41 => [
                'pk_permission' => 41,
                'name'         => 'COMMENT_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'COMMENT_MANAGER',
                'enabled'      => 1
            ],
            42 => [
                'pk_permission' => 42,
                'name'         => 'ALBUM_ADMIN',
                'description'  => _('List'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            43 => [
                'pk_permission' => 43,
                'name'         => 'ALBUM_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            44 => [
                'pk_permission' => 44,
                'name'         => 'ALBUM_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            45 => [
                'pk_permission' => 45,
                'name'         => 'ALBUM_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            46 => [
                'pk_permission' => 46,
                'name'         => 'ALBUM_CREATE',
                'description'  => _('Create'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            47 => [
                'pk_permission' => 47,
                'name'         => 'ALBUM_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            48 => [
                'pk_permission' => 48,
                'name'         => 'VIDEO_ADMIN',
                'description'  => _('List'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            49 => [
                'pk_permission' => 49,
                'name'         => 'VIDEO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            50 => [
                'pk_permission' => 50,
                'name'         => 'VIDEO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            51 => [
                'pk_permission' => 51,
                'name'         => 'VIDEO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            52 => [
                'pk_permission' => 52,
                'name'         => 'VIDEO_CREATE',
                'description'  => _('Create'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            53 => [
                'pk_permission' => 53,
                'name'         => 'VIDEO_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            60 => [
                'pk_permission' => 60,
                'name'         => 'PHOTO_ADMIN',
                'description'  => _('List'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            61 => [
                'pk_permission' => 61,
                'name'         => 'PHOTO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            62 => [
                'pk_permission' => 62,
                'name'         => 'PHOTO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            63 => [
                'pk_permission' => 63,
                'name'         => 'PHOTO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            64 => [
                'pk_permission' => 64,
                'name'         => 'PHOTO_CREATE',
                'description'  => _('Create/upload'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            65 => [
                'pk_permission' => 65,
                'name'         => 'PHOTO_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'IMAGE_MANAGER',
                'enabled'      => 1
            ],
            66 => [
                'pk_permission' => 66,
                'name'         => 'STATIC_PAGE_ADMIN',
                'description'  => _('List'),
                'module'       => 'STATIC_PAGES_MANAGER',
                'enabled'      => 1
            ],
            67 => [
                'pk_permission' => 67,
                'name'         => 'STATIC_PAGE_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'STATIC_PAGES_MANAGER',
                'enabled'      => 1
            ],
            68 => [
                'pk_permission' => 68,
                'name'         => 'STATIC_PAGE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'STATIC_PAGES_MANAGER',
                'enabled'      => 1
            ],
            69 => [
                'pk_permission' => 69,
                'name'         => 'STATIC_PAGE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'STATIC_PAGES_MANAGER',
                'enabled'      => 1
            ],
            70 => [
                'pk_permission' => 70,
                'name'         => 'STATIC_PAGE_CREATE',
                'description'  => _('Create'),
                'module'       => 'STATIC_PAGES_MANAGER',
                'enabled'      => 1
            ],
            71 => [
                'pk_permission' => 71,
                'name'         => 'KIOSKO_ADMIN',
                'description'  => _('List'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            72 => [
                'pk_permission' => 72,
                'name'         => 'KIOSKO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            73 => [
                'pk_permission' => 73,
                'name'         => 'KIOSKO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            74 => [
                'pk_permission' => 74,
                'name'         => 'KIOSKO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            75 => [
                'pk_permission' => 75,
                'name'         => 'KIOSKO_CREATE',
                'description'  => _('Create'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            76 => [
                'pk_permission' => 76,
                'name'         => 'KIOSKO_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            77 => [
                'pk_permission' => 77,
                'name'         => 'POLL_ADMIN',
                'description'  => _('List'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            78 => [
                'pk_permission' => 78,
                'name'         => 'POLL_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            79 => [
                'pk_permission' => 79,
                'name'         => 'POLL_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            80 => [
                'pk_permission' => 80,
                'name'         => 'POLL_DELETE',
                'description'  => _('Delete'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            81 => [
                'pk_permission' => 81,
                'name'         => 'POLL_CREATE',
                'description'  => _('Create'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            82 => [
                'pk_permission' => 82,
                'name'         => 'AUTHOR_ADMIN',
                'description'  => _('List'),
                'module'       => 'AUTHOR',
                'enabled'      => 1
            ],
            83 => [
                'pk_permission' => 83,
                'name'         => 'AUTHOR_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'AUTHOR',
                'enabled'      => 1
            ],
            84 => [
                'pk_permission' => 84,
                'name'         => 'AUTHOR_DELETE',
                'description'  => _('Delete'),
                'module'       => 'AUTHOR',
                'enabled'      => 1
            ],
            85 => [
                'pk_permission' => 85,
                'name'         => 'AUTHOR_CREATE',
                'description'  => _('Create'),
                'module'       => 'AUTHOR',
                'enabled'      => 1
            ],
            86 => [
                'pk_permission' => 86,
                'name'         => 'USER_ADMIN',
                'description'  => _('List'),
                'module'       => 'USER_MANAGER',
                'enabled'      => 1
            ],
            87 => [
                'pk_permission' => 87,
                'name'         => 'USER_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'USER_MANAGER',
                'enabled'      => 1
            ],
            88 => [
                'pk_permission' => 88,
                'name'         => 'USER_DELETE',
                'description'  => _('Delete'),
                'module'       => 'USER_MANAGER',
                'enabled'      => 1
            ],
            89 => [
                'pk_permission' => 89,
                'name'         => 'USER_CREATE',
                'description'  => _('Create'),
                'module'       => 'USER_MANAGER',
                'enabled'      => 1
            ],
            90 => [
                'pk_permission' => 90,
                'name'         => 'PCLAVE_ADMIN',
                'description'  => _('List'),
                'module'       => 'KEYWORD_MANAGER',
                'enabled'      => 1
            ],
            91 => [
                'pk_permission' => 91,
                'name'         => 'PCLAVE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'KEYWORD_MANAGER',
                'enabled'      => 1
            ],
            92 => [
                'pk_permission' => 92,
                'name'         => 'PCLAVE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'KEYWORD_MANAGER',
                'enabled'      => 1
            ],
            93 => [
                'pk_permission' => 93,
                'name'         => 'PCLAVE_CREATE',
                'description'  => _('Create'),
                'module'       => 'KEYWORD_MANAGER',
                'enabled'      => 1
            ],
            95 => [
                'pk_permission' => 95,
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List'),
                'module'       => 'USER_GROUP_MANAGER',
                'enabled'      => 1
            ],
            96 => [
                'pk_permission' => 96,
                'name'         => 'GROUP_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'USER_GROUP_MANAGER',
                'enabled'      => 1
            ],
            97 => [
                'pk_permission' => 97,
                'name'         => 'GROUP_DELETE',
                'description'  => _('Delete'),
                'module'       => 'USER_GROUP_MANAGER',
                'enabled'      => 1
            ],
            99 => [
                'pk_permission' => 99,
                'name'         => 'GROUP_CREATE',
                'description'  => _('Create'),
                'module'       => 'USER_GROUP_MANAGER',
                'enabled'      => 1
            ],
            104 => [
                'pk_permission' => 104,
                'name'         => 'ATTACHMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            105 => [
                'pk_permission' => 105,
                'name'         => 'ATTACHMENT_FRONTS',
                'description'  => _('File Fronts'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            106 => [
                'pk_permission' => 106,
                'name'         => 'ATTACHMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            107 => [
                'pk_permission' => 107,
                'name'         => 'ATTACHMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            108 => [
                'pk_permission' => 108,
                'name'         => 'ATTACHMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            112 => [
                'pk_permission' => 112,
                'name'         => 'NEWSLETTER_ADMIN',
                'description'  => _('Manage Newsletter'),
                'module'       => 'NEWSLETTER_MANAGER',
                'enabled'      => 1
            ],
            114 => [
                'pk_permission' => 114,
                'name'         => 'CACHE_TPL_ADMIN',
                'description'  => _('Manage caches'),
                'module'       => 'CACHE_MANAGER',
                'enabled'      => 1
            ],
            115 => [
                'pk_permission' => 115,
                'name'         => 'SEARCH_ADMIN',
                'description'  => _('Use search'),
                'module'       => 'ADVANCED_SEARCH',
                'enabled'      => 1
            ],
            116 => [
                'pk_permission' => 116,
                'name'         => 'TRASH_ADMIN',
                'description'  => _('List trashed elementes'),
                'module'       => 'TRASH_MANAGER',
                'enabled'      => 1
            ],
            117 => [
                'pk_permission' => 117,
                'name'         => 'WIDGET_ADMIN',
                'description'  => _('List'),
                'module'       => 'WIDGET_MANAGER',
                'enabled'      => 1
            ],
            118 => [
                'pk_permission' => 118,
                'name'         => 'WIDGET_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'WIDGET_MANAGER',
                'enabled'      => 1
            ],
            119 => [
                'pk_permission' => 119,
                'name'         => 'WIDGET_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'WIDGET_MANAGER',
                'enabled'      => 1
            ],
            120 => [
                'pk_permission' => 120,
                'name'         => 'WIDGET_DELETE',
                'description'  => _('Delete'),
                'module'       => 'WIDGET_MANAGER',
                'enabled'      => 1
            ],
            121 => [
                'pk_permission' => 121,
                'name'         => 'WIDGET_CREATE',
                'description'  => _('Create'),
                'module'       => 'WIDGET_MANAGER',
                'enabled'      => 1
            ],
            122 => [
                'pk_permission' => 122,
                'name'         => 'MENU_ADMIN',
                'description'  => _('List'),
                'module'       => 'MENU_MANAGER',
                'enabled'      => 1
            ],
            123 => [
                'pk_permission' => 123,
                'name'         => 'MENU_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'MENU_MANAGER',
                'enabled'      => 1
            ],
            124 => [
                'pk_permission' => 124,
                'name'         => 'MENU_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'MENU_MANAGER',
                'enabled'      => 1
            ],
            125 => [
                'pk_permission' => 125,
                'name'         => 'IMPORT_ADMIN',
                'description'  => _('Import news from agency'),
                'module'       => 'NEWS_AGENCY_IMPORTER',
                'enabled'      => 1
            ],
            132 => [
                'pk_permission' => 132,
                'name'         => 'CONTENT_OTHER_UPDATE',
                'description'  => _('Modify other users\'s content'),
                'module'       => 'CONTENT',
                'enabled'      => 1
            ],
            133 => [
                'pk_permission' => 133,
                'name'         => 'CONTENT_OTHER_DELETE',
                'description'  => _('Delete other users\'s content'),
                'module'       => 'CONTENT',
                'enabled'      => 1
            ],
            134 => [
                'pk_permission' => 134,
                'name'         => 'ONM_SETTINGS',
                'description'  => _('Configure system-wide settings'),
                'module'       => 'ONM',
                'enabled'      => 1
            ],
            135 => [
                'pk_permission' => 135,
                'name'         => 'GROUP_CHANGE',
                'description'  => _('Change the user group from one user'),
                'module'       => 'USER_GROUP_MANAGER',
                'enabled'      => 1
            ],
            137 => [
                'pk_permission' => 137,
                'name'         => 'BOOK_ADMIN',
                'description'  => _('List'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            138 => [
                'pk_permission' => 138,
                'name'         => 'BOOK_CREATE',
                'description'  => _('Create'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            139 => [
                'pk_permission' => 139,
                'name'         => 'BOOK_HOME',
                'description'  => _('Manage widget'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            140 => [
                'pk_permission' => 140,
                'name'         => 'BOOK_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            141 => [
                'pk_permission' => 141,
                'name'         => 'BOOK_SETTINGS',
                'description'  => _('Administrate settings'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            142 => [
                'pk_permission' => 142,
                'name'         => 'BOOK_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            143 => [
                'pk_permission' => 143,
                'name'         => 'BOOK_DELETE',
                'description'  => _('Delete'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            144 => [
                'pk_permission' => 144,
                'name'         => 'BOOK_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'BOOK_MANAGER',
                'enabled'      => 1
            ],
            145 => [
                'pk_permission' => 145,
                'name'         => 'SPECIAL_ADMIN',
                'description'  => _('List'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            146 => [
                'pk_permission' => 146,
                'name'         => 'SPECIAL_CREATE',
                'description'  => _('Create'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            147 => [
                'pk_permission' => 147,
                'name'         => 'SPECIAL_FAVORITE',
                'description'  => _('Manage widget'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            148 => [
                'pk_permission' => 148,
                'name'         => 'SPECIAL_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            149 => [
                'pk_permission' => 149,
                'name'         => 'SPECIAL_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            150 => [
                'pk_permission' => 150,
                'name'         => 'SPECIAL_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            151 => [
                'pk_permission' => 151,
                'name'         => 'SPECIAL_DELETE',
                'description'  => _('Delete'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            152 => [
                'pk_permission' => 152,
                'name'         => 'SPECIAL_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            155 => [
                'pk_permission' => 155,
                'name'         => 'VIDEO_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            156 => [
                'pk_permission' => 156,
                'name'         => 'VIDEO_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            157 => [
                'pk_permission' => 157,
                'name'         => 'ALBUM_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            158 => [
                'pk_permission' => 158,
                'name'         => 'ALBUM_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            159 => [
                'pk_permission' => 159,
                'name'         => 'ALBUM_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'ALBUM_MANAGER',
                'enabled'      => 1
            ],
            160 => [
                'pk_permission' => 160,
                'name'         => 'POLL_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            161 => [
                'pk_permission' => 161,
                'name'         => 'OPINION_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            162 => [
                'pk_permission' => 162,
                'name'         => 'CATEGORY_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'CATEGORY_MANAGER',
                'enabled'      => 1
            ],
            163 => [
                'pk_permission' => 163,
                'name'         => 'VIDEO_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'VIDEO_MANAGER',
                'enabled'      => 1
            ],
            179 => [
                'pk_permission' => 179,
                'name'         => 'MENU_CREATE',
                'description'  => _('Create'),
                'module'       => 'MENU_MANAGER',
                'enabled'      => 1
            ],
            164 => [
                'pk_permission' => 164,
                'name'         => 'MENU_DELETE',
                'description'  => _('Delete'),
                'module'       => 'MENU_MANAGER',
                'enabled'      => 1
            ],
            166 => [
                'pk_permission' => 166,
                'name'         => 'LETTER_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            167 => [
                'pk_permission' => 167,
                'name'         => 'LETTER_DELETE',
                'description'  => _('Delete'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            168 => [
                'pk_permission' => 168,
                'name'         => 'LETTER_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            169 => [
                'pk_permission' => 169,
                'name'         => 'LETTER_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            170 => [
                'pk_permission' => 170,
                'name'         => 'LETTER_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            171 => [
                'pk_permission' => 171,
                'name'         => 'LETTER_FAVORITE',
                'description'  => _('Manage widget'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            172 => [
                'pk_permission' => 172,
                'name'         => 'LETTER_CREATE',
                'description'  => _('Create'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            173 => [
                'pk_permission' => 173,
                'name'         => 'LETTER_ADMIN',
                'description'  => _('List'),
                'module'       => 'LETTER_MANAGER',
                'enabled'      => 1
            ],
            174 => [
                'pk_permission' => 174,
                'name'         => 'POLL_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            175 => [
                'pk_permission' => 175,
                'name'         => 'POLL_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'POLL_MANAGER',
                'enabled'      => 1
            ],
            177 => [
                'pk_permission' => 177,
                'name'         => 'IMPORT_NEWS_AGENCY_CONFIG',
                'description'  => _('Config News Agency importer'),
                'module'       => 'IMPORT',
                'enabled'      => 1
            ],
            180 => [
                'pk_permission' => 180,
                'name'         => 'INSTANCE_SYNC_ADMIN',
                'description'  => _('Administer synchronization between Opennemas'),
                'module'       => 'SYNC_MANAGER',
                'enabled'      => 1
            ],
            181 => [
                'pk_permission' => 181,
                'name'         => 'SPECIAL_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'SPECIAL_MANAGER',
                'enabled'      => 1
            ],
            182 => [
                'pk_permission' => 182,
                'name'         => 'PAYWALL_ADMIN',
                'description'  => _('Administer paywall'),
                'module'       => 'PAYWALL',
                'enabled'      => 1
            ],
            183 => [
                'pk_permission' => 183,
                'name'         => 'ATTACHMENT_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            184 => [
                'pk_permission' => 184,
                'name'         => 'ATTACHMENT_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            185 => [
                'pk_permission' => 185,
                'name'         => 'ATTACHMENT_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'FILE_MANAGER',
                'enabled'      => 1
            ],
            186 => [
                'pk_permission' => 186,
                'name'         => 'KIOSKO_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'KIOSKO_MANAGER',
                'enabled'      => 1
            ],
            187 => [
                'pk_permission' => 187,
                'name'         => 'OPINION_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'OPINION_MANAGER',
                'enabled'      => 1
            ],
            188 => [
                'pk_permission' => 188,
                'name'         => 'USER_EDIT_OWN_PROFILE',
                'description'  => _('Edit user own profile'),
                'module'       => 'USER_MANAGER',
                'enabled'      => 1
            ],
            189 => [
                'pk_permission' => 189,
                'name'         => 'ADVERTISEMENT_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'ADS_MANAGER',
                'enabled'      => 1
            ],
            // All existing permissions
            190 => [
                'pk_permission' => 190,
                'name'         => 'MASTER',
                'description'  => _('Authenticate as a MASTER'),
                'module'       => 'SECURITY',
                'enabled'      => 0
            ],
            // All existing permissions for owned instances and some edition
            // permissions in manager
            191 => [
                'pk_permission' => 191,
                'name'         => 'PARTNER',
                'description'  => _('Authenticate as a PARTNER'),
                'module'       => 'SECURITY',
                'enabled'      => 0
            ],
            // All permissions for active extensions in instance
            192 => [
                'pk_permission' => 192,
                'name'         => 'ADMIN',
                'description'  => _('Authenticate as an ADMINISTRATOR'),
                'module'       => 'SECURITY',
                'enabled'      => 1
            ],
            193 => [
                'pk_permission' => 193,
                'name'         => 'INSTANCE_LIST',
                'description'  => _('List'),
                'module'       => 'INSTANCE',
                'enabled'      => 1
            ],
            194 => [
                'pk_permission' => 194,
                'name'         => 'INSTANCE_CREATE',
                'description'  => _('Create'),
                'module'       => 'INSTANCE',
                'enabled'      => 1
            ],
            195 => [
                'pk_permission' => 195,
                'name'         => 'INSTANCE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'INSTANCE',
                'enabled'      => 1
            ],
            196 => [
                'pk_permission' => 196,
                'name'         => 'INSTANCE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'INSTANCE',
                'enabled'      => 1
            ],
            197 => [
                'pk_permission' => 197,
                'name'         => 'INSTANCE_REPORT',
                'description'  => _('Report'),
                'module'       => 'INSTANCE',
                'enabled'      => 1
            ],
            198 => [
                'pk_permission' => 198,
                'name'         => 'EXTENSION_LIST',
                'description'  => _('List'),
                'module'       => 'EXTENSION',
                'enabled'      => 1
            ],
            199 => [
                'pk_permission' => 199,
                'name'         => 'EXTENSION_CREATE',
                'description'  => _('Create'),
                'module'       => 'EXTENSION',
                'enabled'      => 1
            ],
            200 => [
                'pk_permission' => 200,
                'name'         => 'EXTENSION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'EXTENSION',
                'enabled'      => 1
            ],
            201 => [
                'pk_permission' => 201,
                'name'         => 'EXTENSION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'EXTENSION',
                'enabled'      => 1
            ],
            202 => [
                'pk_permission' => 202,
                'name'         => 'EXTENSION_REPORT',
                'description'  => _('Report'),
                'module'       => 'EXTENSION',
                'enabled'      => 1
            ],
            203 => [
                'pk_permission' => 203,
                'name'         => 'NOTIFICATION_LIST',
                'description'  => _('List'),
                'module'       => 'NOTIFICATION',
                'enabled'      => 1
            ],
            204 => [
                'pk_permission' => 204,
                'name'         => 'NOTIFICATION_CREATE',
                'description'  => _('Create'),
                'module'       => 'NOTIFICATION',
                'enabled'      => 1
            ],
            205 => [
                'pk_permission' => 205,
                'name'         => 'NOTIFICATION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'NOTIFICATION',
                'enabled'      => 1
            ],
            206 => [
                'pk_permission' => 206,
                'name'         => 'NOTIFICATION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'NOTIFICATION',
                'enabled'      => 1
            ],
            207 => [
                'pk_permission' => 207,
                'name'         => 'NOTIFICATION_REPORT',
                'description'  => _('Report'),
                'module'       => 'NOTIFICATION',
                'enabled'      => 1
            ],
            208 => [
                'pk_permission' => 208,
                'name'         => 'CLIENT_LIST',
                'description'  => _('List'),
                'module'       => 'CLIENT',
                'enabled'      => 1
            ],
            209 => [
                'pk_permission' => 209,
                'name'         => 'CLIENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'CLIENT',
                'enabled'      => 1
            ],
            210 => [
                'pk_permission' => 210,
                'name'         => 'CLIENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'CLIENT',
                'enabled'      => 1
            ],
            211 => [
                'pk_permission' => 211,
                'name'         => 'CLIENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'CLIENT',
                'enabled'      => 1
            ],
            212 => [
                'pk_permission' => 212,
                'name'         => 'CLIENT_REPORT',
                'description'  => _('Report'),
                'module'       => 'CLIENT',
                'enabled'      => 1
            ],
            213 => [
                'pk_permission' => 213,
                'name'         => 'PURCHASE_LIST',
                'description'  => _('List'),
                'module'       => 'PURCHASE',
                'enabled'      => 1
            ],
            214 => [
                'pk_permission' => 214,
                'name'         => 'PURCHASE_CREATE',
                'description'  => _('Create'),
                'module'       => 'PURCHASE',
                'enabled'      => 1
            ],
            215 => [
                'pk_permission' => 215,
                'name'         => 'PURCHASE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'PURCHASE',
                'enabled'      => 1
            ],
            216 => [
                'pk_permission' => 216,
                'name'         => 'PURCHASE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'PURCHASE',
                'enabled'      => 1
            ],
            217 => [
                'pk_permission' => 217,
                'name'         => 'PURCHASE_REPORT',
                'description'  => _('Report'),
                'module'       => 'PURCHASE',
                'enabled'      => 1
            ],
            218 => [
                'pk_permission' => 218,
                'name'         => 'REPORT_LIST',
                'description'  => _('List'),
                'module'       => 'REPORT',
                'enabled'      => 1
            ],
            219 => [
                'pk_permission' => 219,
                'name'         => 'REPORT_DOWNLOAD',
                'description'  => _('Download'),
                'module'       => 'REPORT',
                'enabled'      => 1
            ],
            220 => [
                'pk_permission' => 220,
                'name'         => 'COMMAND_LIST',
                'description'  => _('List'),
                'module'       => 'COMMAND',
                'enabled'      => 1
            ],
            221 => [
                'pk_permission' => 221,
                'name'         => 'COMMAND_EXECUTE',
                'description'  => _('Execute'),
                'module'       => 'COMMAND',
                'enabled'      => 1
            ],
            222 => [
                'pk_permission' => 222,
                'name'         => 'OPCACHE_LIST',
                'description'  => _('List'),
                'module'       => 'OPCACHE',
                'enabled'      => 1
            ],
            223 => [
                'pk_permission' => 223,
                'name'         => 'GROUP_PUBLIC',
                'description'  => _('Marks this group as public'),
                'module'       => 'INTERNAL',
                'enabled'      => 1
            ],
            224 => [
                'pk_permission' => 224,
                'name'         => 'MEMBER_SEND_NEWSLETTER',
                'description'  => _('Send newsletter'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            225 => [
                'pk_permission' => 225,
                'name'         => 'MEMBER_HIDE_ADVERTISEMENTS',
                'description'  => _('Hide advertisements'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            226 => [
                'pk_permission' => 226,
                'name'         => 'MEMBER_REQUIRES_PAYMENT',
                'description'  => _('Requires payment'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            227 => [
                'pk_permission' => 227,
                'name'         => 'MEMBER_HIDE_PRINT',
                'description'  => _('Hide print button'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            228 => [
                'pk_permission' => 228,
                'name'         => 'MEMBER_HIDE_SOCIAL',
                'description'  => _('Hide social networks buttons'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            229 => [
                'pk_permission' => 229,
                'name'         => 'MEMBER_BLOCK_BROWSER',
                'description'  => _('Block browser actions (cut, copy,...)'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            230 => [
                'pk_permission' => 230,
                'name'         => 'NON_MEMBER_BLOCK_ACCESS',
                'description'  => _('Block access to content'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            231 => [
                'pk_permission' => 231,
                'name'         => 'NON_MEMBER_HIDE_TITLE',
                'description'  => _('Hide title'),
                'module'       => 'FRONTEND',
                'enabled'      => 0
            ],
            232 => [
                'pk_permission' => 232,
                'name'         => 'NON_MEMBER_HIDE_SUMMARY',
                'description'  => _('Hide summary'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            233 => [
                'pk_permission' => 233,
                'name'         => 'NON_MEMBER_HIDE_BODY',
                'description'  => _('Hide body'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            234 => [
                'pk_permission' => 234,
                'name'         => 'NON_MEMBER_HIDE_PRETITLE',
                'description'  => _('Hide pretitle'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            235 => [
                'pk_permission' => 235,
                'name'         => 'NON_MEMBER_HIDE_MEDIA',
                'description'  => _('Hide media'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            236 => [
                'pk_permission' => 236,
                'name'         => 'NON_MEMBER_HIDE_RELATED_CONTENTS',
                'description'  => _('Hide related contents'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            237 => [
                'pk_permission' => 237,
                'name'         => 'NON_MEMBER_HIDE_INFO',
                'description'  => _('Hide content information'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            238 => [
                'pk_permission' => 238,
                'name'         => 'NON_MEMBER_HIDE_TAGS',
                'description'  => _('Hide tags'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            239 => [
                'pk_permission' => 239,
                'name'         => 'NON_MEMBER_HIDE_PRINT',
                'description'  => _('Hide print button'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            240 => [
                'pk_permission' => 240,
                'name'         => 'NON_MEMBER_HIDE_SOCIAL',
                'description'  => _('Hide social networks buttons'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            241 => [
                'pk_permission' => 241,
                'name'         => 'NON_MEMBER_BLOCK_BROWSER',
                'description'  => _('Block browser actions (cut, copy,...)'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            242 => [
                'pk_permission' => 242,
                'name'         => 'NON_MEMBER_NO_INDEX',
                'description'  => _('Prevent search engine indexation'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            251 => [
                'pk_permission' => 251,
                'name'         => 'NON_MEMBER_HIDE_ADVERTISEMENTS',
                'description'  => _('Hide advertisements'),
                'module'       => 'FRONTEND',
                'enabled'      => 1
            ],
            243 => [
                'pk_permission' => 243,
                'name'         => 'TAG_ADMIN',
                'description'  => _('List'),
                'module'       => 'es.openhost.module.tags',
                'enabled'      => 1
            ],
            244 => [
                'pk_permission' => 244,
                'name'         => 'TAG_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'es.openhost.module.tags',
                'enabled'      => 1
            ],
            245 => [
                'pk_permission' => 245,
                'name'         => 'TAG_DELETE',
                'description'  => _('Delete'),
                'module'       => 'es.openhost.module.tags',
                'enabled'      => 1
            ],
            246 => [
                'pk_permission' => 246,
                'name'         => 'TAG_CREATE',
                'description'  => _('Create'),
                'module'       => 'es.openhost.module.tags',
                'enabled'      => 1
            ],
            247 => [
                'pk_permission' => 247,
                'name'         => 'EVENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'es.openhost.module.events',
                'enabled'      => 1
            ],
            248 => [
                'pk_permission' => 248,
                'name'         => 'EVENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'es.openhost.module.events',
                'enabled'      => 1
            ],
            249 => [
                'pk_permission' => 249,
                'name'         => 'EVENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'es.openhost.module.events',
                'enabled'      => 1
            ],
            250 => [
                'pk_permission' => 250,
                'name'         => 'EVENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'es.openhost.module.events',
                'enabled'      => 1
            ],
        ];
    }

    /**
     * Returns the list of available permissions.
     *
     * @return array The list of available permissions.
     */
    public function getAvailable() : array
    {
        $security = $this->security;

        return array_filter($this->permissions, function ($a) use ($security) {
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
        $names = [];

        foreach ($ids as $id) {
            if (array_key_exists($id, $this->permissions)) {
                $names[$id] = $this->permissions[$id]['name'];
            }
        }

        return $names;
    }
}
