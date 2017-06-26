<?php
// 天翼发送短信model
namespace common\components;

use common\models\TianyiAccesstoken;
use common\models\ShortmessageList;
use yii\log\Logger;

class SendShortMessage
{
	private static $token='';
	
	//获取token
	public static function GetAccessToken()
	{
		$nowtime=date('Y-m-d H:i:s');
		$res= TianyiAccesstoken::find()->one();
		if(!empty($res) && $res['valid_time'] > $nowtime){
			//access_token没有过期，复用
			self::$token=$res['access_token'];
			return true;
		}
		else{
            $res = new TianyiAccesstoken();
			//access_token过期了，重新获取
			$sms_acurl=\Yii::$app->params['sms_acurl'];
			$param_str='grant_type=client_credentials&app_id='.\Yii::$app->params['sms_appid'].'&app_secret='.\Yii::$app->params['sms_appsecret'];
			$output=UsualFunForNetWorkHelper::HttpsPost($sms_acurl,$param_str);
			//{"res_code":"0","res_message":"Success","access_token":"fd0029eecf2335cff44ffa449e33ee1f1450691881008","expires_in":2591728}
            $output_obj=json_decode($output);
			
			if($output_obj->res_code==0){
				self::$token=$output_obj->access_token;
				$timestamp1=strtotime($nowtime)+$output_obj->expires_in-3600;
				$valid_time=date('Y-m-d H:i:s',$timestamp1);
                $res->access_token = self::$token;
                $res->valid_time = $valid_time;
				$res->save();
				return true;
			}
			else{
				return false;
			}
		}
		
		return false;
	}

    /**
     * 获取短信模板
     * @param $templateid
     * @param $json_param
     * @return mixed|string
     */
	public static function GetSmsContent($templateid,$array_param)
    {
		$templates = require(\Yii::$app->getBasePath().'/../common/config/ShortMessageTemplate.php') ;
		$content = '';
		if (isset($templates[$templateid]) && !empty($templates[$templateid])) {
			$model = $templates[$templateid];
			if (is_array($array_param)) {
				//$params = json_decode($json_param,true);
				foreach ($array_param as $key=>$value){
					$model = str_replace('{'.$key.'}',$value,$model);
				}
				$content = $model;
			}
		}
		return $content;
	}

    /**
     * 根据验证码类型获取模板id
     * @param $code_type 验证码类型
     * @return mixed
     */
    public static function GetTemplateIdFromCodeType($code_type)
    {
        $codeTypetemplateList = require(\Yii::$app->getBasePath().'/../common/config/CodeTypeRelateTemplate.php');
        return $codeTypetemplateList[$code_type];
    }

	//发送短信  新的短信发送方法(第二办公室)
	//短信模板ID说明：91002992为催款;91002955为发送验证码;
    /**
     * 发送短信 2office，未启用
     * @param $tel
     * @param $code_type 验证码类型， 1登录 2注册、3修改密码、4重置密码
     * @param $json_param
     * @return stdClass
     */
	public static function SendMessage2office($tel,$code_type,$json_param,&$error)
	{
        $error = '';
        $templateid = self::GetTemplateIdFromCodeType($code_type);
        if(empty($templateid))
        {
            $error = '对应类别找不到模板';
            return false;
        }
			
        $smsInfo = \Yii::$app->params['erban_sms'];
        $url    = $smsInfo['url'];
        $params = $smsInfo['params'];
        $params['mobile'] = $tel;
        $params['content'] = self::GetSmsContent($templateid, $json_param);
        //已经自测ok先不调用了，正式测试时再打开
        $output = UsualFunForNetWorkHelper::HttpsPost($url, $params);
       // $output_obj->res_message=$output;

		//结果更新到短信发送记录表
		$tmparr=array(
				'createtime'=> date('Y-m-d H:i:s'),
				'tel'=>$tel,
				'content' => $json_param,
				'templateid' => $templateid,
				'res'=>$output,
                'status'=>'1'
		);
        //增加发送记录
        $shortmsgRecord = new ShortmessageList();
        $shortmsgRecord->attributes = $tmparr;
		$shortmsgRecord->save();
		return true;
	
	}
	//发送短信
	//短信模板ID说明：91002992为催款;91002955为发送验证码;
    /**
     * 189发送短信，启用
     * @param $tel 电话
     * @param $code_type 类型  1 登录， 2 注册，  3 修改密码，  4 忘记密码
     * @param $array_param  传递的短信模板参数值
     * @param $error 返回错误信息
     * @return bool
     */
    public static function SendMessage189($tel,$code_type,$array_param,&$error)
    {
        $error = '';
        $templateid = self::GetTemplateIdFromCodeType($code_type);
        if(empty($templateid))
        {
            $error = '对应类别找不到模板';
            return false;
        }

			if(!self::GetAccessToken()){
				//获取access_token失败
                $error = '获取189短信token失败';
				return false;
			}
			$url=\Yii::$app->params['sms_url'];
			$param_str='app_id='.\Yii::$app->params['sms_appid'];
			$param_str.='&access_token='.self::$token;
			$param_str.='&acceptor_tel='.$tel;
			$param_str.='&template_id='.$templateid;
			$param_str.='&timestamp='.date('Y-m-d H:i:s');
			if(is_array($array_param))
            {
				$json_param=json_encode($array_param);
			}
			$param_str.='&template_param='.$json_param;

			//已经自测ok先不调用了，正式测试时再打开
        $output=UsualFunForNetWorkHelper::HttpsPost($url,$param_str);
        //{"res_code":0,"res_message":"Success","idertifier":"90611221180554209776"}
        $result = false;
        if(!empty($output))
        {
            $rst = json_decode($output);
            if(isset($rst) && $rst->res_code === 0)
            {
                $result =true;
            }
        }
        $error = ($result?'发送成功': (isset($rst)?$rst->res_message:'发送失败，网络原因'));

        //结果更新到短信发送记录表
        $tmparr=array(
            'createtime'=> date('Y-m-d H:i:s'),
            'tel'=>$tel,
            'content' => $json_param,
            'templateid' => $templateid,
            'res'=>$error,
            'status' => ($result ? '1' : '0')
        );
        $shortmsgRecord = new ShortmessageList();
        $shortmsgRecord->attributes = $tmparr;
        if(!$shortmsgRecord->save())
        {
            \Yii::getLogger()->log(var_export($shortmsgRecord->getErrors(),true),Logger::LEVEL_ERROR);
        }

		return true;
	}

