<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/22
 * Time: 16:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\components\DbUtil;
use common\components\Des3Crypt;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\models\Balance;
use common\models\Client;
use common\models\ClientActive;
use common\models\ClientFansGroup;
use common\models\ClientOther;
use common\models\ClientQiniu;
use common\models\FansGroup;
use common\models\FansGroupMember;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class FansGroupSaveByTrans implements ISaveForTransaction
{

    private $getRecord = null;
    private $extend_params=[];

    /**
     * 注册信息保存
     * @param $record
     * @param array $extend_params
     */
    public function __construct($record,$extend_params=[])
    {
        $this->getRecord = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {


        //先查找粉丝群记录
        $client_fans_group = ClientFansGroup::findOne(['user_id'=>$this->getRecord]);
        if(isset($client_fans_group)){
            $client_fans_group['is_created_group'] = 1;
            if(!$client_fans_group->save()){
                $error = '用户粉丝表修改is_created_group状态失败';
                \Yii::getLogger()->log($error.' :'.var_export($client_fans_group->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
        }else{
            //用户粉丝群记录插入一条数据
            $data = [
                'user_id'=>$this->getRecord,
                'is_created_group'=>1
            ];

            $client_fans_group = new ClientFansGroup();
            $client_fans_group->attributes = $data;

            if(!$client_fans_group->save()){
                $error = '用户粉丝表插入数据失败';
                \Yii::getLogger()->log($error.' :'.var_export($client_fans_group->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
        }

        //获取用户头像和昵称
        $user_info  = $query = (new Query())
            ->select(['icon_pic', 'pic', 'nick_name'])
            ->from('mb_client')
            ->where('client_id = :uid', [':uid' => $this->getRecord])
            ->one();
        $pic = $user_info['icon_pic'] == ''?$user_info['pic']:$user_info['icon_pic'];
        if(strlen($user_info['nick_name']) >= 18)
        {
            $user_info['nick_name'] = substr($user_info['nick_name'],0,18);
        }
        $group_name = $user_info['nick_name'].'的粉丝群';

        if(strlen($pic) >= 100)
        {
            $pic = 'http://q.qlogo.cn/qqapp/1105405817/327EC4B7266B1834CC9D82EC94D0B632/100';
        }

        //腾讯粉丝群创建
        $group_type = 'Public';
        $owner_id = $this->getRecord;
        $ret = TimRestApi::group_create_group($group_type, $group_name, $owner_id, $pic, $error);
        if(!$ret){
            \Yii::getLogger()->log("_________________________________________".$error,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log($pic.$error,Logger::LEVEL_ERROR);
            return false;
        }

        $group_id_new = $ret['GroupId'];


        //建一个新的粉丝群
        $data2 = [
            'group_master_id'=>$this->getRecord,
            'pic' => $pic,
            'group_name' => $group_name,
            'tx_group_id' => $group_id_new
        ];

        $fans_group = new FansGroup();

        $fans_group->attributes = $data2;

        if(!$fans_group->save()){
            $error = '粉丝群表建群失败';
            \Yii::getLogger()->log($error.' :'.var_export($fans_group->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $group_id = $fans_group['group_id'];

        //群成员插入一条数据
        $data3 = [
            'user_id'=>$this->getRecord,
            'group_id'=>$group_id,
            'group_member_type'=>2,
        ];

        $fans_group_member = new FansGroupMember();
        $fans_group_member->attributes = $data3;

        if(!$fans_group_member->save()){
            $error = '群成员表插入群主失败';
            \Yii::getLogger()->log($error.' :'.var_export($fans_group_member->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $outInfo = [
            'group_id' => $group_id,
            'tx_group_id' => $group_id_new
        ];
        return true;
    }
} 