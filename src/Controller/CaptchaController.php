<?php

namespace JZaaa\CakeCaptcha\Controller;

use Cake\Http\CallbackStream;
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

        $stream = new CallbackStream(function () use ($captcha) {
            $captcha->img();
        });

        return $this->response
            ->withType('image/jpeg')
            ->withBody($stream);
    }


}