    /**
     * 示远科技短信
     */
    public static function SendMessageShiYuanKeji($tel,$code_type,$array_param,&$error)
    {
        $error = '';
        $templateid = self::GetTemplateIdFromCodeType($code_type);
        if(empty($templateid))
        {
            $error = '对应类别找不到模板';
            return false;
        }
        $post_data = array();
        $post_data['account'] = '4q9v4e';   //帐号 蜜播：4q9v4e   美愿：006043
        $post_data['pswd'] = 'o5eF7fIO';  //密码 蜜播：o5eF7fIO  美愿：Sd123456
        $msg = self::GetSmsContent($templateid, $array_param);
        $post_data['msg'] =urlencode(self::GetSmsContent($templateid, $array_param)); //短信内容需要用urlencode编码下
        $post_data['mobile'] = $tel; //手机号码， 多个用英文状态下的 , 隔开
        $post_data['product'] = ''; //产品ID  不需要填写
        $post_data['needstatus']='false'; //是否需要状态报告，需要true，不需要false
        $post_data['extno']='';  //扩展码   不用填写
        $url='http://120.26.69.248/msg/HttpBatchSendSM';
        $o='';
        foreach ($post_data as $k=>$v)
        {
            $o.="$k=".urlencode($v).'&';
        }
        $post_data=substr($o,0,-1);
        $result = false;
        $rst = UsualFunForNetWorkHelper::HttpsPost($url, $post_data);
        if(empty($rst))
        {
            $result =false;
        }
        else
        {
            $rstItems = explode(',',$rst);
            if($rstItems[1] === '0')
            {
                $error = '发送成功，示远科技短信';
                $result = true;
            }
            else
            {
                $error = '发送失败,示远科技短信，代号：'.$rstItems[1];
                $result =false;
            }
        }

        $tmparr=array(
            'createtime'=> date('Y-m-d H:i:s'),
            'tel'=>$tel,
            'content' =>  $msg,
            'templateid' => $templateid,
            'res'=>$error,
            'status' => ($result ? '1' : '0')
        );
        $shortmsgRecord = new ShortmessageList();
        $shortmsgRecord->attributes = $tmparr;
        if(!$shortmsgRecord->save())
        {
            \Yii::getLogger()->log(var_export($shortmsgRecord->getErrors(),true),Logger::LEVEL_ERROR);
        }
        return true;
    }

    /**
     * 大汉三通短信
     */
    public static function SendMessageDaHanSanTong($tel,$code_type,$array_param,&$error)
    {
        $error = '';
        $template = self::GetTemplateIdFromCodeType($code_type);
        if(empty($template))
        {
            $error = '找不到对应短信模板类';
            return false;
        }
        $post_data = [];
        $post_data['account'] = 'dh17481';       // 行业账号 dh17481   营销账号 dh17482
        $post_data['password'] = md5('TS60pZq6');  //行业短信 密码 TS60pZq6   营销密码  L41LjH1V 需要MD5加密
        $post_data['msgid'] = UsualFunForStringHelper::CreateGUID();
        $post_data['phones'] = $tel;  // 多个手机 用英文逗号 分隔
        $msg = SendShortMessage::GetSmsContent($template, $array_param);
        $post_data['content'] = $msg;
        $post_data['sign'] = '【蜜播直播】';//'';  //短信签名 头部自带  需要报备后使用
        $post_data['subcode'] = '17481';  // 行业短信对应子码 17481  营销短信对应子码 17482
        $post_data['sentime'] = '';  // 短信发送时间  填空 或 小于当前时间则立即发送
        $url = 'http://www.dh3t.com/json/sms/Submit';
        $json_data = json_encode($post_data);
        $http_rst = UsualFunForNetWorkHelper::HttpsPost($url,$json_data);
        //print_r($http_rst);
        $rst = json_decode($http_rst,true);
        if(empty($rst))
        {
            $result = false;
        }
        else
        {
            if($rst['result'] === '0')
            {
                $error = '发送成功，大汉三通短信';
                $result = true;
            }
            else
            {
                $error = '发送失败，大汉三通短信，代号:'.$rst['result'].'， 错误信息:'.$rst['desc'];
                $result = false;
            }
        }
        $data = [
            'createtime' => date('Y-m-d H:i:s'),
            'tel' => $tel,
            'content' => $msg,
            'templateid' => $template,
            'res' => $error,
            'status' => ($result ? '1' : '0'),
        ];
        $msgModel = new ShortmessageList();
        $msgModel->attributes = $data;
        if(!$msgModel->save())
        {
            \Yii::getLogger()->log(var_export($msgModel->getErrors(),true),Logger::LEVEL_ERROR);
        }

        return true;
    }
}
