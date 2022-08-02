CakeCaptcha
============
CakePHP 验证码插件

## Requires
- php: >=5.6 <8.0
- cakephp/cakephp: ^3.10 | ^4.3
- [gregwar/captcha: ^1.1](https://github.com/Gregwar/Captcha)

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
            'charset' => '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ' // 验证码字符集
        ]
    ]
];
```
或者在`config/app.php`中添加配置项

## 使用
in `*.ctp` 视图文件：
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




