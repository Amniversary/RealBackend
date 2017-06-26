<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\MbapiActions;


use common\components\DeviceUtil;
use common\components\UsualFunForStringHelper;
use frontend\business\ApiLogUtil;
use frontend\business\JobUtil;
use frontend\business\PaymentsUtil;
use yii\base\Action;
use frontend\business\ApiCommon;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;
use common\components\AESCrypt;

class DoAction extends Action
{
    /**
     * 检查参数
     * @param $error
     * @return bool
     */
    private function check_post_params(&$error)
    {
        $error = '';
        //$rand_str, $time, $token,$data,$token_other
        if(!isset($_POST['rand_str']) ||
            !isset($_POST['time']) ||
            !isset($_POST['token']) ||
            !isset($_POST['data']) ||
            !isset($_POST['token_other']))
        {
            $error = '参数缺少';
            \Yii::getLogger()->log('lost param:'.var_export($_POST,true),Logger::LEVEL_ERROR);
            return false;
        }
        if(empty($_POST['rand_str']) ||
            empty($_POST['time']) ||
            empty($_POST['token']) ||
            empty($_POST['data']) ||
            empty($_POST['token_other']))
        {
            \Yii::getLogger()->log('not empty:'.var_export($_POST,true),Logger::LEVEL_ERROR);
            $error = '参数不能为空';
            return false;
        }
        return true;
    }

