<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/26
 * Time: 14:57
 */

namespace frontend\business;


use common\components\ApiLogHelper;
use common\components\UsualFunForNetWorkHelper;
use common\models\ApiLog;
use common\models\StatisticActiveUser;
use common\models\TerminalLog;
use common\models\TokenInfo;
use yii\log\Logger;

class ApiLogUtil
{
    /**
     * 获取api调用日志新模型
     * @param $fun_id
     * @param $time
     * @param $phoneNo
     * @param $deviceNo
     * @return ApiLog
     */
    public static function GetNewModel($fun_id,$time,$unique_no,$deviceNo,$device_type,$funFullName,$clientIp)
    {
        $api_file = ApiLogHelper::GetApiNameConfig();
        $data = $api_file[$fun_id]."\t".time()."\t".$unique_no."\t".$deviceNo."\t".$device_type."\t".$time."\t".$clientIp;
        /*$data = [
            '1'=>$api_file[$fun_id], // 1 fun_id
            '2'=> time(), //  2 create_time    date('Y-m-d H:i:s'); --> time()
            '3'=>$unique_no, // 3 unique_no
            '4'=>$deviceNo,  // 4 device_no
            '5'=>$device_type, // 5 device_type
            '6'=>$time,  // 6  remark1
            '7'=>$clientIp, // 7 remark2
            //'remark2'=>$funFullName,
        ];*/
        return $data;

    }

