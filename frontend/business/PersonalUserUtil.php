<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-14
 * Time: 上午1:17
 */

namespace frontend\business;

use common\components\Des3Crypt;
use common\components\PhpLock;
use common\components\SendShortMessage;
use common\components\SystemParamsUtil;
use common\components\UserNameUtil;
use \common\models\AccountInfo;
use common\components\WaterNumUtil;
use common\models\Balance;
use common\models\Client;
use common\models\ClientActive;
use common\models\Level;
use common\models\UserAccountInfo;
use common\models\Bill;
use common\components\UsualFunForStringHelper;
use yii\db\Query;
use yii\log\Logger;

/**
 * Class 用户个人信息业务类
 * @package frontend\business
 */
class PersonalUserUtil
{

    /**
     * 获取随机头像
     * @return mixed
     */
    public static function GetRandPersonPic()
    {
        $configFile = __DIR__.'/../config/PersonPicConfig.php';
        $wordsAry = require($configFile);
        $index = rand(1,count($wordsAry));
        return $wordsAry[$index -1];
    }
    /**
     * 获取我的信息
     * @param $user_id
     */
    public static function GetSelfInfo($user_id,&$outInfo,&$error)
    {
        $outInfo = [];
        $error = '';
        $client = PersonalUserUtil::GetClientById($user_id);
        if(!isset($client))
        {
            $error = '用户信息找不到';
            return  false;
        }
        $active = PersonalUserUtil::GetActiveByUserId($user_id);
        if(!isset($active))
        {
            $error = '账户活跃信息找不到';
            return false;
        }
        $balance = PersonalUserUtil::GetBalanceById($user_id);
        if(!isset($balance))
        {
            $error = '用户账户信息找不到';
            return false;
        }
        $level = PersonalUserUtil::GetLevelById($active->level_no);
        if(!isset($level))
        {
            $error = '等级信息找不到';
            return false;
        }
        $query = new Query();
        $info = $query
            ->select(['real_ticket_num'])
            ->from('mb_client bc')
            ->leftJoin('mb_time_livingmaster_ticketcount tlt','bc.client_id=tlt.livingmaster_id')
            ->where('hot_type = 1 AND statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\') AND client_id = :id',[':id'=>$user_id])
            ->all();
        foreach($info as $i)
        {
            $today = $i['real_ticket_num'];
        }
        $outInfo['nick_name'] = $client->nick_name;
        $outInfo['pic'] = $client->pic;
        $outInfo['level_id'] = $level->level_id;
        $outInfo['level_pic'] = $level->level_pic;
        $outInfo['sex'] = $client->sex;
        $outInfo['age'] = $client->age;
        $outInfo['city'] = $client->city;
        $outInfo['sign_name'] = $client->sign_name;
        $outInfo['attention_num'] = $active->attention_num;
        $outInfo['funs_num'] = $active->funs_num;
        $outInfo['ticket_count_sum'] = $balance->ticket_count_sum;
        $outInfo['ticket_count'] = $balance->ticket_count;
        $outInfo['ticket_real_sum'] = $balance->ticket_real_sum;
        $outInfo['today_ticket_num'] = $today;
        $outInfo['bean_balance'] = $balance->bean_balance;
        $outInfo['is_bind_weixin'] = $client->is_bind_weixin;
        $outInfo['is_bind_alipay'] = $client->is_bind_alipay;

        return true;
    }
    /**
     * 获取用户支付密码加密串
     * @param $pwd，原始密码
     * @return string
     */
    public static function GetPayPassword($pwd)
    {
        $key = \Yii::$app->params['banlance_pwd_crpty_key'];
        $desCrypt = new Des3Crypt();
        return $desCrypt->des_encrypt($pwd.strval(time()), $key);
    }

    /**
     * 检测密码并返回错误，限制错误次数
     * @param $sourcePwd
     * @param $cryptPwd
     * @param $user_id
     * @param string $error
     */
    public static function CheckPayPasswordByLtErrorCount($sourcePwd,$cryptPwd,$user_id,&$error='')
    {
        $key = 'check_user_pwd_user_id_'.strval($user_id);
         $errorCount = \Yii::$app->cache->get($key);
        if(!empty($errorCount) && $errorCount >= 3)
        {
            $error = '错误次数太多，请20分钟后再来';
            return false;
        }
        $rst = self::CheckPayPassword($sourcePwd, $cryptPwd);
        if(!$rst)
        {
            $errorCount = empty($errorCount)? 0 :intval($errorCount);
            $errorCount ++;
            if($errorCount >= 3)
            {
                $error = '错误次数太多，请20分钟后再来';
            }
            else
            {
                $error = '支付密码错误，剩余尝试次数'.strval(3 - $errorCount);
            }
            \Yii::$app->cache->set($key,strval($errorCount),60*20);
        }
        return $rst;
    }

