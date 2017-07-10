<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/21
 * Time: 14:27
 */

namespace frontend\business;


use common\components\OssUtil;
use common\components\PhpLock;
use common\components\PicHelper;
use common\components\QiNiuUtil;
use common\components\SystemParamsUtil;
use common\components\WaterNumUtil;
use common\models\Approve;
use common\models\Client;
use common\models\ClientActive;
use common\models\ClientOther;
use common\models\ClientPay;
use common\models\ClientQiniu;
use common\models\ClientSalary;
use common\models\CloseIdLog;
use frontend\business\RongCloud\UserUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateExperienceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\QiNiuRegisterSaveForReward;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RegisterSaveForReward;
use yii\db\Query;
use yii\log\Logger;
use common\components\tenxunlivingsdk\TimRestApi;

class ClientUtil
{
    /**
     * 获取需要生成图片的用户
     */
    public static function GetShouldGenPicClients($limit=100)
    {
        $sql = 'select client_id,pic from mb_client where is_pic_deal is null order by client_id ASC limit '.strval($limit);
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }
    /**
     * @param $client_id
     * @param $main_pic
     * @param $icon_pic
     * @param $middle_pic
     */
    public static function UpdateClientPicInfo($client_id,$main_pic,$icon_pic,$middle_pic)
    {
        if(empty($main_pic) ||empty($icon_pic) || empty($middle_pic))
        {
            $sql_null = 'update mb_client set pic=\'\', is_pic_deal=1 where client_id=:cid';
            \Yii::$app->db->createCommand($sql_null,[':cid'=>$client_id])->execute();
            return;
        }
        $sql = 'update mb_client set main_pic=:mpic, icon_pic=:ipic,middle_pic=:mipic,is_pic_deal=1 where client_id=:cid';
        \Yii::$app->db->createCommand($sql,[':mpic'=>$main_pic,':ipic'=>$icon_pic,':mipic'=>$middle_pic,':cid'=>$client_id])->execute();
    }
    /**
     * 生成用户图片缩略图
     * @param $client_id
     * @param $pic_url
     * @param $error
     */
    public static function GenClientPicThumb($client_id,$pic_url,&$error)
    {
        if(!self::GetClientIconThumbFromUrl($pic_url,$thumb_files,$error))
        {
            return false;
        }
        $main_pic = (!empty($thumb_files)?$thumb_files[0]:'');
        $middle_pic = (!empty($thumb_files)?$thumb_files[1]:'');
        $icon_pic = (!empty($thumb_files)?$thumb_files[2]:'');
        self::UpdateClientPicInfo($client_id,$main_pic,$icon_pic,$middle_pic);
        return true;
    }



    /**
     * 获取图像缩略图
     * @param $url
     * @param $thumb_files
     * @param $error
     */
    public static function GetClientIconThumbFromUrl($url,&$thumb_files,&$error)
    {
        //保存网络图片写到本地
        if(!PicHelper::SavePicFromWeb($url,$picFile,$error))
        {
            //更新图片字段为空，要求从新更新图片
            \Yii::getLogger()->log($error.' url:'.$picFile,Logger::LEVEL_ERROR);
            $thumb_files[]='';
            $thumb_files[]='';
            $thumb_files[]='';
            return false;
        }

        if(!self::IsShouldDealPic($picFile))
        {
            $thumb_files[]=$url;
            $thumb_files[]=$url;
            $thumb_files[]=$url;
            unlink($picFile);
            return true;
        }
        if(!PicHelper::img2ThumbMuilt($picFile,$tmp_files,[640,240,60],[640,240,60],[],[],$error))
        {
            unlink($picFile);
            return false;
        }
        unlink($picFile);
        $thumb_files =[];
        //上传图片
        foreach($tmp_files as $file)
        {
            $fName = uniqid('ocimg_');
            $suffix = PicHelper::FileExt($file);
            $picUrl = '';
            $error = '';
            if(!OssUtil::UploadFile($fName,$suffix,'client-pic',$file,$picUrl,$error))
            {
                return false;
            }
            //删除文件
            unlink($file);
            $thumb_files[] = $picUrl;
        }
        return true;
    }

