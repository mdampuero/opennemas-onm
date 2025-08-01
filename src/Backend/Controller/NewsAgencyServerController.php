<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

class NewsAgencyServerController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'NEWS_AGENCY_IMPORTER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'IMPORT_NEWS_AGENCY_CONFIG',
        'list'   => 'IMPORT_NEWS_AGENCY_CONFIG',
        'show'   => 'IMPORT_NEWS_AGENCY_CONFIG',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'news_agency/server';
}
