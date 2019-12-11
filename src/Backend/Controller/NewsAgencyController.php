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

class NewsAgencyController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'NEWS_AGENCY_IMPORTER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'list'   => 'IMPORT_ADMIN',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'news_agency/resource';
}
