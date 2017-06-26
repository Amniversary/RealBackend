<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/24
 * Time: 15:20
 */
namespace frontend\controllers\MblivingActions;

use common\components\WeiXinUtil;

use yii\base\Action;

class MbWebGetWeCachatSignAction extends Action
{
    public function run()
    {
        $datas = \Yii::$app->request->post();
        $url = $datas['url'];
        if(empty($url)){
            $arr_data = ['error_msg' => '参数错误'];
            echo  json_encode($arr_data);
            exit;
        }
        $sign = WeiXinUtil::GetShareSign($url);
        $shareInfo = [];
        $shareInfo['title'] = '我就是网红';
        $shareInfo['content'] = '我就是网红!';
        $shareInfo['link'] = $url;
        $shareInfo['pic'] = 'http://mbpic.mblive.cn/meibo-test/logo_100.png';
        $arr_data = [
            'error_msg' => 'ok',
            'sign' => $sign,
            'title' => $shareInfo['title'],
            'content' => $shareInfo['content'],
            'link' => $shareInfo['link'],
            'pic' => $shareInfo['pic'],
        ];
        echo  json_encode($arr_data);
        exit;

    }
}