    /**
     * 客户图片是否需要处理，大小超过640px的需要处理，小于30的图片不做处理
     * @param $file
     */
    public static function IsShouldDealPic($file)
    {
        if(!file_exists($file))
        {
            return false;
        }
        $info = getimagesize($file);
        if($info[0] > 640 || $info[1] > 640)
        {
            return true;
        }
        $len = filesize($file);
        if($len < (30 * 1024))
        {
            return false;
        }
        return true;
    }

    /**
     * 搜索用户
     * @param $key_word
     * @param $page_no
     * @param $page_size
     * @param $user_id
     */
    public static function SearchUser($key_word,$page_no,$page_size,$user_id)
    {
        if(empty($page_no))
        {
            $page_no = 1;
        }
        if(empty($page_size))
        {
            $page_size = 10;
        }
        $offset = $page_size * ($page_no -1);
        $query = new Query();
        $query->select(['client_id as user_id','ct.nick_name','ifnull(ct.icon_pic,ct.pic) as pic','sex','sign_name','ifnull(c.friend_user_id,0) as is_attention'])
        ->from('mb_client ct')
            ->leftJoin('mb_attention c','c.friend_user_id = ct.client_id and user_id=:uid',[':uid'=>strval(intval($user_id))])
            ->where(['or',['like','client_no',$key_word],['like','ct.nick_name',$key_word]])
            ->offset($offset)
            ->orderBy('ct.client_id asc')
            ->limit($page_size);

        return $query->all();
    }

    /**
     * 搜索粉丝群
     * @param $key_word
     * @param $page_no
     * @param $page_size
     * @param $user_id
     */
    public static function FansGroupSearch($key_word,$page_no,$page_size,$user_id)
    {
        if(empty($page_no))
        {
            $page_no = 1;
        }
        if(empty($page_size))
        {
            $page_size = 10;
        }

        $query = (new Query())
            ->select(['client_id as user_id','fg.pic','bc.nick_name','sign_name','fg.group_id','fg.tx_group_id','IFNULL(fgm.user_id,\'0\') as is_join'])
            ->from('mb_client bc')
            ->innerJoin('mb_fans_group fg','bc.client_id = fg.group_master_id')
            ->leftJoin('mb_fans_group_member fgm','fg.group_id = fgm.group_id and fgm.user_id=:ud',[':ud'=>strval(intval($user_id))])
            ->where(['and','bc.client_id >0',['or',['like','client_no',$key_word],['like','bc.nick_name',$key_word]]])
            ->offset(($page_no - 1) * $page_size)
            ->limit($page_size)
            ->all();

        return $query;
    }


