CakeCaptcha
============
CakePHP 验证码插件，支持PHP7 - PHP8

CakePHP3 请使用 [v1版本](https://github.com/JZaaa/CakeCaptcha/tree/v1)

## Requires
- php: >=7.4
- cakephp/cakephp: ^4.4
- [jzaaa/php-simple-captcha: ^3.0.0](https://github.com/JZaaa/php-simple-captcha)

## 安装

```
composer require jzaaa/cake-captcha
```

## 开启CakePHP插件
```
bin/cake plugin load JZaaa/CakeCaptcha
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
// 清晰验证码
return [
    'captcha' => [
        'route' => '/jzaaa/cake-captcha/', // 默认访问路由
        'config' => [
            'width' => 150, // 验证码图像宽
            'height' => 52, // 验证码图像高
            'sensitive' => false, // 是否对大小写敏感
            'sessionKey' => 'captcha', // 存储session key
            'length' => 4, // 验证码长度
            'charset' => '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ', // 验证码字符集
            'applyPostEffects' => false, // 是否应用后期效果,启用它会影响bgColor、textColor等配置
            'bgColor' => 'f5f7fb', // 背景色
            'textColor' => '#1f2937', // 文字颜色
            'lineColor' => '#d6dde8', // 线条颜色
            'applyNoise' => false, // 是否添加背景噪点字符
            'distort' => false, // 是否扭曲整张验证码图片
            'randomizeFonts' => false, // 是否每个字符随机使用不同字体
            'applyEffects' => true, // 是否启用干扰效果总开关。这个是总开关，控制干扰线、噪点、扭曲、后期效果等是否执行
            'maxLinesBehind' => 1, // 验证码文字后方最多画几条干扰线
            'maxLinesFront' => 0, // 验证码文字前方最多画几条干扰线
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
