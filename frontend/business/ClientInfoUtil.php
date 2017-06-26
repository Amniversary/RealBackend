<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/25
 * Time: 17:26
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\AlipayForCash;
use common\models\Attention;
use common\models\BlackList;
use common\models\Client;
use common\models\ClientOther;
use common\models\CommonWords;
use yii\db\Query;
use yii\log\Logger;

class ClientInfoUtil {

    /**
     * 根据用户id 获取用户个人资料
     * @param $user_id  查看用户的id
     * @param $outInfo  返回信息
     * @param $my_user_id  登录信息中的用户id
     * @param $fields  获取的用户信息参数
     * @param $back  额外拼接的参数
     * @param $error
     * @return bool
     */
    public static function GetUserData($fields,$user_id,$my_user_id,$back,&$outInfo,&$error)
    {
        $params[':ct'] = $user_id;
        $outInfo = [];

        if(!empty($fields))
        {
            $query = (new Query)
                //->select(['nick_name','client_no','pic','level_id','ls.level_pic','font_size','color','level_bg','sex','age','city','sign_name','attention_num','funs_num','ticket_count_sum','ticket_count','ticket_real_sum','bean_balance','is_bind_weixin','is_bind_alipay','is_contract','is_centification'])
                ->select($fields)
                ->from('mb_client bc')
                ->innerJoin('mb_client_active ca','bc.client_id=ca.user_id')
                ->innerJoin('mb_balance bb','ca.user_id=bb.user_id')
                ->innerJoin('mb_level bl','bl.level_id=ca.level_no')
                ->leftJoin('mb_living ll','ll.living_master_id=bc.client_id')
                ->leftJoin('mb_alipay_for_cash afc','bc.client_id = afc.user_id')
                ->leftJoin('mb_level_stage ls','ls.level_stage=bl.level_max')
                ->leftJoin('mb_time_livingmaster_ticketcount lt','lt.livingmaster_id = bc.client_id and hot_type = 1 and statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\')')
                ->leftJoin('mb_fans_group fg','fg.group_master_id=bc.client_id')
//                ->leftJoin('mb_living_private lp','lp.living_master_id=bc.client_id')
                ->where('client_id = :ct',$params)
                ->all();

            if(empty($query))
            {
                $error = '用户信息不存在，查找失败';
                \Yii::getLogger()->log($error.': user_id:'.$user_id.' -----fields:'.(new Query)
                        ->select($fields)
                        ->from('mb_client bc')
                        ->innerJoin('mb_client_active ca','bc.client_id=ca.user_id')
                        ->innerJoin('mb_balance bb','ca.user_id=bb.user_id')
                        ->innerJoin('mb_level bl','bl.level_id=ca.level_no')
                        ->leftJoin('mb_living ll','ll.living_master_id=bc.client_id')
                        ->leftJoin('mb_alipay_for_cash afc','bc.client_id = afc.user_id')
                        ->leftJoin('mb_level_stage ls','ls.level_stage=bl.level_max')
                        ->leftJoin('mb_time_livingmaster_ticketcount lt','lt.livingmaster_id = bc.client_id and hot_type = 1 and statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\')')
                        ->leftJoin('mb_fans_group fg','fg.group_master_id=bc.client_id')
                        ->where('client_id = :ct',$params)
                        ->createCommand()->rawSql,Logger::LEVEL_ERROR);
                return false;
            }
            foreach($query as $info)
            {
                $outInfo = $info;
            }
        }

//        $outInfo['is_police'] = ($client_type == '2' ? 1 : 0);
//        //判断是在直播
//        if($outInfo['is_live']==2)
//        {
//            /******私密直播*******/
//            $private_cache = \Yii::$app->cache->get('private_living_info_'.$outInfo['living_id']);
//            $private_cache = json_decode($private_cache,true);
//            if($private_cache['private_status'] == 1)
//            {
//                $outInfo['private_status'] = 1;
//                $outInfo['views_status'] = 1;
//                $views_cache = \Yii::$app->cache->get('living_'.$private_cache['living_id'].'_before_'.$private_cache['living_before_id'].'_user_'.$private_cache['user_id']);
//                if($views_cache)
//                {
//                    $outInfo['views_status'] = 0;
//                }
//            }
//            else
//            {
//                $outInfo['private_status'] = 0;
//                $outInfo['views_status'] = 0;
//            }
//        }else{
//            //不在直播中
//            $outInfo['private_status'] = 0;
//            $outInfo['views_status'] = 0;
//        }
        if(in_array('cash_rite',$fields))
        {
            if(!isset($outInfo['cash_rite']))
            {
               $outInfo['cash_rite'] = 0;
            }
        }

        if(in_array('first_reward',$back))
        {
            $outInfo['first_reward'] = ChatFriendsUtil::GetFirstContribution($user_id);
        }

        if(in_array('is_attention',$back))
        {
            $outInfo['is_attention'] = ClientInfoUtil::IsAttention($my_user_id,$user_id);
        }

        if(in_array('is_black',$back))
        {
            $outInfo['is_black'] = ClientInfoUtil::IsBlack($my_user_id,$user_id);
        }
        if(in_array('is_join',$back))
        {
            $outInfo['is_join'] = 0;
            if(!empty($outInfo['group_id']))
            {
                $outInfo['is_join'] = FansGroupUtil::IsJoinFansGroup($outInfo['group_id'],$my_user_id,$user_id);
            }
        }
        return true;
    }

