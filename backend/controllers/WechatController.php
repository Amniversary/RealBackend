<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/28
 * Time: 上午9:05
 */

namespace backend\controllers;


use yii\web\Controller;

class WechatController extends Controller
{
    public function actionTest()
    {
        echo "ok";
        exit;
    }

    public function actionCallback()
    {
        echo "<pre>";
        echo "is callback";
        echo "<br/>";
        $rules = (\Yii::$app->request->getPathInfo());
        $AppId = $this->getRulesAppid($rules);
        print_r($rules);
        echo "<br/>";

        exit;
    }

    /**
     * 截取微信回调的动态路由
     * @param $rules //请求Url地址 去掉host test：wechat/wx1283196321321/callback
     * @return string  //AppId 微信公众号的原始ID
     */
    private function getRulesAppid($rules)
    {
        $strstr = strstr($rules,"/");
        $strrpos = strtok($strstr,"/");
        return $strrpos;
    }
}