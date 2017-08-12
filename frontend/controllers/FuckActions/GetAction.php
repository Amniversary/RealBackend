<?php
namespace frontend\controllers\FuckActions;

use backend\business\MenuUtil;
use yii\base\Action;

class GetAction extends Action
{
    public function run()
    {
        date_default_timezone_set("PRC");
        $id= 'J6lxWbGddyo81K1R';
        $key= 'KWfmyikygwv98bTG4xmbs7KMdNXMoV';
        $host = 'http://7xld1x.com1.z0.glb.clouddn.com';

        $now = time();
        $expire = 3*60; //设置该policy超时时间是10s. 即这个policy过了这个有效时间，将不能访问
        $end = $now + $expire;
        $expiration = MenuUtil::gmt_iso8601($end);

        $dir = 'realtech-test/';
//    if($_GET['dir']=='123456789'){
//        //表示没填写上传的目录
//        $dir = 'mblive/';
//    }else{
//        $dir = $_GET['dir'].'/';
//    }
        /*$dir = 'mibo-test/';*/
        //最大文件大小.用户可以自己设置
        $condition = ['content-length-range',0,1048576000];
        $conditions[] = $condition;

        //表示用户上传的数据,必须是以$dir开始, 不然上传会失败,这一步不是必须项,只是为了安全起见,防止用户通过policy上传到别人的目录
        $start = [0=>'starts-with', 1=>'$key', 2=>$dir];
        $conditions[] = $start;

        $arr = ['expiration'=>$expiration,'conditions'=>$conditions];
        //echo json_encode($arr);
        //return;
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = [];
        $response['accessid'] = $id;
        $response['host'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        //这个参数是设置用户上传指定的前缀
        $response['dir'] = $dir;
        echo json_encode($response);
    }
}



