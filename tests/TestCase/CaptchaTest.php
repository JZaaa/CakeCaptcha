<?php

namespace JZaaa\CakeCaptcha\Test\TestCase;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use JZaaa\CakeCaptcha\Captcha;

class CaptchaTest extends TestCase
{
    /**
     * @var Captcha
     */
    protected $Captcha;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('Captcha', []);
        $config = [];
        $this->Captcha = new Captcha($config);
    }


    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->Captcha);
    }

    public function testCreate(): void
    {
        $res = $this->Captcha->create();

        $this->assertSame(['phrase', 'sensitive'], array_keys($res));

    }

    public function testGetPhrase(): void
    {
        $this->Captcha->create();

        $phrase = $this->Captcha->getPhrase();

        $this->assertIsString($phrase);
    }


    public function testBase64(): void
    {
        $this->Captcha->create();
        $base64 = $this->Captcha->base64();

        $this->assertIsString($base64);
    }

    public function testCheck(): void
    {
        $this->Captcha->create();
        $phrase = $this->Captcha->getPhrase();

        $this->assertFalse($this->Captcha->check('error!'));
        $this->assertTrue($this->Captcha->check($phrase));
    }

}
