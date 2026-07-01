<?php

namespace JZaaa\CakeCaptcha;

use InvalidArgumentException;
use SimpleCaptcha\Builder;

class CaptchaConfigs
{
    /**
     * 验证码图像宽
     * @var int
     */
    protected $width = 150;

    /**
     * 验证码图像高
     * @var int
     */
    protected $height = 40;

    /**
     * 验证码长度
     * @var int
     */
    protected $length = 4;

    /**
     * 验证码字符集
     * @var string
     */
    protected $charset = '2346789abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ';

    /**
     * 验证码字体文件路径列表，默认使用 php-simple-captcha 内置字体
     * @var null|array
     */
    protected $fonts = null;

    /**
     * 是否扭曲验证码图片
     * @var bool
     */
    protected $distort = true;

    /**
     * 是否对扭曲后的图片进行插值处理
     * @var bool
     */
    protected $interpolate = true;

    /**
     * 验证码文字后方最大干扰线数量
     * @var null|int
     */
    protected $maxLinesBehind = null;

    /**
     * 验证码文字前方最大干扰线数量
     * @var null|int
     */
    protected $maxLinesFront = null;

    /**
     * 最大字符旋转角度
     * @var int
     */
    protected $maxAngle = 8;

    /**
     * 最大字符垂直偏移量
     * @var int
     */
    protected $maxOffset = 5;

    /**
     * 图片背景色
     * @var null|string|array
     */
    protected $bgColor = null;

    /**
     * 干扰线颜色
     * @var null|string|array
     */
    protected $lineColor = null;

    /**
     * 文字颜色
     * @var null|string|array
     */
    protected $textColor = null;

    /**
     * 背景图片路径
     * @var null|string
     */
    protected $bgImage = null;

    /**
     * 是否应用任何干扰效果
     * @var bool
     */
    protected $applyEffects = true;

    /**
     * 是否添加背景噪点字符
     * @var bool
     */
    protected $applyNoise = true;

    /**
     * 噪点数量因子，按验证码长度倍数生成背景噪点
     * @var int
     */
    protected $noiseFactor = 2;

    /**
     * 后期效果
     * @var bool
     */
    protected $applyPostEffects = true;

    /**
     * 是否启用散点后期效果
     * @var bool
     */
    protected $applyScatterEffect = true;

    /**
     * 是否为每个字符随机选择字体
     * @var bool
     */
    protected $randomizeFonts = true;

    /**
     * CaptchaConfigs constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    /**
     * 批量设置配置，未知配置保持忽略以兼容旧版数组写法。
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            if ($this->has($key)) {
                $this->set($key, $value);
            }
        }

        return $this;
    }

    /**
     * 通用 setter，统一通过 setXxx 方法维护属性。
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException(sprintf('Unknown captcha config "%s".', $key));
        }

        $method = 'set' . ucfirst($key);
        $this->$method($value);

        return $this;
    }

    /**
     * 通用 getter，统一通过 getXxx 方法读取属性。
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException(sprintf('Unknown captcha config "%s".', $key));
        }

        $method = 'get' . ucfirst($key);

        return $this->$method();
    }

    /**
     * 判断配置项是否由当前类维护。
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return in_array($key, $this->getConfigKeys(), true);
    }

    /**
     * 将图像生成配置同步到底层 SimpleCaptcha\Builder。
     *
     * @param \SimpleCaptcha\Builder $builder
     * @return \SimpleCaptcha\Builder
     */
    public function applyToBuilder(Builder $builder)
    {
        foreach ($this->getBuilderConfigKeys() as $key) {
            $value = $this->get($key);
            if ($value !== null) {
                $builder->$key = $value;
            }
        }

        return $builder;
    }

    /**
     * 按当前 length/charset 生成验证码字符。
     *
     * @return string
     */
    public function buildPhrase()
    {
        return Builder::buildPhrase((int)$this->getLength(), $this->getCharset());
    }

    /**
     * 按当前 width/height 构建验证码图片。
     *
     * @param \SimpleCaptcha\Builder $builder
     * @return \SimpleCaptcha\Builder
     */
    public function buildBuilder(Builder $builder)
    {
        $builder->build((int)$this->getWidth(), (int)$this->getHeight());

        return $builder;
    }

    /**
     * 一次性应用完整验证码配置：phrase、Builder 属性、图片尺寸都会生效。
     *
     * @param null|\SimpleCaptcha\Builder $builder
     * @return \SimpleCaptcha\Builder
     */
    public function applyToCaptchaBuilder(Builder $builder = null)
    {
        if (!$builder instanceof Builder) {
            $builder = new Builder($this->buildPhrase());
        }

        $this->applyToBuilder($builder);

        return $this->buildBuilder($builder);
    }

