<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/31
 * Time: 14:15
 */
namespace frontend\controllers\MblivingActions;
use common\models\LivingPrivate;
use frontend\business\LivingUtil;
use yii\base\Action;

class MbWebOpenLivingAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>'','type'=>''];
        $datas = \Yii::$app->request->post();
        $living_id = $datas['living_id'];
        $proving = $datas['proving'];
        $living_password = $datas['living_password'];


        /**
         * type 1 私密直播
         * type 2 门票直播
         */


        //根据直播ID找到房间的直播信息 直播信息中有3直播类型$living_info['living_type']
        // 1 普通直播
        // 2 私密直播
        // 3 门票直播
        $living_info = LivingUtil::GetClientLivingInfo($living_id);

        // private_status 1：私密直播 0：否

        //proving 1：打开视频 2：验证密码
        if($proving == 1)
        {
            if($living_info['living_type'] == 3)
            {
                $rst['code'] = '0';
                $rst['msg'] = '这个直播是私密直播';
                $rst['type'] = '1';
                echo json_encode($rst);
                exit;
            }
            if($living_info['living_type'] == 4)
            {
                $rst['code'] = '0';
                $rst['msg'] = '这个直播是门票直播';
                $rst['type'] = '2';
                echo json_encode($rst);
                exit;
            }
        }

        //$proving为2时 肯定是私密直播
        if($proving == 2)
        {
            if($living_info['living_type'] == 3)
            {
                //验证秘密是否正确
                if($living_info['password'] !== $living_password)
                {
                    $rst['msg'] = '直播密码错误';
                    echo json_encode($rst);
                    exit;
                }
            }
            else
            {
                $rst['msg'] = '直播类型不正确';
                echo json_encode($rst);
                exit;
            }
        }


        $rst['code'] = '0';
        $rst['msg'] = '执行成功';
        echo json_encode($rst);
        exit;
    }
}




