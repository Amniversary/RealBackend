<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/24
 * Time: 下午3:26
 */

namespace backend\controllers\MyPicActions;


use common\components\mp3\mp3file;
use common\components\OssUtil;
use yii\base\Action;
use yii\log\Logger;

class UploadMp3Action extends Action
{
    public function run($file_type)
    {
        $rst = ['code'=>1 ,'msg'=>''];
        if(empty($file_type)) {
            $rst['msg']= '请上传文件类别';
            echo json_encode($rst);
            exit;
        }
        if(!isset($_FILES['file'])) {
            $rst['msg'] = '请上传文件';
            echo json_encode($rst);
            exit;
        }

        $tmpFile = $_FILES['file']['tmp_name'];
        if(!file_exists($tmpFile))
        {
            $rst['msg'] = '上传文件不存在';
            echo json_encode($rst);
            exit;
        }
        $Mp3Class = new mp3file($tmpFile);
        $mp3Info = $Mp3Class->get_metadata();
        $timeLenth =  $mp3Info['Length'];
        $size = $_FILES['file']['size'];
        $mp3size = intval($size / 1024);
        if($mp3size > 2040 ) {  //TODO:实际限制2048
            $rst['msg'] = '文件大小不能超过2MB';
            echo json_encode($rst);
            exit;
        }

        if($timeLenth > 60) {
            $rst['msg']= '音频长度不能大于60秒';
            \Yii::error('mp3Info :'.var_export($mp3Info,true));
            echo json_encode($rst);
            exit;
        }

        $typeStr = $_FILES['file']['type'];
        $typeItems = explode('/',$typeStr);
        if(count($typeItems) < 2)
        {
            $rst['msg']='文件类型解析异常';
            \Yii::error('type:'.$typeStr);
            echo json_encode($rst);
            exit;
        }
        $fileType = $typeItems[0];
        if($fileType !== 'audio')
        {
            $rst['msg']='文件类型不正确';
            echo json_encode($rst);
            exit;
        }
        $suffix = $typeItems[1];
        if(!in_array($suffix,['mpeg','mp3','AMR']))
        {
            $rst['msg'] = '不是正确的音频文件格式';
            echo json_encode($rst);
            exit;
        }
        //$picName = sha1(UsualFunForStringHelper::CreateGUID().date('YmdHis'));
        $picName = $_FILES['file']['name'];
        $picUrl = '';
        $error = '';
        if(!OssUtil::UploadQiniuFile($picName,$tmpFile,$picUrl,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        $rst['msg'] = $picUrl;
        echo json_encode($rst);
    }
}