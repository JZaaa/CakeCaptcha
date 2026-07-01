<?php

namespace JZaaa\CakeCaptcha;

use Cake\Core\Configure;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Session;
use ReflectionMethod;
use SimpleCaptcha\Builder;

class Captcha
{

    /**
     * 验证码配置类
     * @var null|string
     */
    protected $CaptchaConfigureClass = null;

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
     * simple-captcha 图像生成配置
     * @var CaptchaConfigs
     */
    protected $captchaConfigs;

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
            $this->captchaConfigs->setCharset(strtolower($this->captchaConfigs->getCharset()));
        }
    }


    /**
     * 配置
     * @param $config
     */
    protected function configure($config)
    {
        $ignore = ['captchaBuilder', 'phraseBuilder', 'session', 'captchaConfigs'];

        $defaultConfig = Configure::read('captcha.config');

        if (is_array($defaultConfig)) {
            $config = array_merge($defaultConfig, $config);
        }
        $this->captchaConfigs = isset($config['captchaConfigs']) && $config['captchaConfigs'] instanceof CaptchaConfigs
            ? $config['captchaConfigs']
            : new CaptchaConfigs();
        $classVars = array_keys(get_class_vars(get_class($this)));

        foreach ($config as $key => $item) {
            if (in_array($key, $classVars) && !in_array($key, $ignore)) {
                $this->$key = $item;
                continue;
            }

            if ($this->captchaConfigs->has($key)) {
                $this->captchaConfigs->set($key, $item);
            }
        }

        $this->CaptchaConfigureClass = Configure::read('captcha.CaptchaConfigureClass');
    }

    /**
     * 初始化Captcha
     */
    protected function initCaptcha()
    {
        if (!$this->phraseBuilder) {
            $this->phraseBuilder = $this->captchaConfigs->buildPhrase();
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

        // 将 CaptchaConfigs 维护的配置同步到底层 SimpleCaptcha\Builder。
        $this->captchaConfigs->applyToBuilder($this->captchaBuilder);

        if (!empty($this->CaptchaConfigureClass) && is_string($this->CaptchaConfigureClass)) {
            $method = new ReflectionMethod($this->CaptchaConfigureClass, 'configure');
            if ($method->isStatic() && $method->isPublic()) {
                $this->CaptchaConfigureClass::configure($this->captchaBuilder);
            } else {
                throw new MethodNotAllowedException($this->CaptchaConfigureClass . '::configure method must be static and public.');
            }
        }

        $this->captchaConfigs->buildBuilder($this->captchaBuilder);

        $this->phrase = $this->captchaBuilder->phrase;

        $this->session->write($this->sessionKey, $this->phrase);

        return [
            'phrase' => $this->phrase,
            'sensitive' => $this->sensitive,
        ];
    }

    /**
     * 获取 simple-captcha 图像生成配置。
     *
     * @return CaptchaConfigs
     */
    public function getCaptchaConfigs()
    {
        return $this->captchaConfigs;
    }

    /**
     * 设置 simple-captcha 图像生成配置。
     *
     * @param CaptchaConfigs $captchaConfigs
     * @return $this
     */
    public function setCaptchaConfigs(CaptchaConfigs $captchaConfigs)
    {
        $this->captchaConfigs = $captchaConfigs;
        if (!$this->sensitive) {
            $this->captchaConfigs->setCharset(strtolower($this->captchaConfigs->getCharset()));
        }

        // 配置对象变更后重置生成器，确保 length/charset 等配置在下次生成时生效。
        $this->phraseBuilder = null;
        $this->captchaBuilder = null;
        $this->phrase = null;

        return $this;
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
