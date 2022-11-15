<?php

namespace JZaaa\CakeCaptcha;

use Cake\Core\Configure;
use Cake\Http\Session;
use SimpleCaptcha\Builder;

class Captcha
{

    /**
     * session
     * @var Session
     */
    protected $session;

    /**
     * @var $sessionKey
     */
    protected $sessionKey = 'captcha';


    /**
     * 是否对大小写敏感
     * @var bool $sensitive
     */
    protected $sensitive = false;

    /**
     * 验证码图像宽
     * @var int $width
     */
    protected $width = 150;

    /**
     * 验证码图像高
     * @var int $height
     */
    protected $height = 40;

    /**
     * 验证码长度
     * @var int $length
     */
    protected $length = 4;

    /**
     * 验证码字符集
     * @var string $charset
     */
    protected $charset = '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    /**
     * @var Builder
     */
    protected $captchaBuilder;

    /**
     * 验证码
     * @var string $phrase
     */
    protected $phrase;

    /**
     * @var Builder
     */
    protected $phraseBuilder;

    /**
     * 后期效果
     * @var bool $applyPostEffects
     */
    protected $applyPostEffects = true;

    /**
     * Captcha constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->configure($config);

        $this->init();
    }

    protected function init()
    {
        if (!$this->session instanceof Session) {
            $this->session = new Session();
        }
        if (!$this->session->started()) {
            $this->session->start();
        }

        if (!$this->sensitive) {
            $this->charset = strtolower($this->charset);
        }
    }


    /**
     * 配置
     * @param $config
     */
    protected function configure($config)
    {
        $ignore = ['captchaBuilder', 'phraseBuilder', 'session'];

        $defaultConfig = Configure::read('captcha.config');

        if (is_array($defaultConfig)) {
            $config = array_merge($defaultConfig, $config);
        }


        foreach ($config as $key => $item) {
            if (isset($this->$key) && !in_array($key, $ignore)) {
                $this->$key = $item;
            }
        }
    }

    /**
     * 初始化Captcha
     */
    protected function initCaptcha()
    {
        if (!$this->phraseBuilder) {
            $this->phraseBuilder = Builder::buildPhrase((int) $this->length, $this->charset);
        }
        if (!$this->captchaBuilder) {
            $this->captchaBuilder = new Builder($this->phraseBuilder);
        }
    }

    /**
     * captcha生成器
     * @return array
     */
    protected function generate()
    {
        $this->initCaptcha();

        $this->captchaBuilder->applyPostEffects = $this->applyPostEffects;

        $this->captchaBuilder->build((int) $this->width,(int) $this->height);

        $this->phrase = $this->captchaBuilder->phrase;

        $this->session->write($this->sessionKey, $this->phrase);

        return [
            'phrase' => $this->phrase,
            'sensitive' => $this->sensitive,
        ];
    }

    /**
     * 生成captcha
     * @return array
     */
    public function create()
    {
        return $this->generate();
    }


    /**
     * 获取验证码值
     * @return mixed
     */
    public function getPhrase()
    {
        return $this->phrase;
    }


    /**
     * 返回验证码图像src
     * @return string
     */
    public function base64()
    {
        return $this->captchaBuilder->inline();
    }

    /**
     * 返回base64验证码图像
     * @return string
     */
    public function imgBase64()
    {
        return '<img src="' . $this->base64() . '"/>';
    }

    public function img()
    {
        $this->captchaBuilder->output();
    }


    /**
     * 核对验证码
     * @param string $value
     * @return bool
     */
    public function check(string $value)
    {
        $phrase = $this->session->read($this->sessionKey);

        if (is_null($phrase)) {
            return false;
        }

        if (!$this->sensitive) {
            $value = strtolower($value);
        }

        $result = (new Builder())->compare($value, $phrase);

        if ($result) {
            $this->session->delete($this->sessionKey);
        }

        return $result;
    }

}
