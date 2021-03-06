<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/8/17
 * Time: 10:00
 */

namespace backend\business;


use common\components\UsualFunForStringHelper;
use common\models\QrcodeImg;
use yii\log\Logger;

class ImageUtil
{
    /**
     * 生成海报图片
     * @param $qrcodePath //二维码地址
     * @param $picPath //头像地址
     * @param $openid //生成图标名称标识
     * @param $text //插入的文本
     * @param $filename //返回生成后的物理地址
     * @param $error //错误信息
     * @return bool
     */
    public static function imagemaking($qrcodePath, $picPath, $openid, $text, &$filename, &$error)
    {
        if (!function_exists('gd_info')) {
            $error = '请开启 GD库扩展';
            return false;
        }
        @ini_set('memory_limit', '128M');
        $bg_path = \Yii::$app->basePath . '/runtime/source/bg.jpg';
        $font = \Yii::$app->basePath . '/runtime/source/simhei.ttf';
        $bg_info = getimagesize($bg_path);
        $bg_mime = $bg_info['mime'];

        switch ($bg_mime) {  //TODO: 背景图 判断图片类型
            case 'image/gif':
                $bg_image = imagecreatefromgif($bg_path);
                break;
            case 'image/jpeg':
                $bg_image = imagecreatefromjpeg($bg_path);
                break;
            case 'image/png':
                $bg_image = imagecreatefrompng($bg_path);
                break;
            default:
                $error = '背景源图片类型不正确';
                return false;
                break;
        }
        $pic_info = getimagesize($picPath);
        $picmime = $pic_info['mime'];
        switch ($picmime) { //TODO: 用户头像 判断类型
            case 'image/gif':
                $pic_image = imagecreatefromgif($picPath);
                break;
            case 'image/jpeg':
                $pic_image = imagecreatefromjpeg($picPath);
                break;
            case 'image/png':
                $pic_image = imagecreatefrompng($picPath);
                break;
            default:
                $error = '头像源图片类型不正确';
                return false;
                break;
        }
        $qrcode_info = getimagesize($qrcodePath);
        $qrcode_mime = $qrcode_info['mime'];
        switch ($qrcode_mime) {  //TODO:  二维码
            case 'image/gif':
                $qrcode_image = imagecreatefromgif($qrcodePath);
                break;
            case 'image/jpeg':
                $qrcode_image = imagecreatefromjpeg($qrcodePath);
                break;
            case 'image/png':
                $qrcode_image = imagecreatefrompng($qrcodePath);
                break;
            default:
                $error = '二维码源图片类型不正确';
                return false;
                break;
        }

        $target_image = imagecreatetruecolor(550, 825);
        imagecopyresampled($target_image, $bg_image, 0, 0, 0, 0, 550, 825, $bg_info[0], $bg_info[1]);   //源图裁剪为目标图片大小
        /**将输出图片设为透明**/

        $new_pic_img = imagecreatetruecolor(100, 100); //TODO: 创建新的图片资源 设置宽高 100*100 pic
        $alpha = imagecolorallocatealpha($new_pic_img, 0, 0, 0, 127);
        imagefill($new_pic_img, 0, 0, $alpha);
        imageColorTransparent($new_pic_img, $alpha);
        imagecopyresampled($new_pic_img, $pic_image, 0, 0, 0, 0, 100, 100, $pic_info[0], $pic_info[1]); //TODO: 将原图剪裁成100*100

        $new_qrcode_img = imagecreatetruecolor(170, 170);
        $alpha = imagecolorallocatealpha($new_qrcode_img, 0, 0, 0, 127);
        imagefill($new_qrcode_img, 0, 0, $alpha);
        imagecolortransparent($qrcode_image, $alpha);
        imagecopyresampled($new_qrcode_img, $qrcode_image, 0, 0, 0, 0, 170, 170, $qrcode_info[0], $qrcode_info[1]);

        imagecopyresampled($target_image, $new_qrcode_img, 65, 590, 0, 0, 170, 170, 170, 170); //TODO:将二维码填充到底图
        imagecopyresampled($target_image, $new_pic_img, 40, 55, 0, 0, 100, 100, 100, 100); //TODO: 将剪裁的图片填充到底图中
        $black = imagecolorallocate($target_image, 0, 0, 0);
        imagettftext($target_image, 22, 0, 160, 90, $black, $font, $text);

        $filename = \Yii::$app->basePath . '/runtime/bgimg/bg_' . $openid . '.jpeg';
        imagejpeg($target_image, $filename, 90);

        imagedestroy($bg_image);
        imagedestroy($pic_image);
        imagedestroy($qrcode_image);
        imagedestroy($target_image);
        imagedestroy($new_qrcode_img);
        imagedestroy($new_pic_img);
        return true;
    }

