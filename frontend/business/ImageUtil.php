<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/8/17
 * Time: 10:00
 */

namespace frontend\business;


use yii\log\Logger;

class ImageUtil
{
    /**
     * @param $source_path               源图片地址
     * @param $bg_img_path               背景图片地址
     * @param int $out_img_width         输出图片宽度
     * @param int $out_img_height        输出图片高度
     * @param int $cut_dst_x             裁剪图片 X 轴
     * @param int $cut_dst_y             裁剪图片 Y 轴
     * @param int $scale                 缩放比例
     * @param string $error              错误信息
     */
    public static function imagecropper($source_path,$bg_img_path,$text = [],$title,&$outImgPath,&$error,$screenWidth = 320,$out_img_width = 752,$out_img_height=1184,$cut_dst_x = 1290,$cut_dst_y = 888,$scale = 14)
    {
        if(!function_exists('gd_info'))
        {
            $error = '请开启GD库扩展';
            return false;
        }

        @ini_set('memory_limit', '2048M');

        $source_info   = getimagesize($source_path);   //源文件图片地址
        $source_width  = $source_info[0];
        $source_height = $source_info[1];
        $source_mime   = $source_info['mime'];

        $target_width  = intval($source_width*$scale);        //缩放比例
        $target_height = intval($source_height*$scale);

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

        $bg_info   = getimagesize($bg_img_path);
        $bg_width  = $bg_info[0];
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
       
        $target_image  = imagecreatetruecolor($target_width, $target_height);
        $cropped_image = imagecreatetruecolor($out_img_width, $out_img_height);
        $alpha = imagecolorallocatealpha($cropped_image, 0, 0, 0, 127);
        imagefill($cropped_image, 0, 0, $alpha);
        imageColorTransparent($cropped_image,$alpha);

        imagecopyresampled($target_image, $source_image, 0, 0, 0, 0, $target_width, $target_height, $source_width, $source_height);   //源图裁剪为目标图片大小
        imagecopy($cropped_image, $target_image, 0, 0, $cut_dst_x, $cut_dst_y, $out_img_width, $out_img_height);    // 将裁剪后的图片缩放
        /**将输出图片设为透明**/
        $new_bg_imgage = imagecreatetruecolor($out_img_width, $out_img_height);
        $alpha = imagecolorallocatealpha($new_bg_imgage, 0, 0, 0, 127);
        imagefill($new_bg_imgage, 0, 0, $alpha);
        imageColorTransparent($new_bg_imgage,$alpha);
        imagecopyresampled($new_bg_imgage, $bg_imgage_resources, 0, 0, 0, 0, $out_img_width, $out_img_height, $bg_width, $bg_height);

        imagecopyresampled($cropped_image,$new_bg_imgage, 0, 0, 0, 0, $out_img_width, $out_img_height, $out_img_width, $out_img_height);

        //内容
        $white = imagecolorallocatealpha($new_bg_imgage, 0, 0, 0, 127);
        $grey = imagecolorallocate($cropped_image, 128, 128, 128);
        $black = imagecolorallocate($cropped_image, 255, 255, 255);
//        $text = '扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码扫描二维码';  //要写到图上的文字
        $font = './mibo/wswh/hydsj.TTF';  //写的文字用到的字体。
        $text = json_decode($text,true);
        $text_len = count($text);
        $text_str = '';
        $content_text_height = imageftbbox(24,0,$font,$text[0]);
        for($i=0;$i<$text_len;$i++)
        {
            $text_str .= $text[$i].PHP_EOL;
        }
//        $cut_text_x = $out_img_width/2-200;
        $cut_text_x = 40;
        $text_y = abs($content_text_height[7])+abs($content_text_height[1]);
        $temp = (40*$scale+$text_y*($text_len+1));
        if($text_len <= 3)
        {
            $temp = (50+$text_y*$text_len);
        }

        $cut_text_y = $out_img_height-$temp;
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $grey, $font, $text_str);
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $black, $font, $text_str);
        imagettftext($cropped_image, 24, 0, $cut_text_x, $cut_text_y, $white, $font, $text_str);

        //标题
        $title_text_height = imageftbbox(68,0,$font,$title);
        $front_size = 58;
        if($screenWidth >= 320)
        {
            $front_size = 62;
        }
        $cut_title_height = $cut_text_y-(abs($title_text_height[7]+abs($title_text_height[1]))+$text_y);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $grey, $font, $title);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $black, $font, $title);
        imagettftext($cropped_image, $front_size, 0, $cut_text_x, $cut_title_height, $white, $font, $title);

//        header('Content-Type: image/jpeg');
        $outImgPath = './mibo/wswh/img/dst_img_'.time().'.png';
        imagesavealpha($cropped_image, true);
        imagepng($cropped_image,$outImgPath,9);

        imagedestroy($source_image);
        imagedestroy($target_image);
        imagedestroy($cropped_image);
        return true;
    }

}