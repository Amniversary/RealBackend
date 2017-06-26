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
class ArtDialogAsset extends AssetBundle
{
    public $sourcePath = '@bower/artDialog/artDialog4.1.7';
/*    public $basePath = '@webroot';
    public $baseUrl = '@web';*/
    public $css = [
    ];
    public $js = [
        /*'jquery.artDialog.js?skin=default',
        'plugins/iframeTools.js'*/
        'http://mbpic.mblive.cn/mibo-js/artDialog.js?skin=aero',
        'http://mbpic.mblive.cn/mibo-js/iframeTools.js',
        'http://mbpic.mblive.cn/mibo-js/masonry-docs.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
