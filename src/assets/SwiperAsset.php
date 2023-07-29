<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\swiper\assets;

use yii\web\AssetBundle;

class SwiperAsset extends AssetBundle
{
    public $sourcePath = "@npm/swiper/";

    public $js = [
        'swiper-bundle' . (YII_ENV_PROD ? '' : '.min') . '.js',
    ];
    
    public $css = [
        'swiper-bundle' . (YII_ENV_PROD ? '' : '.min') . '.css',
    ];
}
