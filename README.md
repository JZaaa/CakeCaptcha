CakeCaptcha
============
CakePHP 验证码插件，支持PHP8.1

CakePHP3 请使用 [v1版本](https://github.com/JZaaa/CakeCaptcha/tree/v1)

## Requires
- php: >=7.4
- cakephp/cakephp: ^4.4
- [s1syphos/php-simple-captcha: ^2.2.1](https://codeberg.org/S1SYPHOS/php-simple-captcha)

## 安装

```
composer require jzaaa/cake-captcha
```

## 开启CakePHP插件
```
bin/cake plugin load JZaaa/CakeCaptcha --routes --bootstrap
```
或
```php
// src/Application.php
public function bootstrap()
{
    parent::bootstrap();
    $this->addPlugin('JZaaa/CakeCaptcha', ['routes' => true, 'bootstrap' => true]);
}
```
## 配置
创建`config/captcha.php`文件配置插件：
```php
<?php
return [
    'captcha' => [
        'route' => '/jzaaa/cake-captcha/', // 默认访问路由
        'config' => [
            'width' => 150, // 验证码图像宽
            'height' => 40, // 验证码图像高
            'sensitive' => false, // 是否对大小写敏感
            'sessionKey' => 'captcha', // 存储session key
            'length' => 4, // 验证码长度
            'charset' => '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ', // 验证码字符集
            'applyPostEffects' => true, // 是否应用后期效果
        ]
    ]
];
```
或者在`config/app.php`中添加配置项

## 使用
in `*.php` 视图文件：
```php

<?php $captcha = $this->Url->build('/jzaaa/cake-captcha')?>

<img src="<?php echo $captcha?>" onclick="this.src='<?php echo $captcha . '?'?>' + Math.random()" style="cursor: pointer;">

```
in `Controller` 控制器文件：
```php
use JZaaa\CakeCaptcha\Captcha;

// 检测验证码是否合法
public function check()
{
    if ($this->request->is('post')) {
        $userCode = $this->request->getData('userCode');

        if (!empty($userCode)) {
            $captcha = new Captcha([
                'session' => $this->request->getSession()
            ]);
            if ($captcha->check($userCode)) {
                // valid
            }
        } else {
                // invalid
        }

    }
}

```