    /**
     * 检查支付密码首付正确
     * @param $sourcePwd
     * @param $cryptPwd
     * @return bool
     */
    public static function CheckPayPassword($sourcePwd,$cryptPwd)
    {
        $key = \Yii::$app->params['banlance_pwd_crpty_key'];
        $desCrypt = new Des3Crypt();
        $pwd = $cryptPwd;
        $pwd = $desCrypt->des_decrypt($pwd, $key);
        $len = strlen(strval(time()));
        $pwd = substr($pwd,0,strlen($pwd) - $len);
        if($pwd !== $sourcePwd)
        {
            \Yii::getLogger()->log(sprintf('密码验证错误，source[%s]，des[%s]',$pwd,$sourcePwd),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 获取达人版信息，打赏金额、次数最多的人
     * @param $type int 1 打赏金额最多的； 2 次数最多的
     */
    public static function GetLoverList($type, $flag='new', $start_id=0)
    {
        /*
user_id
nick_name
reward_value
         */
        $sqlCondition = '1 =1';
        $sqlConditionParam = [];
        switch($flag)
        {
            case 'up':
                $sqlCondition .= ' and user_active_id > :sid';
                $sqlConditionParam[':sid']=$start_id;
                break;
            case 'down':
                $sqlCondition .= ' and user_active_id < :sid';
                $sqlConditionParam[':sid']=$start_id;
                break;
            default:
                break;
        }
        if($type == '1')
        {
            $typeSql = 'reward_money_sum';
        }
        else
        {
            $typeSql = 'reward_count';
        }

        $sql = 'select * from (
select @rownum:=@rownum+1 AS row_id, user_id,nick_name,pic,'.$typeSql.' as reward_value  from my_account_info ai inner join my_user_active ua on ai.account_id = ua.user_id,(SELECT @rownum:=0) tt
    ) d where '.$sqlCondition.' order by reward_value desc limit 20';
        //$out = [];
        $recordList = \Yii::$app->db->createCommand($sql,$sqlConditionParam)->queryAll();
/*        if(!empty($recordList))
        {
            foreach($recordList as $one)
            {
                $ary=[
                    'row_id'=>$one['rowid'],
                    'user_id'=>$one['user_id'],
                    'nick_name'=>$one['nick_name'],
                    'pic'=>$one['pic'],
                    'reward_value'=> $one[$typeSql],
                ];
                $out[] = $ary;
            }
        }*/
        return $recordList;
        //select @rownum:=@rownum+1 AS rownum, nick_name,reward_money_sum  from my_account_info ai inner join my_user_active ua on ai.account_id = ua.user_id,(SELECT @rownum:=0) my_user_active  order by reward_money_sum desc limit 10'
    }

    /**
     * 根据手机号获取用户账户信息
     * @param $phoneNo
     */
    public static function GetAccountInfoByPhoneNo($phoneNo)
    {
        $user = AccountInfo::find()->where('phone_no=:pno',array(
            ':pno'=>$phoneNo
        ))->one();
        return $user;
    }

    /**
     * 获取默认用户名
     */
    public static function GetDefaultUserName()
    {
        $pre = 'ClientUserName';
        $date = '2015-12-14';
        $num = WaterNumUtil::GenWaterNum($pre,false,false,$date,6);
        return 'user'.$num;
    }


    public static function GetLevelById($level_no)
    {
        $level = Level::findOne(['level_no'=>$level_no]);
        return $level;
    }

    public static function GetBalanceById($user_id)
    {
        $balance = Balance::findOne(['user_id'=>$user_id]);
        return $balance;
    }
    /**
     * 更具用户id获取用户信息
     * @param $user_id
     */
    public static function GetClientById($user_id)
    {
            $ac = Client::findOne([
                'and',['client_id'=>$user_id]
            ]);
        return $ac;
    }

    public static function GetActiveByUserId($user_id)
    {
        $billInfo = ClientActive::findOne(array(
            'user_id'=>$user_id
        ));
        return $billInfo;
    }

    /**
     * 获取账户信息签名 UserAccountInfo
     * @param $userAccountInfoModel
     * @return string
     */
    public static function GetUserAccountInfoSign($userAccountInfoModel)
    {
        $fields = ['balance','out_money_sum','recharge_money_sum'];
        for($i =0; $i < 3; $i ++ )
        {
            $userAccountInfoModel[$fields[$i]] = sprintf('%0.2f',doubleval($userAccountInfoModel[$fields[$i]]));
        }
        $fields = ['account_info_id','user_id','balance','pay_pwd','out_money_sum','recharge_money_sum','rand_str'];
        $dataAry = [];
        $len = count($fields);
        for($i =0; $i <$len; $i ++)
        {
            $dataAry[] = sprintf('%s=%s',$fields[$i], $userAccountInfoModel[$fields[$i]]);
        }
        $dataAry[] = 'dowj0sew0fs=f02hs0e02u0ur20rurue';
        $signStr = implode('&',$dataAry);
        //\Yii::getLogger()->log($signStr, Logger::LEVEL_ERROR);
        return md5($signStr);
    }

    /**
     * 返回半年内逾期次数
     * @param $user_id
     * @return int|string
     */
    public static function GetHalfYearDelayTimes($user_id)
    {
        return Bill::find()->limit(10)->where('user_id=:uid and create_time > date_add(current_timestamp(),interval -6 month) and ((status=0 and back_date<:dt) or (status=1 and back_date < back_time))',[':dt'=>date('Y-m-d'),':uid'=>$user_id])->count('1');
    }

    /**
     * 获取当前是否有逾期未还账单
     * @param $user_id
     * @return bool
     */
    public static function HasDelayUnPaidBill($user_id)
    {
        $rc = Bill::findOne([
            'and','status=0','back_date<date_format(current_timestamp(),\'%Y-%m-%d\')'
        ]);
        return isset($rc);
    }

    /**
     * 获取用户主页信息
     * @param $user_id
     */
    public static function GetPersonalMainPageInfo($user_id)
    {
        $out = [];
        $accountInfo = PersonalUserUtil::GetAccontInfoById($user_id);

        if(!isset($accountInfo))
        {
            return $out;
        }
        $out = [
            'user_id'=>$user_id,
            'phone_no'=>$accountInfo->phone_no,
            'user_name'=>$accountInfo->nick_name,
            'user_sign'=>$accountInfo->sign_name,
            'pic'=>$accountInfo->pic,
            'sex'=>$accountInfo->sex,
            'emotional_state'=>$accountInfo->emotional_state,
            'occupation'=>$accountInfo->occupation,
            'interest'=>$accountInfo->interest
        ];
        $limit = 6;
        $wishListData = WishUtil::GetWishListByUserId($user_id,$limit);
        $outWishListData = WishUtil::GetFormateForWishList($wishListData);
        $out['wish_list'] = $outWishListData;
        $listData = self::GetWishRewardListData($user_id);
        $out['reward_list']=$listData;
        return $out;
    }

    /**
     * 获取主页列表数据
     * @param $user_id
     */
    public static function GetWishRewardListData($user_id)
    {
        $out = [];
        $sql = 'select reward_id as id, mrl.wish_id,mw.pic1 as pic, 2 as data_type, mw.publish_user_name as user_name,mw.wish_name as title,mrl.reward_money as money,mrl.create_time as time
from my_reward_list mrl inner join my_wish mw on mrl.wish_id = mw.wish_id
where reward_user_id=:uid and pay_status=2 order by mrl.reward_id desc limit 10';
        //\Yii::getLogger()->log('sql:'.$sql,Logger::LEVEL_ERROR);
        $rcList = \Yii::$app->db->createCommand($sql,[':uid'=>$user_id])->queryAll();
        foreach($rcList as $rc)
        {
            $ary=[
                'id'=>$rc['id'],
                'wish_id'=>$rc['wish_id'],
                'pic'=>$rc['pic'],
                'user_name'=>$rc['user_name'],
                'title'=>$rc['title'],
                'money'=>$rc['money'],
                'time'=>$rc['time']
            ];
            $out[]=$ary;
        }
        return $out;
    }

    /**
     * 是否第一次打赏
     * @param $user_id
     * @param $setFirstFlag  是否进行设置第一次打赏
     */
    public static function IsFirstTimeReward($user,$setFirstFlag=false)
    {
        if(!isset($user))
        {
            return false;
        }
        $rewardInfo = RewardUtil::GetFirstRewardInfoByUserId($user->account_id);
        return $rewardInfo === null;
        /*$key = sha1('userkey:'.strval($user->account_id));
        $cont = strval($user->account_id).' '.$user->nick_name.' '.$user->phone_no;
        $path = \Yii::$app->getBasePath().'/web/reward_data';
        if(!file_exists($path))
        {
            mkdir($path);
            chmod($path, 777);
        }
        $fileName = $path.'/'.$key.'.user';
        $lock = new PhpLock('reward_user_lock'.strval($user->account_id));
        $lock->lock();
        if(!file_exists($fileName))
        {
            if($setFirstFlag === true)
            {
                $rst = file_put_contents($fileName, $cont);
                if($rst <= 0)
                {
                    \Yii::getLogger()->log('文件无法保存，file:'.$fileName.' '.$cont, Logger::LEVEL_ERROR);
                }
            }
            $lock->unlock();
            return true;
        }
        $lock->unlock();
        return false;*/
    }

    /**
     * 删除第一次领红包信息，用户第一次打赏领红包失败时
     * @param $user
     */
    public static function DelFirstReward($user)
    {
        if(!isset($user))
        {
            return;
        }
        $key = sha1('userkey:'.strval($user->account_id));
        $path = \Yii::$app->getBasePath().'/web/reward_data';
        $fileName = $path.'/'.$key.'.user';
        if(file_exists($fileName))
        {
            unlink($fileName);
        }
    }

    /**
     * 用户注册
     * @param $phoneNo
     * @param $deviceNo
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function RegisterUser($phoneNo,$deviceNo, &$error,$isInner = false)
    {
        $phpLock = new PhpLock('register_user'.$phoneNo);
        $phpLock->lock();
        $ac = PersonalUserUtil::GetAccountInfoByPhoneNo($phoneNo);
        if(isset($ac))
        {
            //$rstData['errno'] = '1';
            $error = '用户已经注册';
            $phpLock->unlock();
            return false;
        }
        if(!self::CouldRegisterByDeviceNo($deviceNo,$error))
        {
            $phpLock->unlock();
            return false;
        }
        $ac = new AccountInfo();
        $ac->nick_name = UserNameUtil::GetClientUserName();// PersonalUserUtil::GetDefaultUserName();
        $ac->device_no = $deviceNo;
        $ac->phone_no = $phoneNo;
        $ac->centification_level = 0;
        $ac->sign_name = '没有个性，暂不签名!';
        $ac->status = 1;
        $ac->sex = '未设置';
        $ac->create_time = date('Y-m-d H:i:s');
        $ac->email = '';
        $ac->pic = self::GetRandPersonPic();
        $ac->occupation = '';
        $ac->interest = '';
        $ac->emotional_state = '';
        $ac->school_name = '';
        $ac->school_area = '';
        $ac->hometown = '';
        $ac->user_type = 0;
        $ac->is_inner = ((!$isInner)?1:2);


        //创建账户信息
        $billInfo = new UserAccountInfo();
        $billInfo->balance = '0.00';//banlance_pwd_crpty_key
        $pwd = UsualFunForStringHelper::mt_rand_str(6,'0123456789');
        //$pwd = '123456';
        $billInfo->SetPassword($pwd);
        $billInfo->out_money_sum = '0.00';
        $billInfo->recharge_money_sum = '0.00';
        $billInfo->rand_str = UsualFunForStringHelper::mt_rand_str(32);

        //活跃度
        $userActive = UserActiveUtil::GetUserActiveNewModel(null);
        //美愿基金
        $fun = FundUtil::GetFundNewModel($ac);

        //跟人动态信息
        $pNewInfo = PersonalNewStatisticUtil::GetNewModel(0);
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if (!$ac->save())
            {
                $error = '系统错误，保存注册信息异常';
                $msg = var_export($ac->getErrors(), true);
                \Yii::getLogger()->log('保存注册信息错误：' . $msg, Logger::LEVEL_ERROR);
                throw new Exception('保存注册信息失败');
                // return false;
            }
            \Yii::getLogger()->log('user_id:'.$ac->account_id.'  pwd:'.$pwd,Logger::LEVEL_ERROR);
            $billInfo->user_id = $ac->account_id;
            if (!$billInfo->save())
            {
                $error = '系统错误，保存账户信息异常';
                $msg = var_export($ac->getErrors(), true);
                \Yii::getLogger()->log('保存账户信息异常：' . $msg, Logger::LEVEL_ERROR);
                throw new Exception('保存账户信息失败');
                //return false;
            }
            $userActive->user_id = $ac->account_id;
            if(!$userActive->save())
            {
                \Yii::getLogger()->log(var_export($userActive->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('保存用户活跃度信息失败');
            }
            $fun->user_id = $ac->account_id;
            if(!$fun->save())
            {
                \Yii::getLogger()->log(var_export($fun->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('保存美愿基金信息失败');
            }
            $pNewInfo->user_id = $ac->account_id;
            if(!$pNewInfo->save())
            {
                \Yii::getLogger()->log(var_export($pNewInfo->getErrors(), true),Logger::LEVEL_ERROR);
                throw new Exception('保存个人动态信息失败');
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $trans->rollBack();
            $phpLock->unlock();
            return false;
        }
        $phpLock->unlock();

        /*$pars = array(
            'param1'=>$pwd,
            'param2'=>SystemParamsUtil::GetSystemParam('system_customer_call',true)
        );
        //去除短信，让用户自己去修改密码
        //发送短信密码通知
        if(!SendShortMessage::SendMessageShiYuanKeji($phoneNo,'99',$pars,$error))
        {
            \Yii::getLogger()->log($error, Logger::LEVEL_ERROR);
        }*/
        return true;
    }

    /**
     * 判断设备号是否可以余额打赏，设备号超过3个禁止用余额打赏
     * @param $device_no
     * @param $error
     */
    public static function CouldRewardByBalance($device_no,&$error)
    {
        if(empty($device_no))
        {
            $error = '设备号不能为空';
            return false;
        }
        $num = self::GetDeviceRegisterNum($device_no);
        if($num === false)
        {
            $error='设备号不存在';
            return false;
        }
        $num = intval($num);
        if($num > 3)
        {
            $error = '您涉嫌恶意注册与打赏，帐号已限制，等待相关部门核实！';
            return false;
        }
        return true;
    }

    /**
     * 是否允许发短信
     * @param $phone_no
     * @param $device_no
     * @param $error
     * @return bool
     */
    public static function CouldReciveShortmsg($phone_no,$device_no,&$error)
    {
        $user = self::GetAccountInfoByPhoneNo($phone_no);
        if(isset($user))
        {
            if(empty($device_no))
            {
                $error = '设备号不能为空';
                return false;
            }
            if($user->status === 0)
            {
                $error = '该用户涉及不合法操作，已被管理员禁用';
                return false;
            }
            //用注册用户设备号查询，多个的为异常用户
            $num = self::GetDeviceRegisterNum($user->device_no);
            if($num === false)
            {
                return true;//没有的还可以注册，同一个设备号查过三个不能注册
            }
            $num = intval($num);
            if($num > 3)
            {
                $error = '异常账号，冻结审核中';
                return false;
            }
        }
        else
        {
            /*if(!self::CouldRegisterByDeviceNo($device_no,$error))
            {
                $error='该设备关联太多账号，冻结审核中';
                return false;
            }*/
        }

        return true;
    }

    /**
     * 设备号是否可以注册
     * @param $device_no
     * @param $error
     * @return bool
     */
    public static function CouldRegisterByDeviceNo($device_no,&$error)
    {
        if(empty($device_no))
        {
            $error = '设备号不能为空';
            return false;
        }
        $num = self::GetDeviceRegisterNum($device_no);
        if($num === false)
        {
            return true;//没有的还可以注册，同一个设备号查过三个不能注册
        }
        $num = intval($num);
        if($num > 2)
        {
            $error = '同一设备最多注册三个账号';
            return false;
        }
        return true;
    }

    /**
     * 获取同一个设备号注册的数量
     * @param $device_no
     * @return bool|string
     */
    public static function GetDeviceRegisterNum($device_no)
    {
        return AccountInfo::find()->select(['count(*) as num'])->addGroupBy('device_no')->limit(1)->where(['device_no'=>$device_no])->scalar();
    }
} 