<?php

namespace JZaaa\CakeCaptcha\Controller;

use JZaaa\CakeCaptcha\Captcha;

class CaptchaController extends AppController
{

    /**
     * create image
     */
    public function index()
    {
        $this->viewBuilder()->setLayout(false);
        $this->autoRender = false;

        $captcha = new Captcha([
            'session' => $this->request->getSession()
        ]);
        $captcha->create();

        header('Content-type: image/jpeg');
        $captcha->img();

        exit;
    }


}