    /**
     * 获取所有配置项。
     *
     * @return array
     */
    public function toArray()
    {
        $config = [];
        foreach ($this->getConfigKeys() as $key) {
            $config[$key] = $this->get($key);
        }

        return $config;
    }

    /**
     * 获取当前类维护的全部配置项。
     *
     * @return array
     */
    public function getConfigKeys()
    {
        return array_merge(
            ['width', 'height', 'length', 'charset'],
            $this->getBuilderConfigKeys()
        );
    }

    /**
     * 获取允许透传给 SimpleCaptcha\Builder 的配置项。
     *
     * @return array
     */
    public function getBuilderConfigKeys()
    {
        return [
            'fonts',
            'distort',
            'interpolate',
            'maxLinesBehind',
            'maxLinesFront',
            'maxAngle',
            'maxOffset',
            'bgColor',
            'lineColor',
            'textColor',
            'bgImage',
            'applyEffects',
            'applyNoise',
            'noiseFactor',
            'applyPostEffects',
            'applyScatterEffect',
            'randomizeFonts',
        ];
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    public function getFonts()
    {
        return $this->fonts;
    }

    public function setFonts($fonts)
    {
        $this->fonts = $fonts;

        return $this;
    }

    public function getDistort()
    {
        return $this->distort;
    }

    public function setDistort($distort)
    {
        $this->distort = $distort;

        return $this;
    }

    public function getInterpolate()
    {
        return $this->interpolate;
    }

    public function setInterpolate($interpolate)
    {
        $this->interpolate = $interpolate;

        return $this;
    }

    public function getMaxLinesBehind()
    {
        return $this->maxLinesBehind;
    }

    public function setMaxLinesBehind($maxLinesBehind)
    {
        $this->maxLinesBehind = $maxLinesBehind;

        return $this;
    }

    public function getMaxLinesFront()
    {
        return $this->maxLinesFront;
    }

    public function setMaxLinesFront($maxLinesFront)
    {
        $this->maxLinesFront = $maxLinesFront;

        return $this;
    }

    public function getMaxAngle()
    {
        return $this->maxAngle;
    }

    public function setMaxAngle($maxAngle)
    {
        $this->maxAngle = $maxAngle;

        return $this;
    }

    public function getMaxOffset()
    {
        return $this->maxOffset;
    }

    public function setMaxOffset($maxOffset)
    {
        $this->maxOffset = $maxOffset;

        return $this;
    }

    public function getBgColor()
    {
        return $this->bgColor;
    }

    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    public function getLineColor()
    {
        return $this->lineColor;
    }

    public function setLineColor($lineColor)
    {
        $this->lineColor = $lineColor;

        return $this;
    }

    public function getTextColor()
    {
        return $this->textColor;
    }

    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;

        return $this;
    }

    public function getBgImage()
    {
        return $this->bgImage;
    }

    public function setBgImage($bgImage)
    {
        $this->bgImage = $bgImage;

        return $this;
    }

    public function getApplyEffects()
    {
        return $this->applyEffects;
    }

    public function setApplyEffects($applyEffects)
    {
        $this->applyEffects = $applyEffects;

        return $this;
    }

    public function getApplyNoise()
    {
        return $this->applyNoise;
    }

    public function setApplyNoise($applyNoise)
    {
        $this->applyNoise = $applyNoise;

        return $this;
    }

    public function getNoiseFactor()
    {
        return $this->noiseFactor;
    }

    public function setNoiseFactor($noiseFactor)
    {
        $this->noiseFactor = $noiseFactor;

        return $this;
    }

    public function getApplyPostEffects()
    {
        return $this->applyPostEffects;
    }

    public function setApplyPostEffects($applyPostEffects)
    {
        $this->applyPostEffects = $applyPostEffects;

        return $this;
    }

    public function getApplyScatterEffect()
    {
        return $this->applyScatterEffect;
    }

    public function setApplyScatterEffect($applyScatterEffect)
    {
        $this->applyScatterEffect = $applyScatterEffect;

        return $this;
    }

    public function getRandomizeFonts()
    {
        return $this->randomizeFonts;
    }

    public function setRandomizeFonts($randomizeFonts)
    {
        $this->randomizeFonts = $randomizeFonts;

        return $this;
    }
}
