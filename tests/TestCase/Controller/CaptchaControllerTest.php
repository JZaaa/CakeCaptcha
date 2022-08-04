<?php

namespace JZaaa\CakeCaptcha\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class CaptchaControllerTest extends TestCase
{

    use IntegrationTestTrait;


    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void {
        parent::setUp();

        Configure::write('Captcha', []);
    }

    /**
     * @return void
     */
    public function testDisplay() {
        $this->disableErrorHandlerMiddleware();

        $this->get(['plugin' => 'JZaaa/CakeCaptcha', 'controller' => 'Captcha', 'action' => 'index']);

        $this->assertResponseCode(200);

        $this->assertContentType('image/jpeg');
    }



}
