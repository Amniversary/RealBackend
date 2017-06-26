<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class WxAsset extends AssetBundle
{
    //public $sourcePath = '@common';
    public $js = [
        'http://res.wx.qq.com/open/js/jweixin-1.0.0.js',
    ];
}