    /**
     * 生成用户签到图片
     * @return bool
     */
    public static function imageSignUp($picPath, $text, $openid, &$filename, &$error)
    {
        if (!function_exists('gd_info')) {
            $error = '请开启 GD库扩展';
            return false;
        }
        @ini_set('memory_limit', '128M');
//        $font = \Yii::$app->basePath .'/runtime/sign_up/Kaiti.ttc';
        $name_font = \Yii::$app->basePath . '/runtime/sign_up/Songti.ttc';
        $font = \Yii::$app->basePath . '/runtime/signimg/HiraginoW3.ttc';

        $pic_info = getimagesize($picPath);
        $pic_mime = $pic_info['mime'];
        switch ($pic_mime) { //TODO: 用户头像 判断类型
            case 'image/gif':
                $pic_image = imagecreatefromgif($picPath);
                break;
            case 'image/jpeg':
                $pic_image = imagecreatefromjpeg($picPath);
                break;
            case 'image/png':
                $pic_image = imagecreatefrompng($picPath);
                break;
            default:
                $error = '头像源图片类型不正确';
                return false;
                break;
        }

        $target_image = imagecreatetruecolor($pic_info[0], $pic_info[1]);
        imagecopyresampled($target_image, $pic_image, 0, 0, 0, 0, $pic_info[0], $pic_info[1], $pic_info[0], $pic_info[1]);   //源图裁剪为目标图片大小
        /**将输出图片设为透明**/

        $hui = imagecolorallocate($target_image, 158, 151, 135);
        $black = imagecolorallocate($target_image, 2, 2, 2);
        $date = UsualFunForStringHelper::DateConversion(date('Y-m-d'));
        $len = strlen($text);
        if($len > 48) {
            $sub = $len - 48;
            $text = mb_substr($text, 0, -$sub);
        }
        $y = 59;
        $font_size = 14;
        if($len <= 9) {
            $x = 440;
        } elseif($len > 9 && $len <= 15) {
            $x = 415;
        } elseif($len > 15 && $len <= 24) {
            $x = 400;
        } elseif($len > 24 && $len <= 30) {
            $x = 395; $font_size = 12;
        } elseif($len > 30 && $len <= 36) {
            $x = 395; $font_size = 10;
        } elseif($len > 36 && $len <= 42) {
            $x = 395; $font_size = 9;
        } elseif($len > 42 && $len <= 48) {
            $x = 385; $font_size = 8;
        } else {
            $x = 385; $font_size = 8;
        }
        imagettftext($target_image, $font_size, 0, $x, $y, $black, $name_font, $text);
        imagettftext($target_image, 14, 0, 480, 499, $hui, $font, $date);
//        header('content-type: image/png');
//        imagepng($target_image);
//        exit;
        $filename = \Yii::$app->basePath . '/runtime/sign_up/source/sign_' . $openid . '.jpeg';
        imagejpeg($target_image, $filename, 90);

        imagedestroy($pic_image);
        imagedestroy($target_image);
        return true;
    }


