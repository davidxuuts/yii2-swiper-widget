<?php

namespace davidxu\swiper;


use davidxu\base\widgets\InputWidget;
use davidxu\swiper\assets\SwiperAsset;
use Yii;
use yii\base\InvalidValueException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\i18n\PhpMessageSource;
use yii\web\JsExpression;

/**
 * A Swiper widget is adapter to javascript Swiper slider
 *
 * @link    https://swiperjs.com/
 *
 * @package davidxu\swiper
 */
class Swiper extends InputWidget
{
    /**
     * Swiper items
     * @example
     * [
     *      Html::img('http://img.zcool.cn/community/01665258173c34a84a0d304fc68fdf.jpg'),
     *      Html::img('http://img.zcool.cn/community/01665258173c34a84a0d304fc68fdf.jpg'),
     *      Html::img('http://img.zcool.cn/community/01665258173c34a84a0d304fc68fdf.jpg'),
     * ]
     * @var array
     */
    public array $slides = [];

    public array $pluginOptions = [];

    /**
     * Swiper Pagination
     * false: do not show pagination
     * true: show pagination in default
     * @link https://swiperjs.com/swiper-api#pagination
     * @var bool|array
     */
    public array|bool $pagination = false;

    /**
     * Swiper navigation
     * false: do not show navigation
     * true: show navigation in default
     * @link https://swiperjs.com/swiper-api#navigation
     * @var bool|array
     */
    public array|bool $navigation = false;

    /**
     * Swiper scroll navigation
     * false: do not show
     * true: show scroll navigation in default
     * @link https://swiperjs.com/swiper-api#scrollbar
     * @var bool|array
     */
    public array|bool $scrollbar = false;

    /**
     * @var string
     */
    public string $swiperEl;

    public function init(): void
    {
        parent::init();
        $this->_view = $this->getView();
        $this->registerTranslations();

        if (!$this->slides) {
            throw new InvalidValueException(Yii::t('swiper', 'no slides found'));
        }

        $this->_wrapContainerId = $this->id . '-swiper-container';

        if ($this->pagination) {
            if (!is_array($this->pagination)) {
                $this->pagination = [];
            }
            $this->_paginationId = $this->id . '-swiper-pagination';
            $this->pagination['el'] = '#' . $this->_paginationId;
            $this->clientOptions['pagination'] = $this->pagination;
        }

        if ($this->navigation) {
            if (!is_array($this->navigation)) {
                $this->navigation = [];
            }
            $this->_navigationNextId = $this->id . '-swiper-button-next';
            $this->_navigationPrevId = $this->id . '-swiper-button-prev';
            $this->navigation['nextEl'] = '#' . $this->_navigationNextId;
            $this->navigation['prevEl'] = '#' . $this->_navigationPrevId;
            $this->clientOptions['navigation'] = $this->navigation;
        }

        if ($this->scrollbar) {
            if (!is_array($this->scrollbar)) {
                $this->scrollbar = [];
            }
            $this->_scrollbarId = $this->id . '-swiper-scrollbar';
            $this->scrollbar['el'] = '#' . $this->_scrollbarId;
            $this->clientOptions['scrollbar'] = $this->scrollbar;
        }

        if (!$this->swiperEl) {
            $this->swiperEl = $this->id . 'Swiper';
        }
    }

    public function run()
    {
        $containerContent = [];
        $slideContent = [];
        foreach ($this->slides as $index => $slide) {
            $slideContent[] = Html::tag('div', $slide, ['class' => 'swiper-slide slide-' . $index]);
        }
        $containerContent[] = Html::tag('div', implode("\n", $slideContent), ['class' => 'swiper-wrapper']);

        if ($this->pagination) {
            $containerContent[] = Html::tag('div', '', ['id' => $this->_paginationId, 'class' => 'swiper-pagination']);
        }

        if ($this->navigation) {
            $containerContent[] = Html::tag('div', '', ['id' => $this->_navigationPrevId, 'class' => 'swiper-button-prev']);
            $containerContent[] = Html::tag('div', '', ['id' => $this->_navigationNextId, 'class' => 'swiper-button-next']);
        }

        if ($this->scrollbar) {
            $containerContent[] = Html::tag('div', '', ['id' => $this->_scrollbarId, 'class' => 'swiper-scrollbar']);
        }

        Html::addCssClass($this->wrapOptions, 'swiper-container');
        $html = Html::tag('div', implode("\n", $containerContent), array_merge($this->wrapOptions, [
            'id' => $this->_wrapContainerId,
        ]));

        $this->registerAssets();

        return $html;
    }

    /**
     * 注册资源
     */
    protected function registerAssets()
    {
        SwiperAsset::register($this->view);
        if ($this->slideImageFullWidth) {
            $css = /** @lang text/css */ <<<CSS
        #{$this->_wrapContainerId} img {
            width: 100%;
        }
CSS;
            $this->view->registerCss($css);
        }

        $clientOptions = Json::encode($this->clientOptions);
        $js = new JsExpression("let {$this->swiperEl} = new Swiper('#{$this->_wrapContainerId}', {$clientOptions})");
        $this->view->registerJs($js);
    }

    protected function registerTranslations(): void
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['swiper*'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@davidxu/swiper/messages',
            'fileMap' => [
                '*' => 'swiper.php',
            ],
        ];
    }
}
