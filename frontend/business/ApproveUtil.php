<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2015/5/4
 * Time: 14:57
 */

namespace frontend\business;
use common\components\PhpLock;
use common\components\SendShortMessage;
use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use common\models\Approve;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckApproveSaveByTrans;
use yii\db\Query;
use yii\log\Logger;

/**
 * 认证
 * Class ApproveUtil
 * @package frontend\business
 */
class ApproveUtil
{
    /**
     * 验证字符串长度
     * @param $name
     * @param int $len
     * @return bool
     */
    public static function CheckActualNameLen($name,$len=3){
        if(mb_strlen($name,'utf-8') > $len){
            return false;
        }
        return true;
    }
    /**
     * 验证手机号码
     * @param $phone_num
     * @return bool
     */
    public static function PregMatchPhoneNum($phone_num){
        if(!preg_match("/^(13[0-9]|14[0-9]|15[0-9]|18[0-9]|17[0-9])\d{8}$/i",$phone_num)){
           return false;
        }
        return true;
    }

    /**
     * 验证身份证号码
     * @return bool
     */
    public static function PregMatchIDcard($idcard_num){
            if(!preg_match("/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/",$idcard_num)){
            return false;
        }
        return true;
    }

    /**
     * 通过ID获取数据
     * @param $approve_id
     * @return null|static
     */
    public static function GetApproveById($approve_id)
    {
        return Approve::findOne(['approve_id' => $approve_id]);
    }

    /**
     * 通过认证ID获取高级认证审核信息
     * @param $approve_id
     * @return array|bool
     */
    public static function GetApproveBusinessCheckById($approve_id,$status=0)
    {
        $query = (new Query())
             ->select(['a.wechat','a.qq','a.account_name','a.family_name','a.bank','a.address','u.client_id','approve_id','actual_name','bank_account','a.phone_num','a.id_card','a.create_time','id_card_pic_all','id_card_pic_main','id_card_pic_turn',
                 'c.check_time','c.check_user_name','c.create_user_name','refused_reason','c.create_user_name','c.status','u.nick_name','c.business_check_id'])
             ->from('mb_approve a')
             ->innerJoin('mb_client u','u.client_id=a.client_id')
             ->innerJoin('mb_business_check c','c.relate_id=a.approve_id')
             ->where('c.status =:stus and business_type=3 and approve_id=:aid',[
                 ':stus' => $status,
                 ':aid' => $approve_id
             ])->one();
        return$query;
    }


    /**
     * 通过认证ID获取低级认证审核信息
     * @param $approve_id
     * @return array|bool
     */
    public static function GetApproveElementaryById($approve_id,$status=0)
    {
        $query = (new Query())
            ->select(['a.wechat','a.qq','a.account_name','a.family_name','a.bank','a.address','u.client_id','approve_id','actual_name','bank_account','a.phone_num','a.id_card','a.create_time','id_card_pic_all','id_card_pic_main','id_card_pic_turn',
                'c.check_time','c.check_user_name','c.create_user_name','refused_reason','c.create_user_name','c.status','u.nick_name','c.business_check_id'])
            ->from('mb_approve a')
            ->innerJoin('mb_client u','u.client_no=a.client_no')
            ->innerJoin('mb_business_check c','c.relate_id=a.approve_id')
            ->where('c.status =:stus and business_type=4 and approve_id=:aid',[
                ':stus' => $status,
                ':aid' => $approve_id
            ])->one();
        return$query;
    }

    /**
     * 设置审核动作
     * @param $params
     * @param $error
     * @return bool
     */
    public static function CheckRefuse($params,&$error)
    {
        $all_params = [
            'approve_id' => $params['approve_id'],
            'refuesd_reason' => $params['refuesd_reason'],
            'check_result_status' => $params['check_rst'],
            'approve_status' => $params['check_rst'],
            'check_user_id' => $params['admin_user_id'],
            'check_user_name' => $params['admin_username'],
            'create_user_id' => $params['user_id'],
            'status' => $params['status'],
            'business_check_id' =>  $params['business_check_id'],
        ];
        //审核记录更新
        $transActions[] = new CheckApproveSaveByTrans($all_params);

        if (!RewardUtil::RewardSaveByTransaction($transActions, $outInfo, $error)) {
            return false;
        }

        return true;
    }

    /**
     * 向认证表添加数据
     * @param $datas
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function AddApprove($datas){
        $sql = 'insert into mb_approve (actual_name,bank_account,phone_num,id_card,id_card_pic_all,id_card_pic_main,id_card_pic_turn,client_id,create_time)
 values(:aname,:account,:pnum,:idcard,:picall,:picmain,:picturn,:cid,:ctime)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':aname' => $datas['actual_name'],
            ':account' => $datas['bank_account'],
            ':pnum' => $datas['phone_num'],
            ':idcard' => $datas['id_card'],
            ':picall' => $datas['id_card_pic_all'],
            ':picmain' => $datas['id_card_pic_main'],
            ':picturn' => $datas['id_card_pic_turn'],
            ':cid' => $datas['client_id'],
            ':ctime' => date('Y-m-d H:i:s',time()),
        ])->execute();
        if($result <= 0){
            return false;
        }
        return true;
    }

    /**
     * 通过client_id获取信息
     * @param $phone_num
     */
    public static function GetApproveByUserId($client_id){
        $query = new Query();
        $result = $query->select(['cl.is_centification','app.approve_id','app.create_time','app.status','cl.status as client_status','app.actual_name','app.bank_account','app.phone_num','app.id_card','id_card_pic_all','id_card_pic_main','id_card_pic_turn','app.client_id'])
            ->from('mb_approve app')
            ->innerJoin('mb_client cl','cl.client_id=app.client_id')
            ->where('app.client_id=:cid',[
                ':cid' => $client_id
            ])->one();
        return $result;
    }

    /**
     * 获取人脸识别记录信息
     * @param $user_id
     * @param $week
     * @return array|bool
     */
    public static function GetUserFaceInfo($user_device_id,$date,$type = 1)
    {
        $query = (new Query())
            ->select(['face_id','user_device_id','type','date','request_num'])
            ->from('mb_face_statistic')
            ->where('user_device_id=:udid and date=:dt and type=:tp',[':udid' => $user_device_id,':dt' => $date,':tp' => $type])->one();
        return $query;
    }

    /**
     * TODO: 验证有盾签名
     * @param $data
     * @param $securityKey
     * @return string
     */
    public static function GetYouDunSignMD5($data,$securityKey)
    {
        $sign_fields = explode("|", $data['sign_field']);
        $fieldsValue = "";
        foreach ($sign_fields as $field)
        {
            $fieldsValue .= $field . "=" . $data[$field] . "|";
        }
        $fieldsValue = substr($fieldsValue, 0, strlen($fieldsValue) - 1);
        //php 文件编码需设置为 UTF - 8
        $signMD5 = strtoupper(md5($fieldsValue . $securityKey));
        return $signMD5;
    }
}