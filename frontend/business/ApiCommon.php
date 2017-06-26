<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/12
 * Time: 16:58
 */

namespace frontend\business;

use common\components\PhpLock;
use common\components\SystemParamsUtil;
use common\models\Client;
use yii\base\Exception;
use yii\db\Query;
use \yii\log\Logger;
use common\components\UsualFunForStringHelper;

class ApiCommon
{
    /**
     * 获取api签名
     * @param $rand_str，32位数字、大小写字母组成的随机字符串
     * @param $time，时间戳
     * @param $token，令牌
     * @param $data，aes加密数据
     * @param $sign_key，签名密钥
     * @return string
     */
    public static function GetApiSign($rand_str, $time, $token,$data,$sign_key)
    {
        $soureStr = sprintf('rand_str=%s&time=%s&token=%s&data=%s&myyuanparampasssignkey=%s',
            $rand_str, $time,$token,$data, $sign_key);
        //\Yii::getLogger()->log($soureStr, Logger::LEVEL_ERROR);
        return md5($soureStr);
    }

    /**
     *检查协议的合法性
     * @param $params
     * @return bool
     */
    public static function CheckApiProtocol($params,&$error)
    {
        /**
        {
        "api_version":"v1",
        "device_type":"",
        "device_no":"",
        "action_name":"login",
        "has_data":"0",
        "data_type":"json",
        "data":{"device_no":"","phone_no":""},
        }
         */
        if(!is_array($params))
        {
            $error = '协议中的数据格式不正确';
            return false;
        }
        if(!isset($params['action_name']) || empty($params['action_name']))
        {
            $error = '功能信息为空';
            return false;
        }
        if(!isset($params['api_version']) || empty($params['api_version']))
        {
            $error = '版本信息为空';
            return false;
        }
        if(!isset($params['device_type']) || empty($params['device_type']))
        {
            $error = '设备类型不能为空';
            return false;
        }
        if(!in_array($params['device_type'],['1','2']))
        {
            $error = '设备类型错误';
            return false;
        }
        if(!isset($params['device_no']) || empty($params['device_no']))
        {
            $error = '设备号不能为空';
            return false;
        }
        if(!isset($params['has_data']))
        {
            $error = 'has_data参数缺失';
            return false;
        }
        if(!in_array($params['has_data'],array('0','1')))
        {
            //$error = 'has_data参数不正确';
            //return false;
        }

        return true;
    }

    /**
     * 检查登录状态
     * @param $dataParam，发送的json数据中的data字段
     * @param $error,返回错误信息
     * @return bool
     */
    public static function CheckLogin($passData,&$error)
    {
        $error = '';
        $dataParam = $passData['data'];
        $device_no = $passData['device_no'];
        $unique_no = $passData['data']['unique_no'];
        $register_type = $passData['data']['register_type'];
        //\Yii::getLogger()->log('register_tyep: '.$register_type,Logger::LEVEL_ERROR);
        //$dataParam['device_no'] = $passData['device_no'];
        //\Yii::getLogger()->log('设备号: '.$passData['device_no'],Logger::LEVEL_ERROR);
        if(!isset($device_no) || empty($device_no))
        {
            $error = '设备号不能为空';
            \Yii::getLogger()->log($error.' '.var_export($dataParam,true),Logger::LEVEL_ERROR);
            return false;
        }
        if(empty($register_type) || !in_array($register_type,\Yii::$app->params['verify_code_type_list']))
        {
            $error = '网络有点偷懒'; //登录类型不正确
            \Yii::getLogger()->log($error.' '.var_export($passData,true),Logger::LEVEL_ERROR);
            return false;
        }
        if(!isset($unique_no) || empty($unique_no))
        {
            $error = '唯一号不能为空';
            \Yii::getLogger()->log($error.' '.var_export($passData,true),Logger::LEVEL_ERROR);
            return false;
        }
        $sysLoginInfo = null;
        if(!self::GetLoginInfo($unique_no, $sysLoginInfo, $error))
        {
            return false;
        }
        $sourceDeviceNo = $sysLoginInfo['device_no'];
        if(!isset($sourceDeviceNo) || empty($sourceDeviceNo))
        {
            $error = '系统错误，登录设备号丢失';
            \Yii::getLogger()->log('系统错误，登录设备号丢失',Logger::LEVEL_ERROR);
            return false;
        }
        $user_id = $sysLoginInfo['user_id'];
        $living = LivingUtil::GetClientLivingInfoByLivingMasterId($user_id);
        if($sourceDeviceNo !== $device_no)
        {
            $error = ['errno'=>'1101','errmsg'=>'您的账号已经在其他设备上登录'];
            if(!LivingNewUtil::SetBanClientFinishLivingToStopLiving($living['living_id'],null,$living['living_master_id'],$living['other_id'],$outInfo,$error)) {
                \Yii::error('关闭直播间异常 :'.$error);
            }
            \Yii::getLogger()->log('您的账号已经在其他设备上登录, sdno:'.$sourceDeviceNo.' curdno:'.$device_no,Logger::LEVEL_ERROR);
            return false;
        }
        $user = ClientUtil::GetClientById(intval($user_id));
        if($user->status === 0)
        {
            if($passData['action_name'] === 'heart_beat')
            {
                $error = ['errno'=>'1104','errmsg'=>'直播已禁止'];
            }
            else
            {
                $error = ['errno'=>'1108','errmsg'=>'您已被禁用，请与管理员联系'];
                \Yii::getLogger()->log($error['errmsg'].' : user_id:'.$user_id.'  status:'.$user->status,Logger::LEVEL_ERROR);
            }
            return false;
        }
        return true;
    }