    /**
     * 搜索直播间
     * @param $room_no
     */
    public static function LivingRoomSearch($room_no,$user_id)
    {
        $query = (new Query())
            ->select(['client_id as user_id','client_no','nick_name','IFNULL(NULLIF(c.main_pic,\'\'),c.pic) as pic','l.city',
                'l.living_id','other_id as group_id','lpn.person_count as living_num','l.living_title',
                'l.device_type','c.sex','is_contract','l.living_type','game_name','pull_rtmp_url','l.living_pic_url',
                'IFNULL(l.room_no,0)as room_no','IFNULL(guess_num,\'-1\') as guess_num',
                'ifnull(free_num,-1) as free_num','ifnull(lpt.tickets,0) as tickets_num',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views',
                'if(l.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as ticket_status','if(ifnull(lptv.views_id,0)=0,0,1) as ticket_views'])
            ->from('mb_client c')
            ->innerJoin('mb_living l','c.client_id = l.living_master_id and l.room_no = :rm',[':rm'=>$room_no])
            ->innerJoin('mb_living_personnum lpn','lpn.living_id = l.living_id')
            ->innerJoin('mb_chat_room cr','cr.living_id = l.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=l.living_id and lp.living_before_id=l.living_before_id and lp.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=l.living_id and lpt.living_before_id=l.living_before_id and lpt.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=l.living_id and gr.room_no=l.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->where('l.status = 2 and l.living_type = 3 or l.living_type = 4')
            ->one();


        return $query;
    }


    /**
     * 搜索直播间
     * @param $appID 应用程序id
     * @param $room_no 房间号
     * @param $user_id 用户id
     * @return array|bool
     */
    public static function LivingRoomSearchByAppId($appID,$room_no,$user_id)
    {
        $query = (new Query())
            ->select(['client_id as user_id','client_no','nick_name','IFNULL(NULLIF(c.main_pic,\'\'),c.pic) as pic','l.city',
                'l.living_id','other_id as group_id','lpn.person_count as living_num','l.living_title',
                'l.device_type','c.sex','is_contract','l.living_type','game_name','pull_rtmp_url','l.living_pic_url',
                'IFNULL(l.room_no,0)as room_no','IFNULL(guess_num,\'-1\') as guess_num',
                'ifnull(free_num,-1) as free_num','ifnull(lpt.tickets,0) as tickets_num',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views',
                'if(l.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as ticket_status','if(ifnull(lptv.views_id,0)=0,0,1) as ticket_views'])
            ->from('mb_client c')
            ->innerJoin('mb_living l','c.client_id = l.living_master_id and l.room_no = :rm',[':rm'=>$room_no])
            ->innerJoin('mb_living_personnum lpn','lpn.living_id = l.living_id')
            ->innerJoin('mb_chat_room cr','cr.living_id = l.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=l.living_id and lp.living_before_id=l.living_before_id and lp.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=l.living_id and lpt.living_before_id=l.living_before_id and lpt.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=l.living_id and gr.room_no=l.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->where('l.status = 2 and l.living_type = 3 or l.living_type = 4')
           
            ->one();


        return $query;
    }

    /**
     * 获取经验
     * @param $sentData
     * @param $error
     */
    public static function BeanTalkdExperience($jobId,$sentData,&$error)
    {
        if(!($sentData instanceof \stdClass))
        {
            $error = '不是json对象';
            return false;
        }
        $device_type = $sentData->device_type;
        $living_id = $sentData->living_id;
        $living_no = $sentData->living_no;
        $user_id = $sentData->user_id;
        $op_type = $sentData->op_type;
        $unique_no = $jobId;
        $source_type = 1;//1 送礼物 2 观看直播 3 直播
        $trans = [];
        $params = [
            'device_type'=>$device_type,
            'living_id'=>$living_id,
            'living_before_id'=>$living_no
        ];
        /*
living_visitor_min_experience
living_master_min_experience
living_bean_to_experience
         */
        if($op_type == '1')//送礼物
        {
            $relate_id = $sentData->relate_id;
            if(empty($relate_id))
            {
                $error = '送礼物记录id不能为空';
                return false;
            }
            $params['relate_id']=$relate_id;
            $bean_num = $sentData->bean_num;
            if(intval($bean_num) <= 0)
            {
                $error = '豆数必须大于0';
                return false;
            }
            $params['gift_value']=$bean_num;
            $rate = SystemParamsUtil::GetSystemParam('living_bean_to_experience',false,'value1');
            $rate = intval($rate);
            if($rate <= 0)
            {
                $error = '豆转经验转化率异常';
                return false;
            }
            $experience_num = $bean_num * $rate;
            $source_type = 1;
        }
        else //直播
        {
            $role = $sentData->role;//身份，区分主播和其他成员，他们经验转化率不同
            $hear_count = $sentData->heart_count;
            $heart_dis_time = SystemParamsUtil::GetSystemParam('heart_dis_time',true,'value1');
            $heart_dis_time = intval($heart_dis_time);
/*            $starttime = $sentData->starttime;
            $endtime = $sentData->endtime;*/

            if($role == '1')//主播
            {
                $rate = SystemParamsUtil::GetSystemParam('living_master_min_experience',false,'value1');
                $rate = intval($rate);
                if($rate <= 0)
                {
                    $error = '主播每分钟经验经验转化率异常';
                    return false;
                }
                $source_type = 3;
                $min = intval(($hear_count * $heart_dis_time)/60);
            }
            else //观看人员
            {
                $rate = SystemParamsUtil::GetSystemParam('living_visitor_min_experience',false,'value1');
                $rate = intval($rate);
                if($rate <= 0)
                {
                    $error = '观众每分钟经验经验转化率异常';
                    return false;
                }
                $source_type = 2;
                $min = $hear_count;
            }
            $experience_num = $min *$rate;
            $params['starttime'] = $hear_count;
            $params['endtime'] = $heart_dis_time;
            $params['owner'] = $role;
        }
        $clientActive = ClientActiveUtil::GetClientActiveInfoByUserId($user_id);
        if(!isset($clientActive))
        {
            $error = '用户活跃记录不存在，账户数据异常';
            return false;
        }
        $params['source_type'] = $source_type;
        $params['change_rate'] = $rate;
        $params['experience'] = $experience_num;
        $params['create_time']=date('Y-m-d H:i:s');
        $params['user_id'] = $user_id;


        $userActive = ClientActiveUtil::GetClientActiveInfoByUserId($user_id);
        if(!isset($userActive))
        {
            $error = '用户活跃记录不存在';
            return false;
        }
        $trans[] = new ExperienceModifyByTrans($userActive,['experience_num'=>$experience_num]);
        $trans[] = new CreateExperienceLogByTrans($userActive,$params);
        if(!SaveByTransUtil::RewardSaveByTransaction($trans,$error))
        {
            return false;
        }
        ClientActiveUtil::UpdateClientLevel($userActive);

        return true;
    }




    /**
     * 用户注册，七牛
     * @param $uniqueNo
     * @param $deviceNo
     * @param $data
     * @param $error
     * @param bool $isInner
     * @return bool
     */
    public static function RegisterUserQiNiu($uniqueNo,$deviceNo, $data,$getui_id, &$error,$isInner = false,$is_v3 = false)
    {
        $phpLock = new PhpLock('register_user_'.$uniqueNo);
        $phpLock->lock();
        //获取七牛信息
        $is_test = \Yii::$app->params['is_test'];
        $title = ($is_test === '1'?'livingtest'.$uniqueNo: 'livingmibo'.$uniqueNo);//七牛直播流昵称，唯一
        $qnInfo = QiNiuUtil::CreateStream($title,$error);
        if($qnInfo === false) {
            $error = '获取七牛云直播信息异常';
            \Yii::error($error.'： title:'.$title,Logger::LEVEL_ERROR);
            $phpLock->unlock();
            return false;
        }
        $ac = self::GetUserByUniqueId($uniqueNo);
        if(isset($ac)) {
            $error = '用户已经注册';
            $phpLock->unlock();
            return false;
        }
        if(!self::CouldRegisterByDeviceNo($deviceNo,$error)) {
            $phpLock->unlock();
            return false;
        }
        $other = ClientUtil::GetClientOtherInfo($uniqueNo);
        $is_bind_wx = 1;
        if($data['register_type'] == 2)
        {
            $is_bind_wx = 2;
            if(isset($other)) {
                $is_bind_wx = 1;
            }
        }
        /*if($data['register_type'] == '3'){
            $error = '微博注册完善中，暂停注册';
            return false;
        }*/

        $nick_name = ((!empty($data['nick_name'])) ? $data['nick_name'] : '');
        $sex = ((!empty($data['sex'])) ? $data['sex'] : '未设置');
        $pic = ((!empty($data['pic'])) ? $data['pic'] : '');
        $deviceType = ((!empty($data['device_type'])) ? $data['device_type']: '');
        $phoneNo = (($data['register_type'] == 1 )? $uniqueNo : '');

        $client_no =WaterNumUtil::GetUniqueIdFromTable($error);
        if($client_no === false)
        {
            $error = '系统繁忙蜜播号生成失败2';
            $phpLock->unlock();
            return false;
        }
        $model = new Client();
        $model->unique_no = $uniqueNo;
        $model->client_no = $client_no;// WaterNumUtil::GetRandUniqueNo();// WaterNumUtil::GenWaterNum('NO.',false,false,'2016-04-25',8);
        $model->register_type = $data['register_type'];
        $model->city = '';
        $model->nick_name = $nick_name.'-'.$client_no;
        $model->pic = $pic;
        $model->main_pic = '';
        $model->icon_pic = '';
        $model->is_pic_deal = 0;
        $model->age = '';
        $model->sign_name = '没有个性，暂不签名!';
        $model->phone_no = $phoneNo;
        $model->device_no = $deviceNo;
        $model->device_type = $deviceType;
        $model->sex = $sex;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->is_inner = ((!$isInner)?1:2);
        $model->is_bind_weixin = $is_bind_wx;
        $model->is_bind_alipay = 1;
        $model->is_contract = 1;
        $model->is_centification = ($data['register_type'] == 1? 2:1);
        $model->client_type = (!isset($data['client_type']) ? 1 : $data['client_type']);
        $model->getui_id = $getui_id;

        $transActions[] = new QiNiuRegisterSaveForReward($model,['qiniu_info'=>json_encode($qnInfo)]);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo,$error)) {
            $phpLock->unlock(); 
            return false;
        }
        $phpLock->unlock();

        if($outInfo->register_type == 1)
        {
            //如果用户是用手机注册的，就在认证表里插入手机号码
            $sql = 'INSERT IGNORE INTO mb_approve(phone_num, client_no, client_id,create_time) VALUES (:cd,:cn,:uid,:ct)';

            \Yii::$app->db->createCommand($sql,[
                ':cd' => $phoneNo,
                ':cn' => $client_no,
                ':uid'=> $outInfo->client_id,
                ':ct' => date('Y-m-d H:i:s'),
            ])->execute();

            //插入认证表后向审核表里插入数据
            $results = Approve::findOne(['client_no' => $client_no]);

            $check_sql = 'insert into mb_business_check(relate_id,business_type,status,check_result_status,create_time,check_time,check_user_id,check_user_name,create_user_id,create_user_name,check_no,refused_reason)
values(:rid,4,0,0,:ctime,0,0,"",:uid,:cname,:cno,"")';
            $check_result = \Yii::$app->db->createCommand($check_sql,[
                ':rid' => $results['approve_id'],
                ':ctime' => date('Y-m-d H:i:s'),
                ':uid' => $outInfo->client_id,
                ':cname' => $outInfo->nick_name,
                ':cno' => $results['approve_id']%20
            ])->execute();

            if($check_result <= 0){
                \Yii::getLogger()->log('check_sql='.$check_sql,Logger::LEVEL_ERROR);
                $error = '直播认证失败2';
                return false;
            }
        }


        /***注册完成人物统计处理队列***/
        $ticket_data = [
            'user_id' => $outInfo->client_id,
            'day' => date('Y-m-d'),
        ];
        if(!JobUtil::AddCustomJob('AddRegNumBeanstalk','add_reg_num',$ticket_data,$error)){
            \Yii::error('AddregNumB :'.$error);
        }

        //设置七牛云直播信息到缓存
        $key = 'qiniu_living_'.strval($model->client_id);
        if(!\Yii::$app->cache->set($key,json_encode($qnInfo))) {
            \Yii::getLogger()->log('设置七牛信息到缓存失败',Logger::LEVEL_ERROR);
        }
        if(!empty($pic))
        {
            $client_id = $model->client_id;
            if(!JobUtil::AddPicJob('deal_client_pic',['client_id'=>$client_id,'pic'=>$pic],$error))
            {
                \Yii::getLogger()->log($error.' pic job save error',Logger::LEVEL_ERROR);
                return false;
            }
        }
        $data = [
            'key_word'=>'set_tencent_im',
            'user_id'=>$outInfo->client_id,
            'nick_name'=>$outInfo->nick_name,
            'pic'=>$outInfo->pic,
        ];

        //注册腾讯用户
        if(!JobUtil::AddImJob('tencent_im',$data,$error))
        {
            \Yii::getLogger()->log('im job save error :'.$error,Logger::LEVEL_ERROR);
        }
        return true;


    }

