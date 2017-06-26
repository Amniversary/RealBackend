<?php
/**
 *
 * User: hlq
 * Date: 2016/8/20
 * Time: 14:58
 */
namespace frontend\controllers\MblivingActions;
use common\components\OssUtil;
use common\components\WeiXinUtil;
use frontend\business\ImageUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * 生成图片海报
 * Class MbGetImageAction
 * @package frontend\controllers\MblivingActions
 */
class MbGetImageAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        ini_set('post_max_size','15M');//修改post变量由2m变成1om,要比upload_max_filesize大
        ini_set('upload_max_filesize','10M');//文件上传最大
        $img_path = './mibo/wswh/';
        $error = '';
        $datas = \Yii::$app->request->post();
        $text = $datas['text'];
        $picture = $datas['picture'];
        $scale = $datas['scale'];
        $cut_dst_x = $datas['x'];
        $cut_dst_y = $datas['y'];
        $out_img_width = $datas['width'];
        $out_img_height = $datas['height'];
        $screenWidth = $datas['screenWidth'];
        $fields = ['picture','scale','x','y','width','height'];
        $len = count($fields);
        for($i=0;$i<$len;$i++) {
            if (empty($datas[$fields[$i]]) && ($datas[$fields[$i]] !== '0'))
            {
                \Yii::getLogger()->log('fields=== ===:'.$fields[$i],Logger::LEVEL_ERROR);
                $arr_data = ['error_msg' => '参数错误'];
                echo  json_encode($arr_data);
                exit;
            }
        }
        $picture = base64_decode(str_replace('data:image/jpeg;base64', '', $picture));
        $time = time();
        $img_name = $img_path.'source/src_img_'.$time.'.png';
        if (!file_put_contents($img_name, $picture)){
            $arr_data = ['error_msg' => '图片保存失败'];
            echo  json_encode($arr_data);
            exit;
        }
        $time1 = microtime(true);
        if(!ImageUtil::imagecropper($img_name,$img_path.'bg.png',$text,'我就是网红',$outImgPath,$error,$screenWidth,$out_img_width,$out_img_height,$cut_dst_x,$cut_dst_y,$scale))
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }
        $time2 = microtime(true);
        $time_out = $time2-$time1;
        \Yii::getLogger()->log('wswh_time_==1:'.$time_out,Logger::LEVEL_ERROR);

        $fName = 'dst_img_'.$time;
        $suffix = 'png';
        $file = $outImgPath;
        $picUrl = '';
        $error = '';
        $time1 = microtime(true);
        if(!OssUtil::UploadFile($fName,$suffix,'meibo-wswh',$file,$picUrl,$error))     //全成的图片上传OSS
        {
            $arr_data = ['error_msg' => $error];
            echo  json_encode($arr_data);
            exit;
        }
        @unlink($img_name);  //删除文件
        @unlink($file);
        $time2 = microtime(true);
        $time_out = $time2-$time1;
        \Yii::getLogger()->log('wswh_time_==2:'.$time_out,Logger::LEVEL_ERROR);
        $url = 'result.html?imgurl='.$picUrl.'&textword='.$text;
        $arr_data = ['error_msg' => 'ok','url' => $url];
        echo  json_encode($arr_data);
        exit;
    }
}