    /**
     * 检测app多版本
     * @param $passData
     * @param $error
     * @return bool
     */
    public static function CheckAppId($passData,&$error)
    {
        $key = 'app_version_info';
        $cnt = \Yii::$app->cache->get($key);
        if($cnt === false)
        {
            $lock = new PhpLock($key.'ddjjosdf');
            $lock->lock();
            $cnt = \Yii::$app->cache->get($key);
            if($cnt === false)
            {
                //从数据库读取
                $vs = MultiVersionInfoUtil::GetAllVersions();
                $cnt = json_encode($vs);
                \Yii::$app->cache->set($key,$cnt);
            }
            $lock->unlock();
        }
        if(empty($cnt))
        {
            $error = 'app版本信息丢失';
            return false;
        }
        //\Yii::getLogger()->log('vinfo:'.$cnt,Logger::LEVEL_ERROR);
        $vInfos = json_decode($cnt,true);
        if(!isset($vInfos) || !is_array($vInfos))
        {
            $error = 'app版本信息异常';
            \Yii::getLogger()->log($error.': '.$vInfos,Logger::LEVEL_ERROR);
            return false;
        }

        if(!isset($vInfos[$passData['app_id']]))
        {
            $error = '此版本已经过期，请下载最新app';
            return false;
        }
        if(!isset($vInfos[$passData['app_id']]['status']) || !in_array($vInfos[$passData['app_id']]['status'],['0','1']))
        {
            $error = '此版本状态异常';
            return false;
        }
        if($vInfos[$passData['app_id']]['status'] == '0')
        {
            $error =empty($vInfos[$passData['app_id']]['forbid_words'])?'该版本已经停用，请下载其他版本app':$vInfos[$passData['app_id']]['forbid_words'];
            return false;
        }
        return true;
    }


    /**
     * 从缓存中获取协议版本配置,没有的话从数据库中获取
     * @param bool $reflash 是否刷新
     * @return mixed|string
     */
    public static function GetApiVersion($reflash = false)
    {
        if($reflash)
        {
            $version = SystemParamsUtil::GetSystemParam('set_version_agreement',$reflash,'value2');
            \Yii::$app->cache->set('get_api_version',$version);
            $rst = $version;
        }
        else
        {
            $cnt = \Yii::$app->cache->get('get_api_version');
            if($cnt === false)
            {
                $lock = new PhpLock('get_api_version_info');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('get_api_version');
                if($cnt === false)
                {
                    $version = SystemParamsUtil::GetSystemParam('set_version_agreement',true,'value2');
                    \Yii::$app->cache->set('get_api_version',$version);
                }
                else
                {
                    $rst = $cnt;
                }
                $lock->unlock();
            }
            else
            {
                $rst = $cnt;
            }
        }
        return $rst;
    }

