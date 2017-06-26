<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\MyPicActions;


use common\components\OssUtil;
use common\components\PicHelper;
use common\components\UsualFunForStringHelper;
use yii\base\Action;
use yii\log\Logger;

/**
 * 上传文件
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class UploadAction extends Action
{
    public function run($pic_type)
    {
        $rst = ['code'=>'1','msg'=>''];
        if(empty($pic_type))
        {
            $rst['msg']= '请上传图片类别';
            echo json_encode($rst);
            exit;
        }
        if(!isset($_FILES['upload_file']))
        {
            $rst['msg'] = '请上传文件';
            echo json_encode($rst);
            exit;
        }
        $tmpFile = $_FILES['upload_file']['tmp_name'];
        if(!file_exists($tmpFile))
        {
            $rst['msg'] = '上传文件不存在';
            echo json_encode($rst);
            exit;
        }
        $typeStr = $_FILES['upload_file']['type'];
        $typeItems = explode('/',$typeStr);
        if(count($typeItems) < 2)
        {
            $rst['msg']='文件类型解析异常';
            \Yii::getLogger()->log('type:'.$typeStr, Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //image/x-icon  image/png  image/gif  image/jpeg
        $fileType = $typeItems[0];
        if($fileType !== 'image')
        {
            $rst['msg']='文件类型不正确';
            echo json_encode($rst);
            exit;
        }
        $suffix = $typeItems[1];
        if(!in_array($suffix,['x-icon','png','gif','jpeg']))
        {
            $rst['msg'] = '不是图片文件';
            echo json_encode($rst);
            exit;
        }
        if($suffix === 'x-icon')
        {
            $suffix = 'ico';
        }
        if($suffix === 'jpeg')
        {
            $suffix = 'jpg';
        }
        $picName = sha1(UsualFunForStringHelper::CreateGUID().date('YmdHis'));
        $picUrl = '';
        $error = '';
        if(!OssUtil::UploadFile($picName,$suffix,$pic_type,$tmpFile,$picUrl,$error))
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