    /**
     * 更具多个用户id 获取信息
     * @param $user_id  查看用户的id
     * @param $outInfo  返回信息
     * @param $my_user_id  登录信息中的用户id
     * @param $fields  获取的用户信息参数
     * @param $back  额外拼接的参数
     * @param $error
     * @return bool
     */
    public static function GetUserDataParams($fields,$user_id,$my_user_id,$back,&$outInfo,&$error)
    {
        /*if(in_array('today_ticket_num',$back))
        {
            $params[':ld'] = $user_id;
        }*/

        $params[':ud'] = $user_id;
        $outInfo = [];
        if(!empty($fields))
        {
            $query = (new Query)
                //->select(['nick_name','client_no','pic','level_id','ls.level_pic','font_size','color','level_bg','sex','age','city','sign_name','attention_num','funs_num','ticket_count_sum','ticket_count','ticket_real_sum','bean_balance','is_bind_weixin','is_bind_alipay','is_contract','is_centification'])
                ->select($fields)
                ->from('mb_client bc')
                ->innerJoin('mb_client_active ca','bc.client_id=ca.user_id')
                ->innerJoin('mb_balance bb','ca.user_id=bb.user_id')
                ->innerJoin('mb_level bl','bl.level_id=ca.level_no')
                ->leftJoin('mb_living ll','ll.living_master_id=bc.client_id')
                ->leftJoin('mb_alipay_for_cash afc','bc.client_id = afc.user_id')
                ->leftJoin('mb_level_stage ls','ls.level_stage=bl.level_max')
                ->leftJoin('mb_time_livingmaster_ticketcount lt','lt.livingmaster_id = bc.client_id and hot_type = 1 and statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\')')
                ->where('client_id in ('. $user_id .')',$params)
                ->all();

            if(empty($query))
            {
                $error = '用户信息不存在，查找失败';
                \Yii::getLogger()->log($error.': '.var_export($query,true).' -----fields:'.var_export($fields,true),Logger::LEVEL_ERROR);
                return false;
            }
            foreach($query as $info)
            {
                $outInfo[] = $info;
            }
        }
        foreach($outInfo as $test)
        {
            $s = array_search($test,$outInfo);
            if(in_array('cash_rite',$fields))
            {
                if(empty($test['cash_rite']))
                {
                    $test['cash_rite'] = 0;
                }
            }
            if(in_array('first_reward',$back))
            {
                $test['first_reward'] = ChatFriendsUtil::GetFirstContribution($test['user_id']);
            }
            if(in_array('is_attention',$back))
            {
                $test['is_attention'] = ClientInfoUtil::IsAttention($my_user_id,$test['user_id']);
            }
            if(in_array('is_black',$back))
            {
                $test['is_black'] = ClientInfoUtil::IsBlack($my_user_id,$test['user_id']);
            }
            $outInfo[$s] = $test;
        }

        return true;
    }