    /**
     * 获取登录信息
     * @param $phoneNo
     * @param $sysLoginInfo
    array(
    'device_no'=>$deviceNo,
    'verify_code' => $verifyCode,
    'user_id'=>$ac->client_id,
    'unique_no' => $uniqueNo,
    'nick_name' =>$ac->nick_name,
    'client_type' =>$ac->client_type,
    );
     * @param $error
     * @return bool
     */
    public static function GetLoginInfo($unique_no,&$sysLoginInfo, &$error)
    {
        $error = '';
        $str = \Yii::$app->cache->get('mb_api_login_'.$unique_no);

        if(!isset($str) || empty($str))
        {
            $query = new Query();
            $logInfo = $query->select(['client_id as user_id','device_no','ifnull(vcode,\'\') as verify_code','unique_no','nick_name','client_type','is_inner'])
            ->from(['mb_client'])
            ->where(['unique_no'=>$unique_no])
            ->one();
            if($logInfo === false)
            {
                $error = ['errno'=>'1102','errmsg'=>'未登录，请进行登录'];
                \Yii::getLogger()->log('未登录，请进行登录,no caceh key:'.'mb_api_login_'.$unique_no,Logger::LEVEL_ERROR);
                return false;
            }
            $str = serialize($logInfo);
        }
        $sysLoginInfo = unserialize($str);
        if(!isset($sysLoginInfo) || empty($sysLoginInfo) || !is_array($sysLoginInfo))
        {
            $error = '系统错误，登录信息未能转化成正确格式';
            \Yii::getLogger()->log('系统错误，登录信息未能转化成正确格式',Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 删除登录状态
     * @param $phoneNo
     */
    public static function DelLoginInfo($unique_no)
    {
        return \Yii::$app->cache->delete('mb_api_login_'.$unique_no);
    }

    /**
     * 删除登录验证码
     * @param $unique_no
     */
    public static function DelLoginCode($unique_no)
    {
        return \Yii::$app->cache->delete('mb_api_verifycode_1_'.$unique_no);
    }
    /**
     * 从协议中获取唯一号、登录类型、设备号、设备类型
     * @param $dataProtocal
     * @param $deviceNo
     * @param $phoneNo
     * @return bool
     */
    public static function GetBaseInfoFromProtocol($dataProtocal, &$deviceNo, &$uniqueNo,&$registerType,&$deviceType,&$error)
    {
        if(!isset($dataProtocal['data']['register_type'])||
            empty($dataProtocal['data']['register_type']))
        {
            $error =  '登录类型不能为空，数据异常';
            return false;
        }

        if(!isset($dataProtocal['data']['unique_no'])||
            empty($dataProtocal['data']['unique_no']))
        {
            $error ='唯一号不能为空，数据异常';
            return false;
        }
        $deviceNo = $dataProtocal['device_no'];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $registerType = $dataProtocal['data']['register_type'];
        $deviceType = $dataProtocal['device_type'];
        return true;
    }

    /**
     * 获取地理信息
     * @param $dataProtocal
     * @param $regionInfo ,返回经度纬度数组
     * @param $error
     * @return bool
     */
    public static function GetRegionInfo($dataProtocal,&$regionInfo,&$error)
    {
        $regionInfo = null;
        $error ='';
        if(!isset($dataProtocal['extend_data']['region_info']) ||
            empty($dataProtocal['extend_data']['region_info']['longitude']) ||
            empty($dataProtocal['extend_data']['region_info']['latitude']))
        {
            $error = '美愿无法获取你当前的位置';
            return false;
        }
        $regionInfo = $dataProtocal['extend_data']['region_info'];
        return true;
    }

    /**
     * 将base64图片数据存储为图片文件
     * @param $picContent  base64数据
     * @param $backPath 返回路径，网站根目录upload下开始的图片路径
     * @param string $path 路径，只限一级，不能包含多级
     * @param string $picName 图片名称不包含后缀名
     * @param string $suffix 图片后缀名，不包含图片
     * @param bool $recover 是否覆盖，默认是
     * @param string $error  返回错误
     * @return bool
     */
    public static function SavePicFromBase64($picContent,&$backPath,$path = 'wish',$picName='auto',$suffix='jpg',&$error='',$recover = true)
    {
        $error = '';
        if(!empty($path))
        {
            $path = trim($path,'/\\');
        }
        $fPath = \Yii::$app->getBasePath().'/web/upload/';
        $backPath = 'upload/';
        if(!file_exists($fPath))
        {
            mkdir($fPath);
            chmod($fPath, 777);
        }
        $fPath .= (!empty($path)?$path:'other').'/';
        $backPath .= (!empty($path)?$path:'other').'/';
        if(!file_exists($fPath))
        {
            mkdir($fPath);
            chmod($fPath, 777);
        }
        if(empty($picContent))
        {
            $error = '图片内容不能为空';
            return false;
        }
        if(empty($picName) || $picName === 'auto')
        {
            $picRealName = md5(UsualFunForStringHelper::CreateGUID()).'.'.$suffix;
        }
        else
        {
            $picRealName = $picName.'.'.$suffix;
        }
        $fileRealName = $fPath.$picRealName;
        $backPath .= $picRealName;
        if($recover === false)
        {
            if(file_exists($fileRealName))
            {
                $error = '文件已经存在';
                return false;
            }
        }
        if(file_put_contents($fileRealName,base64_decode($picContent)) === false)
        {
            return false;
        }
        return true;
    }

    /**
     * TODO:设置登录信息缓存
     * @param $ary /TODO: 登录信息数组
     * @param $error
     * @return bool
     */
    public static function setLoginCache($ary, &$error)
    {
        $str = serialize($ary);
        \Yii::$app->cache->set('mb_api_login_'.$ary['unique_no'], $str,30*24*3600);//保持一个月
        $loginInfoStr = \Yii::$app->cache->get('mb_api_login_'.$ary['unique_no']);
        if(!isset($loginInfoStr) || empty($loginInfoStr))
        {
            $error = '存储登录信息异常，登录失败';
            return false;
        }
        return true;
    }
} 