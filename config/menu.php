<?php

use Spatie\Menu\Laravel\Menu;
use Spatie\Menu\Laravel\Html;
use Spatie\Menu\Laravel\Link;

// Menu::macro('fullsubmenuexample', function () {
//    return Menu::new()->prepend('<a href="#"><span> Multilevel PROVA </span> <i class="fa fa-angle-left pull-right"></i></a>')
//        ->addParentClass('treeview')
//        ->add(Link::to('/link1prova', 'Link1 prova'))->addClass('treeview-menu')
//        ->add(Link::to('/link2prova', 'Link2 prova'))->addClass('treeview-menu')
//        ->url('http://www.google.com', 'Google');
// });

Menu::macro('adminlteSubmenu', function ($submenuName) {
    return Menu::new()->prepend('<a href="#"><span> ' . $submenuName . '</span> <i class="fa fa-angle-left pull-right"></i></a>')
        ->addParentClass('treeview')->addClass('treeview-menu');
});
Menu::macro('adminlteMenu', function () {
    return Menu::new()
        ->addClass('sidebar-menu');
});
Menu::macro('adminlteSeparator', function ($title) {
    return Html::raw($title)->addParentClass('header');
});

Menu::macro('sidebar', function () {
    $menuData = [
        '/admin' => ['label' => '主页', 'link' => '/admin'],
        'permission_menu' => [
            'label' => '权限管理',
            'items' => [
                'permissions' => ['label' => '权限管理', 'link' => '/admin/permissions'],
                'roles' => ['label' => '角色管理', 'link' => '/admin/roles'],
            ],
        ],
        'orders' => ['label' => '订单管理', 'link' => '/admin/orders'],
        'shops_menu' => [
            'label' => '商铺管理',
            'items' => [
                'shops' => ['label' => '商铺管理', 'link' => '/admin/shops'],
                'stations' => ['label' => '站点管理', 'link' => '/admin/stations'],
            ],
        ],
        'devices_menu' => [
            'label' => '设备管理',
            'items' => [
                'devices' => ['label' => '设备管理', 'link' => '/admin/devices'],
                'batteries' => ['label' => '电池管理', 'link' => '/admin/batteries'],
                'slots' => ['label' => '槽位管理', 'link' => '/admin/slots'],
            ]
        ],
        'users' => ['label' => '用户管理', 'link' => '/admin/users'],
        'refund_logs' => ['label' => '提现记录', 'link' => '/admin/refund_logs'],
    ];

    $menuBuilder = function($menu) use (&$menuBuilder) {
        if(isset($menu['items'])) {
            $parent = Menu::adminlteSubmenu($menu['label']);
            foreach($menu['items'] as $k => $v) {
                $parent = $parent->add($menuBuilder($v));
            }
            return $parent;
        } else {
            return Link::to($menu['link'], $menu['label']);
        }
    };

    $rootMenu = Menu::adminlteMenu();
    foreach($menuData as $k => $v) {
        $rootMenu = $rootMenu->add($menuBuilder($v));
    }
    return $rootMenu->setActiveFromRequest();
});

// Menu::macro('sidebar', function () {
//     return Menu::adminlteMenu()
//         ->add(Html::raw('HEADER')->addParentClass('header'))
//         //->action('HomeController@index', 'Home')
//         ->link('http://www.acacha.org', 'Another link')
// //        ->url('http://www.google.com', 'Google')
//         ->add(Menu::adminlteSeparator('Acacha Adminlte'))
//         #adminlte_menu
//         ->add(Menu::adminlteSeparator('SECONDARY MENU'))
//         ->add(Menu::new()->prepend('<a href="#"><span>Multilevel</span> <i class="fa fa-angle-left pull-right"></i></a>')
//             ->addParentClass('treeview')
//             ->add(Link::to('/link1', 'Link1'))->addClass('treeview-menu')
//             ->add(Link::to('/link2', 'Link2'))
//             ->url('http://www.google.com', 'Google')
//             ->add(Menu::new()->prepend('<a href="#"><span>Multilevel 2</span> <i class="fa fa-angle-left pull-right"></i></a>')
//                 ->addParentClass('treeview')
//                 ->add(Link::to('/link21', 'Link21'))->addClass('treeview-menu')
//                 ->add(Link::to('/link22', 'Link22'))
//                 ->url('http://www.google.com', 'Google')
//             )
//         )
//        ->add(
//            Menu::fullsubmenuexample()
//        )
//        ->add(
//            Menu::adminlteSubmenu('Best menu')
//                ->add(Link::to('/acacha', 'acacha'))
//                ->add(Link::to('/profile', 'Profile'))
//                ->url('http://www.google.com', 'Google')
//        )
//         ->setActiveFromRequest();
// });
