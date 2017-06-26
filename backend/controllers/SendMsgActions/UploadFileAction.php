<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14
 * Time: 14:28
 */

namespace backend\controllers\SendMsgActions;

use common\components\UsualFunForStringHelper;
use yii\base\Action;
use yii\log\Logger;

class UploadFileAction extends Action
{
    public function run()
    {
        $path = \Yii::$app->basePath .'/data';
        if( !is_dir( $path ) )
        {
            @mkdir( $path );
        }
        $retVal = $this->uploadFile( $path );
        echo json_encode( $retVal );
    }

    function uploadFile( $path )
    {
        $tmpFile = $_FILES['upload_file']['tmp_name'];

        if(!file_exists($tmpFile))
        {
            $rst['msg'] = '上传文件不存在';
            echo json_encode($rst);
            exit;
        }

        $typeStr = $_FILES['upload_file']['type'];
        if( $typeStr != 'text/plain' )
        {
            $rst['msg']='文件类型不正确,请上传.txt类型的文件';
            echo json_encode($rst);
            exit;
        }

        $picName = sha1(UsualFunForStringHelper::CreateGUID().date('YmdHis'));

        if(is_uploaded_file($tmpFile))
        {
            $fullPath = $path."/".$picName.'.txt';

            if( move_uploaded_file($tmpFile,$fullPath) )
            {
                //将上传成功后的文件名赋给返回数组
                $res["info"] = $fullPath;
                $res["code"]= '0';

            }
            else
            {
                $res["info"]="上传文件失败！";
                $res["code"]= '1';
            }
        }

        return $res;
    }

}