    /**
     * 保存api调用日志
     * @param $model
     * @param $error
     */
    public static function  SaveApiLog($model,&$error)
    {
        if(empty($model))
        {
            return;
        }
        $data = [
            'key_word'=>'set_api_log',
            'data'=> json_encode($model),
        ];

        if(!JobUtil::AddApiJob('set_api_job',$data,$error))
        {
            \Yii::getLogger()->log('api job save error:'.$error,Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 得到api_log签名sign
     *  @param array $params
     * @return string
     *
     */
    public static function GetApiLogSign($params){
        if(!is_array($params))
        {
            return false;
        }
        ksort($params);
        $str = '';
        foreach($params as $key=>$v){
            $key = strtolower($key);
            $str .= $key.'='.$v.'&';
        }
        $str .= 'key=Wg1Y3PRwrw3NOwMToQq5drGpv9uw7alPyMbVc8uL';
//        echo $str.'&p_sign='.sha1($str);
        return sha1($str);
    }


    /**
     * 得到api_log文件内容并写入数据库
     * @param $fileurl
     * @param $filename
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function CreateApiLog($filename,&$error)
    {
        set_time_limit(0);
        @ini_set('memory_limit', '2048M');
        $base_path = \Yii::$app->getBasePath();
//        $base_path = rtrim($base_path,'frontend');
        $put_file_path = $base_path.'/runtime/api_log_files/';
        $file_url = $put_file_path.'api_log_'.$filename.'.tar.gz';
        if(!file_exists($file_url))
        {
            $error = '文件不存在   '.$file_url;
            return false;
        }
        $tar_shell = 'tar -zxvf '.$file_url.' -C '.$put_file_path;
        $is_tar = shell_exec($tar_shell);

        if(empty($is_tar))
        {
            $error = '文件解压失败';
            return false;
        }

        $files_names = scandir($put_file_path);
        $put_file_name_month = 'api_log_month_'.date('Y-m',strtotime($filename)).'.log';
        $put_file_num_name_month = 'api_log_month_num_'.date('Y-m',strtotime($filename)).'.log';
        $get_file_names = [];  //记录已存在文件的文件名称
        $regex = '/\d_api_log_('.$filename.').log/';
        foreach($files_names as $file_name)
        {
            if(preg_match($regex, $file_name)){
                $get_file_names[] = $file_name;           //匹配出所有满足条件的文件名称
            }
        }
        if(empty($get_file_names))
        {
            $error = '统计文件不存在';
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }

        $day_num = 0;
        $new_temp_arr = [];
        $file_is_exists = 0;
        $file_month = [];
        $file_exists = 0;
        $month_num = 0;

        //如果月统计文件存在，读取文件
        if(file_exists($put_file_path.$put_file_name_month))
        {
            $file_month = file($put_file_path.$put_file_name_month);
            $file_is_exists = 1;
        }
        foreach($get_file_names as $fileName)
        {
            //66	1477113910	oVKOWs0eUDCbraTs6xfyS3aDhSA8	8EA3192B-0D15-425B-97FF-35E23B4844B3	2	0.12	10.32.121.3
            $file = fopen($put_file_path.$fileName, "r") or exit("Unable to open file!");
            while(!feof($file))
            {
               $file_data = fgets($file);
                $file_data = json_decode($file_data,true);
                $data = explode("\t",$file_data);
                if(($data['0'] == 60) && ($new_temp_arr[$data[2]] != 1) && !empty($data[2]))
                {
                    $new_temp_arr[$data[2]] = 1;  //中间变量，判断是否有重复值
                    $day_num++;   //日统计
                    if($file_is_exists == 0)  //文件不存在或空，直接写入月统计数据
                    {
                        $month_num++;      //月统计
                        $params_month = implode("\t",$data);
                        $params_month = json_encode($params_month);

                        $out_file = file_put_contents($put_file_path.$put_file_name_month,$params_month.PHP_EOL,FILE_APPEND);  //写入新的月数据
                        if(!$out_file)
                        {
                            \Yii::getLogger()->log($put_file_path.$put_file_name_month.'.log    文件写入失败',Logger::LEVEL_ERROR);
                            $error = $put_file_path.$put_file_name_month.'.log    文件写入失败';

                        }
                    }
                    else    //月统计存在并且不为空，加入新数据
                    {
                        foreach($file_month as $month)
                        {
                            $data_month = json_decode($month,true);
                            $data_month = explode("\t",$data_month);
                            if($data_month[2] == $data[2])    //如果unique_no存在直接跳出
                            {
                                $file_exists = 0;
                                break;
                            }
                            $file_exists = 1;
                        }

                        if($file_exists == 1)
                        {
                            $month_num++;      //月统计
                            $params_month = implode("\t",$data);
                            $params_month = json_encode($params_month);

                            $out_file = file_put_contents($put_file_path.$put_file_name_month,$params_month.PHP_EOL,FILE_APPEND);  //写入新的月数据
                            if(!$out_file)
                            {
                                \Yii::getLogger()->log($put_file_path.$put_file_name_month.'.log    文件更新失败',Logger::LEVEL_ERROR);
                                $error = $put_file_path.$put_file_name_month.'.log    文件更新失败';
                            }
                        }
                    }

                }
            }
            fclose($file);

        }
        if(!file_exists($put_file_path.$put_file_num_name_month))
        {
            $params_month_num = [
                'user_num' => $month_num
            ];
            $params_month_num = json_encode($params_month_num);
            $out_file = file_put_contents($put_file_path.$put_file_num_name_month,$params_month_num.PHP_EOL,FILE_APPEND);  //写入月统计结果数据
            if(!$out_file)
            {
                \Yii::getLogger()->log($put_file_path.$put_file_num_name_month.'.log    文件写入失败',Logger::LEVEL_ERROR);
                $error = $put_file_path.$put_file_num_name_month.'.log    文件写入失败';
                return false;

            }
        }
        else
        {
            $month_num_data = json_decode(file_get_contents($put_file_path.$put_file_num_name_month),true);
            $month_num += $month_num_data['user_num'];
            $params_month_num = [
                'user_num' => $month_num
            ];

            $params_month_num = json_encode($params_month_num);
            $out_file = file_put_contents($put_file_path.$put_file_num_name_month,$params_month_num.PHP_EOL);  //写入月统计结果数据
            if(!$out_file)
            {
                \Yii::getLogger()->log($put_file_path.$put_file_num_name_month.'.log    文件更新失败',Logger::LEVEL_ERROR);
                $error = $put_file_path.$put_file_num_name_month.'.log    文件更新失败';
                return false;

            }
        }
//        向服务器发送日活、月活统计数据
        if(!self::GetStatisticAction($day_num,$month_num,$filename,$error))
        {
            return false;
        }

        @unlink($file_url);  //删除文件
        foreach($get_file_names as $name)
        {
            @unlink($put_file_path.$name);
        }
        return true;
    }

    /**
     * 向正式服务器发送日活、月活统计数据
     * @param $day_num
     * @param $month_num
     * @param $date
     * @param $error
     * @return bool
     */
    public static function GetStatisticAction($day_num,$month_num,$date,&$error)
    {
        $send_data = [
            'user_num_1' => $day_num,
            'statistic_time_1' => $date,
            'statistic_type_1' => 1,
            'user_num_2' => $month_num,
            'statistic_time_2' => $date,
            'statistic_type_2' => 2
        ];
        $sign_data = self::SetApiLogSign($send_data);
        $send_data['rand_str'] = $sign_data['rand_str'];
        $send_data['time'] = $sign_data['time'];
        $send_data['p_sign'] = $sign_data['p_sign'];
        $url = 'http://manage1.mblive.cn/site/statisticapilog';
//        $token_str = \Yii::$app->cache208->set('api_log_'.$send_data['time'],1);
//        \Yii::getLogger()->log('cache208  设置缓存key===:api_log_'.$send_data['time'],Logger::LEVEL_ERROR);
        $return_data = UsualFunForNetWorkHelper::HttpsPost($url,$send_data); //统计完成向正式服务器发送数据
        if($return_data != 'ok')
        {
            $error = '将请求内容回传到服务器错误'.var_export($return_data,true);
            return false;
        }
        return true;
    }

    /**
     * 设置api签名
     * @param $params
     * @param int $length
     * @return array
     */
    public static function SetApiLogSign($params,$length=40)
    {
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $rand_str.=$strPol[rand(0,$max)];
        }

        if(empty($params['time']))
        {
            $params['time'] = time();
        }
        $params['rand_str'] = $rand_str;
        $p_sign = self::GetApiLogSign($params);
        $return_data = [
            'rand_str' => $rand_str,
            'time' => $params['time'],
            'p_sign' => $p_sign
        ];
        return $return_data;
    }

    /**
     * 保存日活
     * @param $params
     * @param $error
     * @return bool
     */
    public static  function InsertStatisticApiLogActive($params)
    {
        $record = new StatisticActiveUser();
        $record->statistic_time = $params['statistic_time'];
        $record->statistic_type = $params['statistic_type'];
        $record->user_num = $params['user_num'];
        if(!$record->save())
        {
            \Yii::getLogger()->log('保存日活统计记录失败：'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 保存月活
     * @param $params
     * @param $error
     * @return bool
     */
    public static  function InsertStatisticApiLogDayActive($params)
    {
        $mondel = self::GetInfoByStatisticTime($params['statistic_time']);
        if(empty($mondel))
        {
            $mondel = new StatisticActiveUser();
            $mondel->statistic_time = $params['statistic_time'];
            $mondel->statistic_type = $params['statistic_type'];
        }
        $mondel->user_num = $params['user_num'];

        if(!$mondel->save())
        {
            \Yii::getLogger()->log('保存日活统计记录失败：'.var_export($mondel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 通过日期得到信息
     * @param $time
     * @return null|static
     */
    public static function GetInfoByStatisticTime($time)
    {
        $return_info = StatisticActiveUser::findOne(['statistic_time'=>$time]);
        return $return_info;
    }

    /**
     * 清除mb_api_log表数据
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function TruncateApiLogTable()
    {
        $sql = 'TRUNCATE mb_api_log';
        $sql_out = \Yii::$app->db->createCommand($sql)->execute();
        if($sql_out !== 0)
        {
            return false;
        }
        return true;
    }

    /**
     * 创建前端日志信息模型并保存
     * @param $sentData
     * @param $error
     * @return bool
     */
    public static function NewCreateGatherErrorModel($sentData,&$error)
    {
        $model = new TerminalLog();
        $model->attributes = $sentData;

        if(!$model->save())
        {
            $error = '前端错误日志信息保存失败';
            \Yii::getLogger()->log($error.': '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 保存token信息到数据库
     * @param $data
     * @param $error
     * @return bool
     */
    public static function SaveTokenInfo($data , &$error)
    {
        $token = self::GetTokenInfo($data['token_key']);
        if($data['no_save'] == 1)
        {
            if(!isset($token))
            {
                $model = new TokenInfo();
                $model->token = $data['token'];
                $model->cache_token = $data['token_key'];
                $model->data = $data['txt'];
                $model->succeed_num = 0;
                $model->error_num = 0;
                $model->create_time = date('Y-m-d H:i:s');
                if(!$model->save())
                {
                    $error = '保存记录token信息失败';
                    \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                    return false;
                }
            }
        }
        $token = self::GetTokenInfo($data['token_key']);
        if(isset($token))
        {
            if($token->error_num == 0)
            {
                if($data['succeed'] == 1)
                {
                    $sql = 'update mb_token_info set succeed_num = succeed_num + 1, remark1 = :tm WHERE  cache_token = :tk';
                    $rst = \Yii::$app->db->createCommand($sql,[
                        ':tm'=>date('Y-m-d H:i:s'),
                        ':tk'=>$data['token_key'],
                    ])->execute();

                    if($rst <= 0)
                    {
                        $error = '更新token缓存请求成功次数失败';
                        \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($sql,[
                                ':tm'=>date('Y-m-d H:i:s'),
                                ':tk'=>$data['token_key'],
                            ])->rawSql,Logger::LEVEL_ERROR);
                        return false;
                    }
                }
                else
                {
                    $sql = 'update mb_token_info set error_num = error_num + 1, remark1 = :tm WHERE  cache_token = :tk';
                    $rst = \Yii::$app->db->createCommand($sql,[
                        ':tm'=>date('Y-m-d H:i:s'),
                        ':tk'=>$data['token_key'],
                    ])->execute();

                    if($rst <= 0)
                    {
                        $error = '更新token缓存请求失败次数失败';
                        \Yii::getLogger()->log($error.' '.\Yii::$app->db->createCommand($sql,[
                                ':tm'=>date('Y-m-d H:i:s'),
                                ':tk'=>$data['token_key'],
                            ])->rawSql,Logger::LEVEL_ERROR);
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public static function GetTokenInfo($token_key)
    {
        return TokenInfo::findOne(['cache_token'=>$token_key]);
    }
} 