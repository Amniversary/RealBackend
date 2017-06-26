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
class QueryMd5Asset extends AssetBundle
{
    public $sourcePath = '@bower/jqureymd5/';
/*    public $basePath = '@webroot';
    public $baseUrl = '@web';*/
    public $css = [
    ];
    public $js = [
        'http://image.matewish.cn/commonjs/jquery.min.md5.js'
        //'jquery.min.md5.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