    /**
     * 是否关注
     * @param $user_id
     * @param $self_user_id
     * @return string
     */
    public static function IsAttention($self_user_id,$user_id)
    {
        if($user_id == $self_user_id)
        {
            return 0;
        }
        $query = Attention::find()->where('user_id = :ud and friend_user_id = :fd',[
            ':ud'=>$self_user_id,
            ':fd'=>$user_id
        ])->one();

        if(empty($query))
        {
            return 0;
        }

        return 1;
    }

    /**
     * 是否拉黑
     * @param $friend_user_id
     * @param $self_user_id
     * @return int|mixed
     */
    public static function IsBlack($self_user_id,$friend_user_id)
    {
        if($friend_user_id == $self_user_id)
        {
            return 1;
        }
        $query = BlackList::find()->where('user_id = :ud and black_user_id = :fd',[
            ':ud'=>$self_user_id,
            ':fd'=>$friend_user_id
        ])->one();

        if(empty($query))
        {
            return 1;
        }

        return $query->hide_msg;
    }
    /**
     * 获取票转豆商品列表信息
     * @param $outInfo
     * @param $error
     * @return bool
     */
    public static function GetBeanGoods(&$outInfo,&$error)
    {
        $query=new Query();
        $sql = $query
            ->select(['record_id','ticket_num','pic','bean_num'])
            ->from('mb_to_bean_goods')
            ->where('status = 1')
            ->orderBy('ticket_num asc')
            ->all();

        if(empty($sql))
        {
            $error = '票转豆商品列表不存在，查找失败';
            \Yii::getLogger()->log($error.' '.var_export($sql,true),Logger::LEVEL_ERROR);
            return false;
        }
        $outInfo = [];
        foreach($sql as $info)
        {
            $outInfo[] = $info;
        }

        return true;
    }



    /**
     * 绑定第三方信息记录
     * @param $LoginInfo
     * @param $openid
     * @param $registerType
     * @param $error
     * @return bool
     */
    public static function GetBindInfo($LoginInfo,$openid,$registerType,&$error)
    {

        /*$sql = 'select other_id FROM mb_client_other where user_id=:uid ';
        $other_id = \Yii::$app->db->createCommand($sql,[':uid'=>$LoginInfo['user_id']])->queryScalar();
        if(!empty($other_id))
        {
            if($openid === $other_id)
            {
                $error = '您已绑定微信支付账号，无需重复绑定';
            }
            else
            {
                $error = '您已经绑到其他微信账号';
            }
            \Yii::getLogger()->log($error.' other_id:'.$other_id.' user_id:'.$LoginInfo['user_id'],Logger::className());
            return false;
        }*/

        $sql = 'select user_id,other_id FROM mb_client_other where other_id = :od ';
        $query = \Yii::$app->db->createCommand($sql,[
            ':od'=>$openid
        ])->queryOne();
        if(!empty($query) && !empty($query['user_id']))
        {
            if($query['user_id'] != $LoginInfo['user_id'])
            {
                $error = '该微信号已经绑定到其他账户';
            }
            else
            {
                $error = '您已绑定微信支付账号，无需重复绑定';
            }

            \Yii::getLogger()->log($error.' other_id:'.$query['other_id'].' user_id:'.$LoginInfo['user_id'],Logger::className());
            return false;
        }

        $model = new ClientOther();
        $model->user_id = $LoginInfo['user_id'];
        $model->other_id = $openid;
        $model->register_type = $registerType;
        $model->create_time = date('Y-m-d H:i:s');

        if(!$model->save())
        {
            $error = '第三方信息保存失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::className());
            return false;
        }

        if(!self::UpdateWeixinBind($model->user_id,$error))
        {
            return false;
        }
        return true;
    }


