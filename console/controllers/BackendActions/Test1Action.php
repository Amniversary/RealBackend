<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:39
 */

namespace console\controllers\BackendActions;


use common\components\ApiLogHelper;
use common\components\UsualFunForNetWorkHelper;
use frontend\business\ChatPersonGroupUtil;
use yii\base\Action;
use yii\log\Logger;

class Test1Action extends Action
{

    public function run()
    {
        set_time_limit(0);
        $filePath = __DIR__.'/../../runtime/api_logs/218_api_log_2016-10-10.log';
        $file = fopen($filePath, "r") or exit("Unable to open file!");
        $fileOutPath = __DIR__.'/../../runtime/api_logs/out.log';
        $fileOut = fopen($fileOutPath,'a');
//Output a line of the file until the end is reached
//feof() check if file read end EOF
        $i = 0;
        while(!feof($file))
        {
            $tmp = fgets($file);
            $tmp = json_decode($tmp,true);
            $tmp =ApiLogHelper::CompressApiLog($tmp);
            $tmp = json_encode($tmp).PHP_EOL;
            if($i < 5)
            {
                echo $tmp;
            }
            fwrite($fileOut,$tmp);
            $i ++;
        }
        fclose($file);
        fclose($fileOut);
        echo 'ok';
        exit;
        set_time_limit(0);
        $sql = 'select client_id,unique_no from mb_client where register_type=3 and  xinlang_uid is null';
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        $url = 'https://api.weibo.com/2/account/get_uid.json?access_token=%s';
        $update_sql = 'update mb_client set xinlang_uid=:uid where client_id=:cid';
        $ok_count = 0;
        $fail_count = 0;
        $i = 0;
        foreach($data as $one)
        {
            $rst_url = sprintf($url,$one['unique_no']);
            $d = UsualFunForNetWorkHelper::HttpGet($rst_url);
            $rst = json_decode($d,true);
            $uid = 'overtime';
            if(isset($rst['uid']))
            {
                $uid = $rst['uid'];
                $ok_count ++;
            }
            else
            {
                $fail_count ++;
            }
            $r_t = \Yii::$app->db->createCommand($update_sql,[':uid'=>$uid,':cid'=>$one['client_id']])->execute();
            if($r_t <= 0)
            {
                echo 'client_id:'.$one['client_id'].'excute_error uid:'.$uid.'<br/>';
            }
            echo date('Y-m-d').'count:'.$i.' fail_count:'.$fail_count.' ok_count:'.$ok_count."\n";
        }
        echo '<br/> ok_count:'.$ok_count.' fail_count:'.$fail_count.'<br/>';
    }
} 