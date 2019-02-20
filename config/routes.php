<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

Router::plugin('JZaaa/CakeCaptcha', [
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