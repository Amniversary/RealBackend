<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/17
 * Time: 11:31
 */

namespace frontend\zhiboapi\v3;

use common\components\ValidateCodeUtil;
use common\models\Approve;
use common\models\Client;
use common\models\OffUserLiving;
use frontend\business\ClientUtil;
use frontend\business\ApproveUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;
use common\components\SystemParamsUtil;


/**
 * 直播验证
 * @package frontend\zhiboapi\v3
 */
class ZhiBoCreateLivingApprove implements IApiExcute
{

    /**
     * @param $dataProtocal
     * @param $rstData
     * @param $error
     * @param array $extendData
     * @return bool
     * @throws \yii\db\Exception
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        //根据用户的信息找到用户是否已经认证过
        $user_info = Client::findOne(['unique_no' => $dataProtocal['data']['unique_no']]);
        if (!$user_info) {
            $error = '用户不存在';
            return false;
        }
        $approve_phone = Approve::findOne(['phone_num' => $dataProtocal['data']['phone_number'] ]);

        if($user_info['is_centification'] > 1 && $approve_phone['status'] == 1)
        {
            $error = '您已经认证通过了';
            return false;
        }

        //验证手机号码
        if(!ApproveUtil::PregMatchPhoneNum($dataProtocal['data']['phone_number']))
        {
            $error = '手机号码不正确';
            return false;
        }

	    $open = SystemParamsUtil::GetSystemParam('is_open_face',true,'value1');
        if ($open != 1) {
            $white_user = OffUserLiving::findone(['client_no' => $user_info['client_no']]);
            if(!isset($white_user)) {
                $error = '开播请添加客服QQ';
                return false;
            }
	    }

        //验证验证码是否正确
        if(!ValidateCodeUtil::CheckValidateCode($dataProtocal['data']['phone_number'],5,$dataProtocal['data']['verification_code'])){           //验证码验证
            $error = '验证码不正确';
            return false;
        }

        $transation = \Yii::$app->db->beginTransaction();
        //验证成功后修改个人信息表的数据
        $sql = 'update mb_client set is_centification = :ic,phone_no = :pn WHERE unique_no = :un';
        $client_res = \Yii::$app->db->createCommand($sql,[
            ':ic' => 2,
            ':un' => $dataProtocal['data']['unique_no'],
            ':pn' => $dataProtocal['data']['phone_number'],
        ])->execute();
        $InsertSql = 'insert ignore into mb_approve(client_id,client_no) VALUES (:cid,:cn)';
        \Yii::$app->db->createCommand($InsertSql,[
            ':cid' => $user_info['client_id'],
            ':cn' => $user_info['client_no'],
        ])->execute();
        $sqls = 'update mb_approve set create_time=:ctime,phone_num=:pn WHERE client_id=:cid';
        $result = \Yii::$app->db->createCommand($sqls,[
            ':ctime' => date('Y-m-d H:i:s',time()),
            ':cid' => $user_info['client_id'],
            ':pn' => $dataProtocal['data']['phone_number'],
        ])->execute();

        if(($client_res <=0) || ($result <=0))
        {
            $transation->rollBack();
            $error = '认证失败';
            \Yii::getLogger()->log('手机认证失败',Logger::LEVEL_ERROR);
            return false;
        }
        $transation->commit();
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'string';
        $rstData['data']['is_approve'] = '1';
        return true;
    }

}