    /**
     * 绑定支付宝信息记录
     * @param $LoginInfo
     * @param $registerType
     * @param $error
     * @return bool
     */
    public static function GetBindAlipay($LoginInfo,$registerType,&$error)
    {
        $sql = 'SELECT * FROM mb_client bc INNER JOIN mb_alipay_for_cash afc ON bc.client_id = afc.user_id WHERE client_id = :cd AND register_type = :rg AND alipay_no = :al';
        $query = \Yii::$app->db->createCommand($sql,[
            ':cd'=>$LoginInfo['user_id'],
            ':rg'=>$registerType,
            ':al'=>$LoginInfo['alipay'],
        ])->queryOne();

        if(!empty($query))
        {
            $error = '该用户已绑定支付宝账号，无需重复绑定';
            \Yii::getLogger()->log($error.' '.var_export($query,true),Logger::LEVEL_ERROR);
            return false;
        }

        $model = new AlipayForCash();
        $model->user_id = $LoginInfo['user_id'];
        $model->alipay_no = $LoginInfo['alipay'];
        $model->identity_no = $LoginInfo['identity_no'];
        $model->real_name = $LoginInfo['real_name'];
        $model->create_time = date('Y-m-d H:i:s');

        if(!$model->save())
        {
            $error = '支付宝账户信息保存失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        if(!self::UpdateAlipayBind($model->user_id,$error))
        {
            return false;
        }

        return true;
    }

    /**
     * 微信绑定信息更新
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function UpdateWeixinBind($user_id,&$error)
    {
        $sql = 'update mb_client set is_bind_weixin = 2 WHERE client_id = :ud';
        $query = \Yii::$app->db->createCommand($sql,[
            ':ud'=>$user_id
        ])->execute();
        if($query <= 0)
        {
            $error = '更新微信绑定信息失败';
            \Yii::getLogger()->log($error.' '.var_export($query,true),Logger::className());
            return false;
        }

        return true;
    }

    /**
     * 支付宝绑定信息更新
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function UpdateAlipayBind($user_id,&$error)
    {
        $sql = 'update mb_client set is_bind_alipay = 2 WHERE client_id = :ud';
        $query = \Yii::$app->db->createCommand($sql,[
            ':ud'=>$user_id,
        ])->execute();
        if($query <= 0)
        {
            $error = '更新支付宝绑定信息失败';
            \Yii::getLogger()->log($error.' '.var_export($query,true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


    /**
     * 更新充值绑定账号信息
     * @param $client_no
     * @param $openid
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function UpdateBindUserPay($userId,$client_no,$openid,&$error)
    {

        $other = ClientUtil::GetClientPay($openid);

        $Info = self::GetClientNo($client_no);
        if(!isset($Info))
        {
            $error = '没有找到该账号相关信息!';
            return false;
        }
        if($userId === $Info->client_id)
        {
            $error = '该用户已绑定，无需重复绑定!';
            \Yii::getLogger()->log($error.': '.$userId,Logger::LEVEL_ERROR);
            return false;
        }

        if(empty($other))
        {
            $sql = 'insert into mb_client_pay (`user_id`,`other_id`,`register_type`,`create_time`) VALUES (:ud,:od,:tp,:tm)';
            $query = \Yii::$app->db->createCommand($sql,[
                ':ud'=>$Info->client_id,
                ':od'=>$openid,
                ':tp'=>3,
                ':tm'=>date('Y-m-d H:i:s')
            ])->execute();

            if($query <= 0)
            {
                $error = '添加充值绑定账号失败!';
                \Yii::getLogger()->log($error.' :'.var_export($query,true),Logger::LEVEL_ERROR);
                return false;
            }
            \Yii::$app->session['recharge_user_id'] = $Info->client_id;
            return true;
        }


        $sql = 'update mb_client_pay SET user_id = :ud,create_time = :tm WHERE other_id = :od';
        $query = \Yii::$app->db->createCommand($sql,[
            ':ud'=>$Info->client_id,
            ':od'=>$other->other_id,
            ':tm'=>date('Y-m-d H:i:s')
        ])->execute();

        if($query <= 0)
        {
            $error = '更新充值绑定账号失败!';
            \Yii::getLogger()->log($error.' :'.var_export($query,true),Logger::LEVEL_ERROR);
            return false;
        }
        \Yii::$app->session['recharge_user_id'] = $Info->client_id;

        return true;
    }

    /**
     * 根据蜜播ID 获取用户信息
     * @param $client_no
     * @return null|static
     */
    public static function GetClientNo($client_no)
    {
        return Client::findOne(['client_no'=>$client_no]);
    }

    /**
     * 根据用户id 获取绑定支付宝信息
     * @param $user_id
     * @return static[]
     */
    public static function GetAlipayBindInfo($user_id)
    {
        return AlipayForCash::findAll(['user_id'=>$user_id]);
    }

    /**
     * 根据用户id 获取管理员 警告语列表
     * @param $user_id
     * @param bool $refresh
     * @return array|mixed
     */
    public static function GetAdminWarningList($user_id,$refresh = false)
    {
        if($refresh)
        {
            $rst = self::GetAdminWarningByUser($user_id);
            $pStr = serialize($rst);
            \Yii::$app->cache->set('set_admin_warning_'.$user_id,$pStr);
        }
        else
        {
            $cnt = \Yii::$app->cache->get('set_admin_warning_'.$user_id);
            if($cnt === false)
            {
                $lock = new PhpLock('get_admin_warning');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('set_admin_warning_'.$user_id);
                if($cnt === false)
                {
                    $rst = self::GetAdminWarningByUser($user_id);
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('set_admin_warning_'.$user_id,$pStr);
                }
                else
                {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            }
            else
            {
                $rst = unserialize($cnt);
            }
        }
        return $rst;
    }


    /**
     * 获取超管警告语列表
     * @param $user_id
     * @return array
     */
    public static function GetAdminWarningByUser($user_id)
    {
        $query = (new Query())
            ->select(['cid','user_id','content'])
            ->from('mb_common_words')
            ->where('status = 1 and user_id in (1,'. $user_id .')')
            ->all();

        foreach($query as &$rst)
        {
            $rst['type'] = 2;
            if($rst['user_id'] == 1)
            {
                $rst['type'] = 1;
            }
            unset($rst['user_id']);
        }
        return $query;
    }

    /**
     * 保存超管常用语
     * @param $user_id
     * @param $content
     * @param $error
     */
    public static function SetAdminWarning($user_id,$content,&$error)
    {
        $model = new CommonWords();
        $params = [
            'user_id'=>$user_id,
            'content'=>$content,
            'status'=>1,
            'create_at'=>date('Y-m-d H:i:s'),
        ];
        $model->attributes = $params;
        if(!$model->save())
        {
            $error = '保存超管常用语信息失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        \Yii::$app->cache->delete('set_admin_warning_'.$user_id);

        return true;
    }


    /**
     * 删除超管常用语
     * @param $user_id
     * @param $cid
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function DeleteAdminWarning($user_id,$cid,&$error)
    {
        $upsql = 'delete from mb_common_words WHERE user_id = :ud AND cid in ('.$cid.')';
        $rst = \Yii::$app->db->createCommand($upsql,[
            ':ud'=>$user_id,
        ])->execute();

        if($rst <= 0)
        {
            $error = '删除超管常用语失败。';
            \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($upsql,[
                    ':ud'=>$user_id,
                ])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }

        \Yii::$app->cache->delete('set_admin_warning_'.$user_id);

        return true;
    }
} 