    /**
     * 生成用户签到图片
     * @return bool
     */
    public static function imageSign($bgPath, $picPath, $openid, $text, &$filename, &$error)
    {
        if (!function_exists('gd_info')) {
            $error = '请开启 GD库扩展';
            return false;
        }
        @ini_set('memory_limit', '128M');
        $font = \Yii::$app->basePath . '/runtime/signimg/HiraginoW3.ttc';
        $bg_info = getimagesize($bgPath);
        $bg_mime = $bg_info['mime'];

        switch ($bg_mime) {  //TODO: 背景图 判断图片类型
            case 'image/gif':
                $bg_image = imagecreatefromgif($bgPath);
                break;
            case 'image/jpeg':
                $bg_image = imagecreatefromjpeg($bgPath);
                break;
            case 'image/png':
                $bg_image = imagecreatefrompng($bgPath);
                break;
            default:
                $error = '背景源图片类型不正确';
                return false;
                break;
        }
        $pic_info = getimagesize($picPath);
        $picmime = $pic_info['mime'];
        switch ($picmime) { //TODO: 用户头像 判断类型
            case 'image/gif':
                $pic_image = imagecreatefromgif($picPath);
                break;
            case 'image/jpeg':
                $pic_image = imagecreatefromjpeg($picPath);
                break;
            case 'image/png':
                $pic_image = imagecreatefrompng($picPath);
                break;
            default:
                $error = '头像源图片类型不正确';
                return false;
                break;
        }

        $target_image = imagecreatetruecolor($bg_info[0], $bg_info[1]);
        imagecopyresampled($target_image, $bg_image, 0, 0, 0, 0, $bg_info[0], $bg_info[1], $bg_info[0], $bg_info[1]);   //源图裁剪为目标图片大小
        /**将输出图片设为透明**/

        $new_pic_img = imagecreatetruecolor(80, 80); //TODO: 创建新的图片资源 设置宽高 100*100 pic
        $alpha = imagecolorallocatealpha($new_pic_img, 0, 0, 0, 127);
        imagefill($new_pic_img, 0, 0, $alpha);
        imageColorTransparent($new_pic_img, $alpha);
        imagecopyresampled($new_pic_img, $pic_image, 0, 0, 0, 0, 80, 80, $pic_info[0], $pic_info[1]); //TODO: 将原图剪裁成100*100

        imagecopyresampled($target_image, $new_pic_img, 340, 40, 0, 0, 80, 80, 80, 80); //TODO: 将剪裁的图片填充到底图中
        $black = imagecolorallocate($target_image, 0, 0, 0);
        $red = imagecolorallocate($target_image, 111, 6, 10);
        $len = strlen($text['name']);
        $name = mb_substr($text['name'], 0, 6);
        imagettftext($target_image, 16, 0, 200, 60, $black, $font, $name);
        if ($len > 6) {
            $name2 = mb_substr($text['name'], 6, 6);
            imagettftext($target_image, 16, 0, 200, 85, $black, $font, $name2);
        }
        if ($len > 13) {
            $name3 = mb_substr($text['name'], 12, 6);
            imagettftext($target_image, 16, 0, 200, 110, $black, $font, $name3);
        }
        imagettftext($target_image, 26, 0, 135, 725, $red, $font, $text['num']);
        //header('content-type: image/png');
        //imagepng($target_image);
        $filename = \Yii::$app->basePath . '/runtime/signimg/source/bg_' . $openid . '.jpeg';
        imagejpeg($target_image, $filename, 90);

        imagedestroy($bg_image);
        imagedestroy($pic_image);
        imagedestroy($target_image);
        imagedestroy($new_pic_img);
        return true;
    }