    public function run()
    {
        $time1 = microtime(true);
        $rstOut = [
            "errno"=>"0",
            "errmsg"=>"提示信息",
            "has_data"=>"0",
            "data_type"=>"string",
            "data"=>""
        ];
        $rst = [
            "errno"=>"0",
            "errmsg"=>"提示信息",
        ];
        $error = '';


        if(!$this->check_post_params($error))
        {
            $rst['errno'] = '1';
            $rst['errmsg'] =$error;
            \Yii::getLogger()->log($rst['errmsg'],Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $rand_str = $_POST['rand_str'];
        $time = $_POST['time'];
        $token = $_POST['token'];
        $data = $_POST['data'];
        $token_other = $_POST['token_other'];
        $isUpdateSelfProtocal = false;
        $configAry = \Yii::$app->params['default_param_api'];
        $defaultToken = $configAry['token'];
        //\Yii::getLogger()->log('token:'.$token, Logger::LEVEL_ERROR);
        if(isset($token) && !empty($token) && $token === $defaultToken)
        {
            $isUpdateSelfProtocal = true;
            //默认配置
            $configAry = \Yii::$app->params['default_param_api'];
        }
        if($isUpdateSelfProtocal === false)
        {
            $key = 'my_api_key_'.$token;
            $configInfo = \Yii::$app->cache->get($key);
            if(!isset($configInfo) || empty($configInfo))
            {
                $rst['errno'] = '1109';
                $rst['errmsg'] = '网络有点偷懒'; //token令牌错误
                $configInfo = \Yii::$app->cache->get($key);
                $tmpRst = (!isset($configInfo) || empty($configInfo))?'no_value':$configInfo;
                if($tmpRst === 'no_value')
                {
                    \Yii::getLogger()->log($rst['errmsg'].' token_key:'.$key.' v:'.$tmpRst,Logger::LEVEL_ERROR);
                    echo json_encode($rst);
                    exit;
                }
                else
                {
                    \Yii::getLogger()->log($rst['errmsg'].' token_key:'.$key.' v1:'.$tmpRst,Logger::LEVEL_ERROR);
                }
            }

            $configAry = unserialize($configInfo);
            if(!is_array($configAry) || empty($configAry))
            {
                $rst['errno'] = '3';
                $rst['errmsg'] = '系统内部错误，找不到令牌对应的的配置信息';
                \Yii::getLogger()->log($rst['errmsg'].'  config_key:'.$key,Logger::LEVEL_ERROR);
                echo json_encode($rst);
                exit;
            }

        }

        $sign_key = $configAry['sign_key'];
        if(!isset($sign_key) || empty($sign_key))
        {
            $rst['errno'] = '4';
            $rst['errmsg'] = '系统内部错误，找不到签名密钥';
            \Yii::getLogger()->log($rst['errmsg'].' sign_key:'.$sign_key,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $sign = ApiCommon::GetApiSign($rand_str,$time,$token,$data,$sign_key);
        if($token_other !== $sign)
        {
            $rst['errno'] = '5';
            $rst['errmsg'] = '协议数据异常，签名错误';
            \Yii::getLogger()->log($rst['errmsg'].' my_sign:'.$token_other.', sign:'.$sign,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $crypt_key = $configAry['crypt_key'];
        if(!isset($crypt_key) || empty($crypt_key))
        {
            $rst['errno'] = '6';
            $rst['errmsg'] = '系统内部错误，找不到加密密钥';
            \Yii::getLogger()->log($rst['errmsg'].' config_Ary:'.var_export($configAry,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $cryptManager= new AESCrypt($crypt_key);
        $dataSource  = $cryptManager->decrypt($data);

        if($dataSource === false)
        {
            $rst['errno'] = '7';
            $rst['errmsg'] = 'data字符串无法解析';
            \Yii::getLogger()->log($rst['errmsg'].'   crypt_key:'.$crypt_key,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $dataAry = json_decode($dataSource, true);

        if(!isset($dataAry) || empty($dataAry))
        {
            $rst['errno'] = '8';
            $rst['errmsg'] = 'data内容无法转为json字符串';
            \Yii::getLogger()->log($rst['errmsg'].' data:'.$dataSource,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //检查协议合法行
        if(!ApiCommon::CheckApiProtocol($dataAry,$error))
        {
            $rst['errno'] = '9';
            $rst['errmsg'] = '发送的协议数据格式错误:'.$error;
            \Yii::getLogger()->log($rst['errmsg'].'     dataArray==:'.var_export($dataAry,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $version = $dataAry['api_version'];
        $version = strtoupper($version);
        $actionName = $dataAry['action_name'];
        if($isUpdateSelfProtocal && $actionName !== 'update_key')
        {
            $rst['errno'] = '1110';
            $rst['errmsg'] = '发送的协议错误';
            \Yii::getLogger()->log($rst['errmsg']. ' actonName:'.$actionName.' updateSelfProtocal:'.$isUpdateSelfProtocal,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }


        //检测是否系统维护
        if(!in_array($actionName, \Yii::$app->params['api_not_maintain']))
        {
            $is_maintain = \Yii::$app->params['is_maintain'];
            $unique_no = $dataAry['data']['unique_no'];
            $loginInfo = \Yii::$app->cache->get('mb_api_login_'.$unique_no);
            if(!isset($loginInfo) || empty($loginInfo))
            {
                $query = new Query();
                $logInfo = $query->select(['client_id as user_id','device_no','ifnull(vcode,\'\') as verify_code','unique_no','nick_name','client_type','is_inner'])
                    ->from(['mb_client'])
                    ->where(['unique_no'=>$unique_no])
                    ->one();
                serialize($logInfo);
            }
            $str = unserialize($loginInfo);
            if($is_maintain == '1')
            {
                if(!empty($str))
                {
                    if(!isset($str['is_inner']) || $str['is_inner'] == 1 )
                    {
                        $rst['errno'] = '1111';
                        $rst['errmsg'] = '亲爱的用户，为了给您更好的使用体验，我们将对服务器进行停机升级维护，给你带来不便，请谅解';
                        //\Yii::getLogger()->log($rst['errmsg'].': '.$is_maintain.';'.$actionName.'; str:'.var_export($str,true),Logger::LEVEL_ERROR);
                        echo json_encode($rst);
                        exit;
                    }
                }
                else
                {
                    $rst['errno'] = '1111';
                    $rst['errmsg'] = '亲爱的用户，为了给您更好的使用体验，我们将对服务器进行停机升级维护，给你带来不便，请谅解';
                    //\Yii::getLogger()->log($rst['errmsg'].': '.$is_maintain.';'.$actionName.' str:'.var_export($str,true),Logger::LEVEL_ERROR);
                    echo json_encode($rst);
                    exit;
                }
            }
        }


        //检查登录
        if(!in_array($actionName, \Yii::$app->params['api_not_login']))
        {
            $errorMsg = '';
            if(!ApiCommon::CheckLogin($dataAry, $errorMsg))
            {
                if(!is_array($errorMsg))
                {
                    $rst['errno'] = '11';
                    $rst['errmsg'] = $errorMsg;
                }
                else
                {
                    $rst = $errorMsg;
                }
                \Yii::getLogger()->log(var_export($rst,true),Logger::LEVEL_ERROR);
                echo json_encode($rst);
                exit;
            }
        }
        //检测版本
        $is_check = \Yii::$app->params['is_begin_check_app_id'];
        if(!isset($is_check))
        {
            $is_check = '0';
        }
        if($is_check === '1')//启用检测版本
        {
            if(!ApiCommon::CheckAppId($dataAry,$errorMsg))
            {
                if(!is_array($errorMsg))
                {
                    $rst['errno'] = '16';
                    $rst['errmsg'] = $errorMsg;
                }
                else
                {
                    $rst = $errorMsg;
                }
                \Yii::getLogger()->log(var_export($rst,true),Logger::LEVEL_ERROR);
                echo json_encode($rst);
                exit;
            }
        }
        //调用处理类处理并返回数据
        $api_versions = ApiCommon::GetApiVersion(true);
        //$api_versions = 'v1,v2';
        $version_params = explode(',',$api_versions);
        if(!in_array($dataAry['api_version'],$version_params))
        {
            $rst['errno'] = '1112';
            $rst['errmsg'] = '蜜播App版本过低，请更新到最新版本!';
            \Yii::getLogger()->log($rst['errmsg'].' api_versions:'.$api_versions.' app_version:'.$dataAry['api_version'].' version:'.$version.' action_name:'.$actionName,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        //\Yii::getLogger()->log($rst['errmsg'].' api_versions:'.$api_versions.' app_version:'.$dataAry['api_version'].' version:'.$version.' action_name:'.$actionName,Logger::LEVEL_ERROR);

        $configFile = \Yii::$app->getBasePath().'/zhiboapi/Config'.$version.'.php';
        //var_dump($configFile);
        if(!file_exists($configFile))
        {
            $rst['errno'] = '12';
            $rst['errmsg'] = '版本信息错误，找不到配置文件';
            \Yii::getLogger()->log($rst['errmsg'].' version:'.$version,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $funData = require($configFile);
        if(!isset($funData[$actionName]))
        {
            $rst['errno'] = '13';
            $rst['errmsg'] = '找不到对应的功能';
            \Yii::getLogger()->log($rst['errmsg'].' action:'.$actionName.' clientinfo:'.var_export($dataAry,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $actionClass = 'frontend\zhiboapi\\'.$dataAry['api_version'].'\\'.$funData[$actionName];
        if(!class_exists($actionClass))
        {
            $rst['errno'] = '14';
            $rst['errmsg'] = '对应的功能不存在';
            \Yii::getLogger()->log($rst['errmsg'].' class not exists,action:'.$actionName.'; file:'.$actionClass.' clientinfo:'.var_export($dataAry,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //写入协议访问日志
        $errorMsg = '';
        $fun = new $actionClass;
        try
        {
            if(!$fun->excute_action($dataAry, $rstOut,$errorMsg))
            {
                if(is_array($errorMsg))
                {
                    $rst = $errorMsg;
                }
                else
                {
                    $rst['errno'] = '15';
                    $rst['errmsg'] = $errorMsg;
                }
                \Yii::getLogger()->log($rst['errmsg'].' 功能执行有异常，action:'.$actionName,Logger::LEVEL_ERROR);
                echo json_encode($rst);
                exit;
            }
        }
        catch(Exception $e)
        {
            \Yii::getLogger()->log('error111111111:'.$e->getMessage(),Logger::LEVEL_ERROR);
            echo 'Setting unknown property: common\models\Client::getui1_id';
            exit;
        }


        $unique_no =(!empty($dataAry['data'])?(isset($dataAry['data']['unique_no'])?$dataAry['data']['unique_no']:''):'');
        $deviceNo = (isset($dataAry['device_no'])?$dataAry['device_no']:'');
        $device_type =(isset($dataAry['device_type'])?$dataAry['device_type']:'');
        $clientIp = DeviceUtil::GetClientRealIp();
        //加密返回数据
        //$rstOut['action_name'] = $actionName;
        //\Yii::getLogger()->log('rstdata:'.var_export($rstOut,true),Logger::LEVEL_ERROR);

        $tmp = json_encode($rstOut);
        $crypt_data = $cryptManager->encrypt($tmp);
        $time2 = microtime(true);
        $disTime = round($time2 - $time1, 3);//单位秒
        if($actionName == 'get_hot_living')
        {
            $apiLog = ApiLogUtil::GetNewModel($actionName,strval($disTime),$unique_no,$deviceNo,$device_type,$actionClass,$clientIp);
            if(!ApiLogUtil::SaveApiLog($apiLog,$error))
            {
                \Yii::getLogger()->log($error.'保存api日志信息失败',Logger::LEVEL_ERROR);
                return false;
            }
        }
        //输出处理结果
        echo $crypt_data;
    }
} 