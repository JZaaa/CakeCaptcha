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
            'bgColor' => null, // 图片背景色
            'lineColor' => null, // 干扰线颜色
            'textColor' => null, // 文字颜色
        ]
    ]
];
