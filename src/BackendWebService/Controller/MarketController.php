<?php

namespace BackendWebService\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class MarketController extends Controller
{
    protected $modules = [
        [
            'id'           => 1,
            'name'         => 'Theme 1',
            'author'       => 'Openhost S.L.',
            'description'  => 'Description for theme 1',
            'last_updated' => '2015-05-05 08:00:00',
            'price'        => 19.99,
            'tags'         => [ 'theme', 'template', 'flat' ]
        ],
        [
            'id'           => 2,
            'name'         => 'Advertisement',
            'author'       => 'Openhost S.L.',
            'description'  => 'Description for advertisement module',
            'last_updated' => '2015-05-05 08:00:00',
            'price'        => 19.99,
            'tags'         => [ 'advertisement', 'ad' ]
        ],
        [
            'id'           => 1,
            'name'         => 'Advanced frontpage',
            'author'       => 'Openhost S.L.',
            'description'  => 'Description for  advanced frontpage',
            'last_updated' => '2015-05-05 08:00:00',
            'price'        => 19.99,
            'tags'         => [ 'frontpage', 'layout', 'wysiwyg' ]
        ]
    ];


    public function listAction()
    {

        return new JsonResponse([ 'results' => $this->modules ]);
    }
}
