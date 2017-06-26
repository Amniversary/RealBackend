<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:39
 */

namespace console\controllers\BackendActions;


use common\components\GameRebotsHelper;
use common\components\OssUtil;
use common\components\PicHelper;
use common\components\rebots\RebotsUtil;
use common\components\UsualFunForStringHelper;
use frontend\business\ClientUtil;
use yii\base\Action;

class GenGameRebotAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $picPath = __DIR__.'/../../runtime/game_rebot_pics/pics/';
        if(!file_exists($picPath))
        {
            exit;
        }
        $fileList = scandir($picPath);//会包含隐藏文件
        $fileFilter = ['jpg','jpeg','png','gif','bmp'];
        $count = 0;
        foreach($fileList as $pic)
        {
            $fileExt = PicHelper::FileExt($pic);
            $fileExt = strtolower($fileExt);
            if(!in_array($fileExt,$fileFilter))
            {
                continue;
            }

            $fileName = md5(UsualFunForStringHelper::CreateGUID());
            $fileFullName = $picPath.$pic;
            echo $fileFullName."\n";
            $rst = OssUtil::UploadFile($fileName,$fileExt,'user-rebot-pic',$fileFullName,$picUrl,$error);
            echo $error;
            while(!$rst)
            {
                $rst = OssUtil::UploadFile($fileName,$fileExt,'user-rebot-pic',$fileFullName,$picUrl,$error);
                echo $error;
                usleep(200*1000);
            }
            $device_no = 'rebots_'.$fileName;
            $nick_name = RebotsUtil::GetNickName();
            $sex = rand(0,1) === 0?'女':'男';
            if(!ClientUtil::RegisterUserQiNiu($fileName,$device_no,['nick_name'=>$nick_name,'pic'=>$picUrl,'sex'=>$sex,'client_type'=>3,'register_type'=>5,'device_type'=>3],'mibo_rebot_empty',$error))
            {
                echo $error;
                exit;
            }
            unlink($fileFullName);
            $count ++;
            usleep(1000*30);
        }
        echo 'gen game rebots:'.strval($count)."\n";
    }
}