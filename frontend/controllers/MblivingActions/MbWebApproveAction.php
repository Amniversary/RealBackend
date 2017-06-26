<?php
/**
 * 直播个人银行卡认证
 * User: hlq
 * Date: 2016/5/4
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use common\components\UsualFunForStringHelper;
use common\components\ValidateCodeUtil;
use frontend\business\ApiCommon;
use frontend\business\ApproveUtil;
use frontend\business\ClientUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AddApproveSaveByTrans;
use yii\base\Action;
use yii\log\Logger;

class MbWebApproveAction extends Action
{
    public function run()
    {
        header('Content-Type: application/json; charset=utf-8');
//        $datas = \Yii::$app->request->post();
        $datas = json_decode(file_get_contents("php://input"),true);
        \Yii::getLogger()->log('post=:'.var_export($datas,true),Logger::LEVEL_ERROR);
        $rand_str = $datas['rand_str'];
        $time = $datas['time'];
        $post_sign = $datas['p_sign'];
        $unique_no = $datas['unique_no'];
//        $phone_num = $datas['phone_num'];
        $id_card = $datas['id_card'];
        $params = [
            'unique_no' => $unique_no,
            'time' => $time,
            'rand_str' =>$rand_str,
        ];
        $sign = ClientUtil::GetClientSign($params);  //签名验证
        if($post_sign !== $sign){
            $arr_data = ['error_msg' => '签名不正确'];
            echo  json_encode($arr_data);
            exit;
        }

//        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error)){ //是否登录
//            $arr_data = ['error_msg' => '未登录'];
//            echo  json_encode($arr_data);
//            exit;
//        }

//        $key = 'mb_api_verifycode_'.$datas['verify'].'_'.$phone_num;
//        $tmpVcode = \Yii::$app->cache->get($key);
//        \Yii::getLogger()->log('verify=:'.$datas['verify'],Logger::LEVEL_ERROR);
//
//        if(!ValidateCodeUtil::CheckValidateCode($phone_num,6,$datas['verify'])){  //验证码验证
//            $arr_data = ['error_msg' => '验证码不正确'];
//            echo  json_encode($arr_data);
//            exit;
//        }

        $user = ClientUtil::GetUserByUniqueId($unique_no);
        if(!$user->client_id){
            $arr_data = ['error_msg' => '用户不存在'];
            echo  json_encode($arr_data);
            exit;
        }


        $datas['client_id'] = $user->client_id;
        $datas['nick_name'] = $user->nick_name;
        $fiels = ['actual_name','id_card'];
        $attr_fiels = ['真实姓名','身份证号'];
        for($i = 0;$i<count($fiels);$i++){
            if(!isset($datas[$fiels[$i]]) || empty($datas[$fiels[$i]])){
                $arr_data = ['error_msg' => $attr_fiels[$i].'不能为空'];
                echo  json_encode($arr_data);
                exit;
            }
        }

        if(!ApproveUtil::CheckActualNameLen($datas['actual_name'],20)){
            $arr_data = ['error_msg' => '真实姓名长度不能超过6个汉字'];
            echo  json_encode($arr_data);
            exit;
        }

        if(!ApproveUtil::PregMatchIDcard($id_card)){
            $arr_data = ['error_msg' => '身份证号码不正确'];
            echo  json_encode($arr_data);
            exit;
        }

        $result = ApproveUtil::GetApproveByUserId($user->client_id);

//        !empty($result)
        if($user->is_centification == 1 || $user->is_centification == 5)
        {
            $arr_data = ['error_msg' => '您的账号未通过低级认证，请开通直播后再来进行认证'];
            echo  json_encode($arr_data);
            exit;
        }
        if($user->is_centification == 2)
        {
            $arr_data = ['error_msg' => '您已经认证过，无需再验证'];
            echo  json_encode($arr_data);
            exit;
        }
        if($user->is_centification == 3 )
        {
            \Yii::getLogger()->log('您已提交过审核认证信息   $result===:'.var_export($result,true),Logger::LEVEL_ERROR);
            $arr_data = ['error_msg' => '您已提交过审核认证信息'];
            echo  json_encode($arr_data);
            exit;
        }

        $transActions[] = new AddApproveSaveByTrans($datas);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }

//        if(!ApproveUtil::AddApprove($datas)){
//            $arr_data = ['error_msg' => '插入失败'];
//            echo  json_encode($arr_data);
//            exit;
//        }

        $arr_data = ['error_msg' => 'ok'];

        echo  json_encode($arr_data);
        exit;
    }
}