    /**
     * 生成并保存七牛信息
     * @param $unique_no
     * @param $client_id
     * @param $qiniu_info
     * @param $error
     * @return bool
     */
    public static function GenQiNiuInfoForClient($unique_no,$client_id,&$qiniu_info,&$error)
    {
        //获取七牛信息
        $is_test = \Yii::$app->params['is_test'];
        $title = ($is_test === '1'?'livingtest'.$unique_no: 'livingmibo'.$unique_no);//七牛直播流昵称，唯一
        $qnInfo = QiNiuUtil::CreateStream($title,$error);
        if($qnInfo === false)
        {
            \Yii::getLogger()->log('unique_no:'.$unique_no.' client_id:'.$client_id,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('获取七牛云直播信息异常：'.$error,Logger::LEVEL_ERROR);
            $error = '获取七牛云直播信息异常';
            return false;
        }
        $clientQiniu = ClientUtil::GetQiNiuInfoByClientId($client_id);
        if(!isset($clientQiniu))
        {
            $clientQiniu = new ClientQiniu();
            $clientQiniu->user_id = $client_id;
        }
        $qiniu_info = json_encode($qnInfo);
        $clientQiniu->qiniu_info = $qiniu_info;
        if(!$clientQiniu->save())
        {
            $error ='保存七牛直播信息异常';
            \Yii::getLogger()->log($error.':'.var_export($clientQiniu->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        //设置七牛云直播信息到缓存
        $key = 'qiniu_living_'.strval($client_id);
        if(!\Yii::$app->cache->set($key,$qiniu_info))
        {
            \Yii::getLogger()->log('设置七牛信息到缓存失败',Logger::LEVEL_ERROR);
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
            return true;//没有的还可以注册，同一个设备号查过4个不能注册
        }
        $maxNum = SystemParamsUtil::GetSystemParam('system_device_register_no',true);
        $num = intval($num);
        if($num > $maxNum)
        {
            $error = '同一设备最多注册'.$maxNum.'个账号';
            \Yii::getLogger()->log($error.'  max_num:'.$maxNum.'  num:'.$num.'  device_no:'.$device_no,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 是否允许发送短信
     * @param $phone_no
     * @param $device_no
     * @param $error
     * @return bool
     */
    public static function CouldReciveShortmsg($phone_no,$device_no,&$error)
    {
        $user = self::GetClientByPhoneNo($phone_no);
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
                return true;//没有的还可以注册，同一个设备号查过 系统配置个数 不能注册
            }
            $num = intval($num);
            $maxNum = SystemParamsUtil::GetSystemParam('system_device_register_no',true);
            if($num > $maxNum)
            {
                $error = '异常账号，冻结审核中';
                \Yii::getLogger()->log($error.'  device_no:'.$device_no,Logger::LEVEL_ERROR);
                return false;
            }
        }
        else
        {
            if(!self::CouldRegisterByDeviceNo($device_no,$error))
            {
                $error='该设备关联太多账号，冻结审核中';
                \Yii::getLogger()->log($error.'  device_no:'.$device_no,Logger::LEVEL_ERROR);
                return false;
            }
        }
        return true;
    }

    /**
     * 根据唯一号获取用户基本信息
     * @param $unique_no
     * @return Client|null
     */
    public static function GetClientByUniqueNo($unique_no)
    {
        return Client::findOne(['unique_no'=>$unique_no]);
    }


    /**
     * 获取同一个设备号注册的数量
     * @param $device_no
     * @return bool|string
     */
    public static function GetDeviceRegisterNum($device_no)
    {
        return Client::find()->select(['count(*) as num'])->addGroupBy('device_no')->limit(1)->where(['device_no'=>$device_no])->scalar();
    }

    /**
     * 根据设备号,登录类型获取用户数据
     * @param $uniqueNo
     * @param $registerType
     * @return null|static
     */
    public static function GetClientByRegister($uniqueNo,$registerType)
    {
        $user = Client::findOne(['unique_no'=>$uniqueNo,'register_type'=>$registerType]);
        return $user;
    }

    /**
     * 完善用户信息保存
     * @param $userInfo
     * @param $uniqueNo
     * @param $registerType
     * @param $error
     * @return bool
     */
    public static function  UpdateUser($userInfo,$uniqueNo,$registerType,&$error)
    {
        $user = ClientUtil::GetClientByUniqueNo($uniqueNo);
        if(!self::CheckNameIsNull($userInfo['nick_name'], $user->client_id, $error))
        {
            return false;
        }
        //\Yii::getLogger()->log('user用户 : '.var_export($user,true),Logger::LEVEL_ERROR);
        if(!isset($user))
        {
            $error = '用户信息信息不存在';
            \Yii::getLogger()->log('unique:'.$uniqueNo.'   register:'.$registerType,Logger::LEVEL_ERROR);
            return false;
        }

        $picUpdate = false;
        if(!empty($userInfo['pic']) && $user->pic != $userInfo['pic'])
        {
            $picUpdate =true;
        }
        $user->attributes = $userInfo;
        if(!$user->save())
        {
            $error = '用户信息更新失败';
            \Yii::getLogger()->log($error.' :'.var_export($user->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $userInfo['userId'] = $user['client_id'];
        /*if(!UserUtil::refreshUserInfo($userInfo,$error)) {
            return false;
        }*/

        $data = [
            'client_id'=>$user->client_id,
            'pic'=>$user->pic
        ];

        if($picUpdate === true)
        {
            $key_word = 'deal_client_pic';
            if(!JobUtil::AddPicJob($key_word,$data,$error))
            {
                \Yii::getLogger()->log('pic job save error: '.$error,Logger::LEVEL_ERROR);
            }
        }

        return true;
    }


    /**
     * 得到前端签名sign
     *  @param array $params
     * @return string
     */
    public static function GetClientSign($params){
        ksort($params);
        $token = \Yii::$app->params['living_key'];
        $str = '';
        foreach($params as $key=>$v){
            $key = strtolower($key);
            $str .= $key.'='.$v.'&';
        }
        $str .= 'key='.$token;
        return sha1($str);
    }


    /**
     * 禁用用户或者解封
     * @param $client
     * @return bool
     */
    public static function SetBanUser($client,$seal_reason='',&$error)
    {
        if($client->status == 0)
        {
            if($client->client_type == 2)
            {
                $error = '超级管理员不能被禁用';
                return false;
            }
        }
        if(!self::SaveClient($client,$error))
        {
            return false;
        }
        $user_info = ClientUtil::GetClientById($client->client_id);

        $params = [
            'client_no' => $client->client_no,
            'nick_name' => $client->nick_name,
            'manage_id' => \Yii::$app->user->id,
            'manage_name' => \Yii::$app->user->identity->username,
            'operate_type' => 2,
            'management_type' => 1,
            'create_time' => date('Y-m-d H:i:s')
        ];

        if($user_info->status == 0)
        {

            \Yii::getLogger()->log('params_rst:'.var_export($params,true),Logger::LEVEL_ERROR);
            $params['remark1'] = $seal_reason;
            //增加封播日志信息
            if (!self::CreateCloseUserByLog($params, $error)) {
                return false;
            }

            //IM禁止发言
            if(!TimRestApi::setnospeaking((string)$client->client_id)) {
                $error = 'IM禁止发言操作失败';
                return false;
            }
        }
        else
        {
            $params['management_type'] = 2;
            $params['remark1'] = $seal_reason;

            //增加封播日志信息
            if (!self::CreateCloseUserByLog($params, $error)) {
                return false;
            }

            //IM取消禁止发言
            if(!TimRestApi::setnospeaking((string)$client->client_id, 0, 0)) {
                $error = 'IM取消禁止发言操作失败';
                return false;
            }
        }
        if($client->status == 0)
        {
            $living_info = LivingUtil::GetClientLivingInfoByLivingMasterId($client->client_id);
            $finishInfo = null;
            if(!LivingUtil::SetBanClientFinishLiving($living_info['living_id'],$finishInfo,$living_info['living_master_id'],$living_info['other_id'],$outInfo,$error))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 检测用户名是否被占用
     * @param $name
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function CheckNameIsNull($name,$user_id,&$error)
    {
        $sql = 'select nick_name from mb_client where nick_name = :name AND client_id NOT IN ('.$user_id.') limit 2';
        $rst = \Yii::$app->db->createCommand($sql,[':name'=>$name])->queryAll();
        if(!empty($rst))
        {
            foreach($rst as $v)
            {
                if(($v['client_id'] !== $user_id) && ($v['nick_name'] === $name))
                {
                    $error = '该用户名已经被使用';
                    return false;
                }
            }
        }

        return true;
    }

} 