    /**
     * 生成用户早晚安图片
     * @return bool
     */
    public static function imageLater($bgPath, &$filename, &$error)
    {
        if (!function_exists('gd_info')) {
            $error = '请开启 GD库扩展';
            return false;
        }
        @ini_set('memory_limit', '128M');
        $font = \Yii::$app->basePath . '/runtime/signimg/HiraginoW3.ttc';
        $bg_info = getimagesize($bgPath);
        $bg_mime = $bg_info['mime'];

        switch ($bg_mime) {  //TODO: 背景图 判断图片类型
            case 'image/gif':
                $bg_image = imagecreatefromgif($bgPath);
                break;
            case 'image/jpeg':
                $bg_image = imagecreatefromjpeg($bgPath);
                break;
            case 'image/png':
                $bg_image = imagecreatefrompng($bgPath);
                break;
            default:
                $error = '背景源图片类型不正确';
                return false;
                break;
        }
        $target_image = imagecreatetruecolor(500, 825);
        imagecopyresampled($target_image, $bg_image, 0, 0, 0, 0, 500, 825, $bg_info[0], $bg_info[1]);   //源图裁剪为目标图片大小
        /**将输出图片设为透明**/

        $black = imagecolorallocate($target_image, 255, 255, 255);
        $back = imagecolorallocate($target_image, 0,0,0);
        $text = date('Y.m.d');
        $x = 500;
        $y = 825;
        $font_size = 14;
        //imagettftext($target_image, 18, 0, $x * 0.4 ,$y * 0.95, $black, $font, $text);
        imagettftext($target_image, $font_size, 0, ($x+3) * 0.4 , $y * 0.95, $black, $font, $text);
        imagettftext($target_image, $font_size, 0, ($x-1)* 0.4, $y * 0.95, $black, $font, $text);
        imagettftext($target_image, $font_size, 0, $x* 0.4, ($y-1) * 0.95, $black, $font, $text);
        imagettftext($target_image, $font_size, 0, $x* 0.4, ($y+1) * 0.95, $black, $font, $text);
        imagettftext($target_image, $font_size, 0, $x* 0.4 , $y * 0.95, $back, $font, $text);
//        header('content-type: image/png');
//        imagepng($target_image);
        $filename = \Yii::$app->basePath . '/runtime/signimg/source/bg_' . time() . '.jpeg';
        imagejpeg($target_image, $filename, 90);
        imagedestroy($bg_image);
        imagedestroy($target_image);
        return true;
    }

    /**
     * @param $source_path               源图片地址
     * @param $bg_img_path               背景图片地址
     * @param int $out_img_width 输出图片宽度
     * @param int $out_img_height 输出图片高度
     * @param int $cut_dst_x 裁剪图片 X 轴
     * @param int $cut_dst_y 裁剪图片 Y 轴
     * @param int $scale 缩放比例
     * @param string $error 错误信息
     */
    public static function imagecropper($source_path, $bg_img_path, $text = [], $title, &$outImgPath, &$error, $screenWidth = 320, $out_img_width = 752, $out_img_height = 1184, $cut_dst_x = 1290, $cut_dst_y = 888, $scale = 14)
    {
        if (!function_exists('gd_info')) {
            $error = '请开启GD库扩展';
            return false;
        }

        @ini_set('memory_limit', '2048M');

        $source_info = getimagesize($source_path);   //源文件图片地址
        $source_width = $source_info[0];
        $source_height = $source_info[1];
        $source_mime = $source_info['mime'];


        $target_width = intval($source_width * $scale);        //缩放比例
        $target_height = intval($source_height * $scale);
        switch ($source_mime)              //判断图片类型
        {
            case 'image/gif':
                $source_image = imagecreatefromgif($source_path);
                break;

            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($source_path);
                break;

            case 'image/png':
                $source_image = imagecreatefrompng($source_path);
                break;

            default:
                $error = '源图片类型不正确';
                return false;
                break;
        }

        $bg_info = getimagesize($bg_img_path);
        $bg_width = $bg_info[0];
        $bg_height = $bg_info[1];
        $bg_mime = $bg_info['mime'];
        switch ($bg_mime)              //判断图片类型
        {
            case 'image/gif':
                $bg_imgage_resources = imagecreatefromgif($bg_img_path);
                break;

            case 'image/jpeg':
                $bg_imgage_resources = imagecreatefromjpeg($bg_img_path);
                break;

            case 'image/png':
                $bg_imgage_resources = imagecreatefrompng($bg_img_path);
                break;

            default:
                $error = '背景图片类型不正确';
                return false;
                break;
        }

        $target_image = imagecreatetruecolor($target_width, $target_height);
        $cropped_image = imagecreatetruecolor($out_img_width, $out_img_height);
        $alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
        imagefill($cropped_image, 0, 0, $alpha);
        imageColorTransparent($cropped_image, $alpha);

        imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);   //源图裁剪为目标图片大小
        imagecopy($cropped_image, $target_image, 0, 0, $cut_dst_x, $cut_dst_y, $out_img_width, $out_img_height);    // 将裁剪后的图片缩放
        /**将输出图片设为透明**/
        $new_bg_imgage = imagecreatetruecolor($out_img_width, $out_img_height);
        $alpha = imagecolorallocatealpha($new_bg_imgage, 0, 0, 0, 127);
        imagefill($new_bg_imgage, 0, 0, $alpha);
        imageColorTransparent($new_bg_imgage, $alpha);
        imagecopyresampled($new_bg_imgage, $bg_imgage_resources, 0, 0, 0, 0, $out_img_width, $out_img_height, $bg_width, $bg_height);

