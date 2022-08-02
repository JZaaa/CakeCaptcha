<?php
use Cake\Core\Configure;

$version = explode('.', Configure::version())[0];
if ($version == 3) {
    \Cake\Routing\Router::plugin('JZaaa/CakeCaptcha', [
        'path' => '/'
    ], function (\Cake\Routing\RouteBuilder $routes) {
        //  route
        if (Configure::read('captcha.route')) {
            $routes->connect(
                Configure::read('captcha.route'),
                ['plugin' => 'JZaaa/CakeCaptcha', 'controller' => 'Captcha', 'action' => 'index']
            );
        } else {
            $routes->connect(
                '/jzaaa/cake-captcha/',
                ['plugin' => 'JZaaa/CakeCaptcha', 'controller' => 'Captcha', 'action' => 'index']
            );
        }
    });
} else {
    return function (\Cake\Routing\RouteBuilder $routes) {
        $routes->plugin('JZaaa/CakeCaptcha', ['path' => '/'], function (\Cake\Routing\RouteBuilder $routes) {
            //  route
            if (Configure::read('captcha.route')) {
                $routes->connect(
                    Configure::read('captcha.route'),
                    ['plugin' => 'JZaaa/CakeCaptcha', 'controller' => 'Captcha', 'action' => 'index']
                );
            } else {
                $routes->connect(
                    '/jzaaa/cake-captcha/',
                    ['plugin' => 'JZaaa/CakeCaptcha', 'controller' => 'Captcha', 'action' => 'index']
                );
            }
        });
    };

}
