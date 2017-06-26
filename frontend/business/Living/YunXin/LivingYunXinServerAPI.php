<?php
/**
 * 网易云信server API 接口 1.0
 * Class ServerAPI
 * @author  hzchensheng15@corp.netease.com
 * @date    2015-10-27  16:30
 * 
***/
namespace frontend\business\Living\YunXin;

class LivingYunXinServerAPI
{
    private static $AppKey='f0db27b7aabe1b086a608746eb7166e4';                //开发者平台分配的AppKey
    private static  $AppSecret='2c08e80dcd3e';             //开发者平台分配的AppSecret,可刷新
    const   HEX_DIGITS = "0123456789abcdef";

    /**
     * 参数初始化
     * @param $AppKey
     * @param $AppSecret
     * @param $RequestType [选择php请求方式，fsockopen或curl,若为curl方式，请检查php配置是否开启]
     */
/*    public function __construct($AppKey='f0db27b7aabe1b086a608746eb7166e4',$AppSecret='2c08e80dcd3e',$RequestType='curl'){
        $this->AppKey    = $AppKey;
        $this->AppSecret = $AppSecret;
        $this->RequestType = $RequestType;
    }*/

    /**
     * API checksum校验生成
     * @param  void
     * @return $CheckSum(对象私有属性)
     */
    public static function CheckSumBuilder(){
    	//此部分生成随机字符串
    	$hex_digits = self::HEX_DIGITS;
    	$nonce='';
    	for($i=0;$i<128;$i++){			//随机字符串最大128个字符，也可以小于该数
            $nonce.= $hex_digits[rand(0,15)];
    	}
    	$curTime = (string)(time());	//当前时间戳，以秒为单位

    	$join_string = self::$AppSecret.$nonce.$curTime;
    	return [
            'cur_time'=>$curTime,
            'nonce'=>$nonce,
            'check_sum'=>sha1($join_string)
        ];
    	//print_r($this->CheckSum);
    }

    /**
     * 将json字符串转化成php数组
     * @param  $json_str
     * @return $json_arr
     */
    public static function json_to_array($json_str){
        if(is_null(json_decode($json_str))){
            $json_str = $json_str;
        }else{
            $json_str = json_decode($json_str);
        }
        $json_arr=array();
        //print_r($json_str);
        foreach($json_str as $k=>$w){
            if(is_object($w)){               
                $json_arr[$k]= self::json_to_array($w); //判断类型是不是object
            }else if(is_array($w)){
                $json_arr[$k]= self::json_to_array($w);
            }else{
                $json_arr[$k]= $w;
            }
        }
        return $json_arr;
    }

    /**
     * 使用CURL方式发送post请求
     * @param  $url 	[请求地址]
     * @param  $data    [array格式数据]
     * @return $请求返回结果(array)
     */
    public static function postDataCurl($url,$data,&$out_data=null,&$error=''){
    	$checkSumIno = self::CheckSumBuilder();		//发送请求前需先生成checkSum
		 
		$timeout = 5000;  
        $http_header = array(
            'AppKey:'.self::$AppKey,
            'Nonce:'.$checkSumIno['nonce'],
            'CurTime:'.$checkSumIno['cur_time'],
            'CheckSum:'.$checkSumIno['check_sum'],
            'Content-Type:application/json;charset=utf-8'
        );
        //print_r($http_header);
        $postdata = json_encode($data);
		$ch = curl_init(); 
		curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt ($ch, CURLOPT_HEADER, false ); 
		curl_setopt ($ch, CURLOPT_HTTPHEADER,$http_header);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题  
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        
		$result = curl_exec($ch);  
        if (false === $result) {
            $error =  curl_errno($ch);
            curl_close($ch);
            return false;
        }
		curl_close($ch);
        $out_data =json_decode($result,true);
		return true;
    }


    /**
     * 创建云信ID
     * 1.第三方帐号导入到云信平台；
     * 2.注意accid，name长度以及考虑管理秘钥token
     * @param  $channel_name     [频道名称]
     * @param  $type      [频道类型（0:rtmp；1:hls；2:http）]
     * @return $result    [返回array数组对象]
     */
    public static function CreateChannel($channel_name,$type,&$outData,&$error)
    {
    	$url = 'https://vcloud.163.com/app/channel/create';
    	$data= array(
    		'name' => $channel_name,
    		'type'  => $type,
    	);
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 修改频道
     * @param $channel_id
     * @param $channel_name
     * @param $type
     * @param $outData
     * @param $error
     */
    public static function ModifyChannel($channel_id,$channel_name,$type,&$outData,&$error)
    {
        $url = 'https://vcloud.163.com/app/channel/update';
        $data= array(
            'name' => $channel_name,
            'type'  => $type,
            'cid'=>$channel_id
        );
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 删除频道
     * @param $channel_id
     * @param $error
     */
    public static function DeleteChannel($channel_id,&$error)
    {
        $url = 'https://vcloud.163.com/app/channel/delete';
        $data= array(
            'cid'=>$channel_id
        );
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 获取频道状态
     * @param $channel_id
     * @param $error
     * @return bool
     */
    public static function GetChannelStatus($channel_id,&$outData,&$error)
    {
        //频道状态：0空闲、1直播中、2直播录制中、3屏蔽
        $url = 'https://vcloud.163.com/app/channelstats';
        $data= array(
            'cid'=>$channel_id
        );
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 获取频道列表
     * @param $pageNo
     * @param $pageSize
     * @param $ofield
     * @param $sortFlog
     * @param $error
     * @return bool
     */
    public static function GetChannelList($pageNo, $pageSize,$ofield,$sortFlog,&$outData,&$error)
    {
        $url = 'https://vcloud.163.com/app/channellist';
        $data= array(
            'records'=>$pageSize,
            'pnum'=>$pageNo,
            'ofield'=>$ofield,//排序的域，支持的排序域为：ctime（默认）
            'sort'=>$sortFlog //升序还是降序，1升序，0降序，默认为desc
        );
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 获取频道流地址
     * @param $channel_id
     * @param $error
     * @return bool
     */
    public static function GetStreamAddress($channel_id,&$outData,&$error)
    {
        //频道状态：0空闲、1直播中、2直播录制中、3屏蔽
        $url = 'https://vcloud.163.com/app/address';
        $data= array(
            'cid'=>$channel_id
        );
        $outData = null;
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        if($outData['code'] !== 200)
        {
            $error = $outData['msg'];
            return false;
        }
        return true;
    }

    /**
     * 更新并获取新token
     * @param  $accid     [云信ID，最大长度32字节，必须保证一个APP内唯一（只允许字母、数字、半角下划线_、@、半角点以及半角-组成，不区分大小写，会统一小写处理）]
     * @return $result    [返回array数组对象]
     */
    public static function UpdateUserToken($accid,&$outData,&$error)
    {
        $url = 'https://api.netease.im/nimserver/user/refreshToken.action';
        $data= array(
            'accid' => $accid
        );
        if(!self::postDataCurl($url,$data,$outData,$error))
        {
            return false;
        }
        return true;
    }
}

?>