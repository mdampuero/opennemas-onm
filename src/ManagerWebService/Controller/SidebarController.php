<?php

namespace ManagerWebService\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SidebarController extends Controller
{
    /**
     * Returns the sidebar items for manager.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        return new JsonResponse([
            'items' => [
                [
                    'name'  => _('Dashboard'),
                    'icon'  => 'fa-home',
                    'route' => 'manager_welcome',
                    'click' => true,
                ],
                [
                    'name'     => _('Instances'),
                    'icon'     => 'fa-cubes',
                    'route'    => 'manager_instances_list',
                    'click'    => true,
                    'security' => [
                        'permission' => [ 'INSTANCE_LIST' ],
                    ]
                ],
                [
                    'name'     => _('Extensions'),
                    'icon'     => 'fa-puzzle-piece',
                    'security' => [
                        'permission' => [ 'EXTENSION_LIST' ],
                    ],
                    'items' => [
                        [
                            'name'  => _('Modules'),
                            'icon'  => 'fa-flip-horizontal fa-plug',
                            'route' => 'manager_modules_list',
                            'click' => true
                        ]
                    ]
                ],
                [
                    'name'     => _('Notifications'),
                    'icon'     => 'fa-bell',
                    'route'    => 'manager_notifications_list',
                    'click'    => true,
                    'security' => [
                        'permission' => [ 'NOTIFICATION_LIST' ],
                    ],
                ],
                [
                    'name' => _('Store'),
                    'icon' => 'fa-shopping-cart',
                    'security' => [
                        'permission' => [ 'CLIENT_LIST', 'PURCHASE_LIST'],
                    ],
                    'items' => [
                        [
                            'name'     => _('Clients'),
                            'icon'     => 'fa-user',
                            'route'    => 'manager_clients_list',
                            'click'    => true,
                            'security' => [
                                'permission' => [ 'CLIENT_LIST' ],
                            ],
                        ],
                        [
                            'name'     => _('Purchases'),
                            'icon'     => 'fa-shopping-bag',
                            'route'    => 'manager_purchases_list',
                            'click'    => true,
                            'security' => [
                                'permission' => [ 'PURCHASE_LIST' ],
                            ],
                        ],
                    ]
                ],
                [
                    'name'     => _('Reports'),
                    'icon'     => 'fa-files-o',
                    'route'    => 'manager_reports_list',
                    'click'    => true,
                    'security' => [
                        'permission' => [ 'REPORT_LIST' ],
                    ],
                ],
                [
                    'name'     => _('Framework'),
                    'icon'     => 'fa-home',
                    'security' => [
                        'permission' => [ 'COMMAND_LIST', 'OPCACHE_LIST' ],
                    ],
                    'items' => [
                        [
                            'name'     => _('Commands'),
                            'icon'     => 'fa-code',
                            'route'    => 'manager_commands',
                            'click'    => true,
                            'security' => [
                                'permission' => [ 'COMMAND_LIST' ],
                            ],
                        ],
                        [
                            'name'  => _('OpCache Status'),
                            'icon'  => 'fa-database',
                            'route' => 'manager_opcache_status',
                            'click' => true,
                            'security' => [
                                'permission' => [ 'OPCACHE_LIST' ],
                            ],
                        ]
                    ]
                ],
                [
                    'name'     => _('System'),
                    'icon'     => 'fa-gears',
                    'security' => [
                        'permission' => [ 'USER_LIST', 'GROUP_LIST' ],
                    ],
                    'items' => [
                        [
                            'name'     => _('Users'),
                            'icon'     => 'fa-user',
                            'route'    => 'manager_users_list',
                            'click'    => true,
                            'security' => [
                                'permission' => [ 'USER_LIST' ],
                            ],
                        ],
                        [
                            'name'     => _('User groups'),
                            'icon'     => 'fa-users',
                            'route'    => 'manager_user_groups_list',
                            'click'    => true,
                            'security' => [
                                'permission' => [ 'GROUP_LIST' ],
                            ],
                        ]
                    ]
                ]
            ],
            'translations' => [
                'Show/hide sidebar' => _('Show/hide sidebar')
            ]
        ]);
    }
}