        imagecopyresampled($cropped_image, $new_bg_imgage, 0, 0, 0, 0, $out_img_width, $out_img_height, $out_img_width, $out_img_height);

        //内容
        $white = imagecolorallocatealpha($new_bg_imgage, 0, 0, 0, 127);
        $grey = imagecolorallocate($cropped_image, 128, 128, 128);
        $black = imagecolorallocate($cropped_image, 255, 255, 255);
//        $text = '扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码';  //要写到图上的文字
        $font = \Yii::$app->basePath . '/web/wswh/hydsj.TTF';  //写的文字用到的字体。
        //$text = json_decode($text,true);
        $text_len = count($text);
        $text_str = '';
        $content_text_height = imageftbbox(24, 0, $font, $text[0]);
        for ($i = 0; $i < $text_len; $i++) {
            $text_str .= $text[$i] . PHP_EOL;
        }
//        $cut_text_x = $out_img_width/2-200;
        $cut_text_x = 40;
        $text_y = abs($content_text_height[7]) + abs($content_text_height[1]);
        $temp = (40 * $scale + $text_y * ($text_len + 1));
        if ($text_len <= 3) {
            $temp = (50 + $text_y * $text_len);
        }

        $cut_text_y = $out_img_height - $temp;
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $grey, $font, $text_str);
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $black, $font, $text_str);
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $white, $font, $text_str);

        //标题
        $title_text_height = imageftbbox(68, 0, $font, $title);
        $front_size = 58;
        if ($screenWidth >= 320) {
            $front_size = 62;
        }
        $cut_title_height = $cut_text_y - (abs($title_text_height[7] + abs($title_text_height[1])) + $text_y);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $grey, $font, $title);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $black, $font, $title);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $white, $font, $title);

        //header('Content-Type: image/jpeg');
        $outImgPath = \Yii::$app->basePath . '/web/wswh/img/dst_img_' . time() . '.png';
        imagesavealpha($cropped_image, true);
        imagepng($cropped_image, $outImgPath, 9);

        imagedestroy($source_image);
        imagedestroy($target_image);
        imagedestroy($cropped_image);
        return true;
    }

    /**
     * @param $client_id
     * @return null|QrcodeImg
     */
    public static function GetQrcodeImg($client_id)
    {
        return QrcodeImg::findOne(['client_id' => $client_id]);
    }
}