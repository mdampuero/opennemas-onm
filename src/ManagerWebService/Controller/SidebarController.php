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
                    'name' => _('Dashboard'),
                    'icon' => 'fa-home',
                    'route' => 'manager_welcome',
                    'click' => true
                ],
                [
                    'name' => _('Instances'),
                    'icon' => 'fa-cubes',
                    'route' => 'manager_instances_list',
                    'click' => true
                ],
                [
                    'name' => _('Notifications'),
                    'icon' => 'fa-bell',
                    'route' => 'manager_notifications_list',
                    'click' => true
                ],
                [
                    'name' => _('Store'),
                    'icon' => 'fa-shopping-cart',
                    'route' => 'manager_purchases_list',
                    'items' => [
                        [
                            'name' => _('Clients'),
                            'icon' => 'fa-user',
                            'route' => 'manager_purchases_list',
                            'click' => 'true'
                        ],
                        [
                            'name' => _('Purchases'),
                            'icon' => 'fa-cart-arrow-down',
                            'route' => 'manager_purchases_list',
                            'click' => 'true'
                        ],
                    ]
                ],
                [
                    'name' => _('Reports'),
                    'icon' => 'fa-files-o',
                    'route' => 'manager_reports_list',
                    'click' => 'true'
                ],
                [
                    'name' => _('Framework'),
                    'icon' => 'fa-home',
                    'items' => [
                        [
                            'name' => _('Commands'),
                            'icon' => 'fa-code',
                            'route' => 'manager_commands',
                            'click' => true
                        ],
                        [
                            'name' => _('OpCache Status'),
                            'icon' => 'fa-database',
                            'route' => 'manager_opcache_status',
                            'click' => true
                        ]
                    ]
                ],
                [
                    'name' => _('System'),
                    'icon' => 'fa-gears',
                    'items' => [
                        [
                            'name' => _('Users'),
                            'icon' => 'fa-user',
                            'route' => 'manager_users_list',
                            'click' => true
                        ],
                        [
                            'name' => _('User groups'),
                            'icon' => 'fa-users',
                            'route' => 'manager_user_groups_list',
                            'click' => true
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
