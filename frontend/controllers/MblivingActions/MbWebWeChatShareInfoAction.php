<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/24
 * Time: 15:20
 */
namespace frontend\controllers\MblivingActions;

use common\components\DeviceUtil;
use common\components\WeiXinUtil;

use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWebWeChatShareInfoAction extends Action
{
    public function run()
    {
        $living_id = $datas = \Yii::$app->request->post('living_id');
        $url =  \Yii::$app->request->post('url');
        $error_msg = '';
        if(empty($living_id)){
            $error_msg = '直播ID不能为空';
        }
//       $url = \Yii::$app->request->getAbsoluteUrl();
//        \Yii::getLogger()->log('living_id======'.$living_id,Logger::LEVEL_ERROR);

        $sign = WeiXinUtil::GetShareSign($url);

        $living_info = LivingUtil::GetLivingInfo($living_id);
        if(empty($living_info['living_title'])){
            $living_info['living_title'] = '上蜜播有福利';//'陪朋友吃饭，不如上蜜播扯淡！';
        }
        $shareInfo = [];
        $shareInfo['title'] = $living_info['living_title'];
        $shareInfo['content'] = '我在蜜播和帅哥美女聊天，就等你来！';
        $shareInfo['link'] = $url;
        $shareInfo['pic'] = $living_info['pic'];
//        \Yii::getLogger()->log('$living_info==share=:'.$living_info,Logger::LEVEL_ERROR);
//        $isWx = DeviceUtil::IsMobileWeixinBrowse();

        $arr_data = [
            'error_msg' => $error_msg,
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