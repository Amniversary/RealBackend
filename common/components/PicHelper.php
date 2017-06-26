<?php
/**
 * 图片处理函数
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午7:05
 */

namespace common\components;


use yii\log\Logger;

class PicHelper
{
    /**
     * 取得文件扩展
     *
     * @param $filename 文件名
     * @return 扩展名
     */
    public static function FileExt($filename)
    {
        if(empty($filename))
        {
            return '';
        }
        $items = explode('/',$filename);
        $last = $items[count($items) -1];
        return strtolower(trim(substr(strrchr($last, '.'), 1, 10)));
    }

    /**
     * @param $mine_type
     */
    public static function GetPicTypeFromMine($mine_type)
    {
        /**
        'application/octet-stream',
        'image/bmp',
        'image/gif',
        'image/jpeg',
        'image/png'
         */
        switch(strval($mine_type))
        {
            case 'image/bmp':
                $rst = 'bmp';
                break;
            case 'image/gif':
                $rst = 'gif';
                break;
            case 'image/jpeg':
                $rst = 'jpg';
                break;
            case 'image/png':
                $rst = 'png';
                break;
            default:
                $rst = false;
                break;
        }
        if(empty($rst))
        {
            return false;
        }
        return $rst;
    }

    /**
     * 保存网络图片
     * @param $url
     * @param $file
     * @param $error
     */
    public static function SavePicFromWeb($url,&$fileName,&$error)
    {
        if(empty($url))
        {
            $error = '图片链接为空';
            return false;
        }
        $data = UsualFunForNetWorkHelper::HttpGetImg($url,$cnt_type,$error);
        if($data === false)
        {
            //$error = '无法访问的图片或不是图片';
            return false;
        }
        $type = self::FileExt($url);
        if(empty($type))
        {
            $type = self::GetPicTypeFromMine($cnt_type);
            if($type ===false)
            {
                $error = '图片格式不合法';
                \Yii::getLogger()->log($error.' :'.$cnt_type,Logger::LEVEL_ERROR);
                return false;
            }
        }
        $name = uniqid('web_pic_').'.'.$type;
        $fileName = __DIR__.'/../tmppic/'.$name;
        $len = file_put_contents($fileName,$data);
        if($len <= 0)
        {
            \Yii::getLogger()->log('图片保存失败,'.$fileName,Logger::LEVEL_ERROR);
        }
        return true;
    }
    /**
     * @param $src_img
     * @param $dst_imgs
     * @param array $widths 多个规格宽度数组
     * @param array $heights 多个规格高度数据
     * @param array $cuts  多个规格裁剪参数数组 默认 0
     * @param array $proportions 多个规格缩放参数数组 默认 0
     */
    public static function img2ThumbMuilt($src_img,&$dst_imgs,$widths,$heights,$cuts=[],$proportions= [],&$error)
    {
        $error= '';
        $dst_imgs = [];
        if(!is_array($widths) || !is_array($heights))
        {
            $error = '宽度和高度参数不符号要求';
            return false;
        }
        $lenW = count($widths);
        $lenH = count($heights);
        if($lenW !== $lenH)
        {
            $error = '长度和高度个数不相等';
            return false;
        }
        if(!is_array($cuts) || !is_array($proportions))
        {
            $error = '裁剪参数或缩小参数异常';
            return false;
        }
        $lenC = count($cuts);
        if($lenC !=0 && $lenW !== $lenC)
        {
            $error = '裁剪参数异常';
            return false;
        }
        $lenP = count($proportions);
        if($lenP != 0 && $lenW !== $lenP)
        {
            $error = '缩放参数异常';
            return false;
        }
        if($lenC === 0)
        {
            for($i =0; $i < $lenW; $i++)
            {
                $cuts[$i]  = 0;
            }
        }
        if($lenP === 0)
        {
            for($i =0; $i < $lenW; $i ++)
            {
                $proportions[$i] = 0;
            }
        }
        if(!file_exists($src_img))
        {
            $error = '原始图片路径错误';
            \Yii::getLogger()->log($error.':'.$src_img,Logger::LEVEL_ERROR);
            return false;
        }
        $ext = self::fileext($src_img);
        if(empty($ext))
        {
            $error = '图片没有扩张名';
            return false;
        }

        $path = __DIR__.'/../tmppic/';
        $thumbLen = count($widths);
        for($i =0; $i < $thumbLen; $i ++)
        {
            $dst_file = $path.uniqid('pic_thumb_').'.'.$ext;
            if(self::img2Thumb($src_img,$dst_file,$widths[$i],$heights[$i],$cuts[$i],$proportions[$i]))
            {
                $dst_imgs[] = $dst_file;
            }
            else
            {
                $error = '缩略图 w：'.strval($widths[$i]).' h:'.$heights[$i].'生成失败';
                return false;
            }
        }
        return true;
    }
    /**
     * 生成缩略图
     * @param string     源图绝对完整地址{带文件名及后缀名}
     * @param string     目标图绝对完整地址{带文件名及后缀名}
     * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
     * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
     * @param int        是否裁切{宽,高必须非0}
     * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
     * @return boolean
     */
    public static function img2Thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
    {
        if(!is_file($src_img))
        {
            return false;
        }
        //echo 'cccccc'."\n";
        $ot = self::fileext($dst_img);
        $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
        $srcinfo = getimagesize($src_img);
        if($srcinfo === false)
        {
            return false;
        }
        $src_w = $srcinfo[0];
        $src_h = $srcinfo[1];
        $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
        if($type == 'bmp')
        {
            return false;
        }
        $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
        $dst_h = $height;
        $dst_w = $width;
        $x = $y = 0;

        /**
         * 缩略图不超过源图尺寸（前提是宽或高只有一个）
         */
        if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
        {
            $proportion = 1;
        }
        if($width> $src_w)
        {
            $dst_w = $width = $src_w;
        }
        if($height> $src_h)
        {
            $dst_h = $height = $src_h;
        }
        if(!$width && !$height && !$proportion)
        {
            return false;
        }
        if(!$proportion)
        {
            if($cut == 0)
            {
                if($dst_w && $dst_h)
                {
                    if($dst_w/$src_w> $dst_h/$src_h)
                    {
                        $dst_w = $src_w * ($dst_h / $src_h);
                        $x = 0 - ($dst_w - $width) / 2;
                    }
                    else
                    {
                        $dst_h = $src_h * ($dst_w / $src_w);
                        $y = 0 - ($dst_h - $height) / 2;
                    }
                }
                else if($dst_w xor $dst_h)
                {
                    if($dst_w && !$dst_h)  //有宽无高
                    {
                        $propor = $dst_w / $src_w;
                        $height = $dst_h  = $src_h * $propor;
                    }
                    else if(!$dst_w && $dst_h)  //有高无宽
                    {
                        $propor = $dst_h / $src_h;
                        $width  = $dst_w = $src_w * $propor;
                    }
                }
            }
            else
            {
                if(!$dst_h)  //裁剪时无高
                {
                    $height = $dst_h = $dst_w;
                }
                if(!$dst_w)  //裁剪时无宽
                {
                    $width = $dst_w = $dst_h;
                }
                $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
                $dst_w = (int)round($src_w * $propor);
                $dst_h = (int)round($src_h * $propor);
                $x = ($width - $dst_w) / 2;
                $y = ($height - $dst_h) / 2;
            }
        }
        else
        {
            $proportion = min($proportion, 1);
            $height = $dst_h = $src_h * $proportion;
            $width  = $dst_w = $src_w * $proportion;
        }

        $src = $createfun($src_img);
        $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        //echo '11111'."\n";
        if(function_exists('imagecopyresampled'))
        {
            imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        else
        {
            imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
        }
        //echo '222222'."\n";
        $otfunc($dst, $dst_img);
        imagedestroy($dst);
        imagedestroy($src);
        return true;
    }


    /**
     * 保存上传的图片，返回图片信息
     * @param $controlName file控件的name值，
     * 如果name的格式是model[attribute]，则这里填写model名称，
     * 如WebItem[menu_icon]，则这里填写WebItem
     * @param string $error 错误时返回错误字符串信息
     * @param array picInfo
     * @param string $attribute，如片控件属性，如果file控件以model[attribute]方式命名，需要传入属性，否则可以为空
     * @return bool
     */
    public static  function SavePicUpload($controlName, $attribute='',&$picInfo,&$error)
    {
        $picInfo = [];
        $error = '';
        if(empty($controlName) ||!isset($_FILES) || !isset($_FILES[$controlName]))
        {
            $error = '未上传图片！';
            return false;
        }
        $picName = $_FILES[$controlName]['name'];
        if(is_array($picName))//如果是数组则控件name方式为model[attribute]，必须以属性为键才能取到值
        {
            if(!empty($attribute))
            {
                $picName = $picName[$attribute];
            }
        }

        if (!empty($picName)) //有文件上传
        {

            $path = "upload";
            $realPath = \Yii::$app->getBasePath().'/web/'.$path;
            if(!file_exists($realPath))
            {
                mkdir($realPath);
            }
            $path .= '/';
            $realPath .= '/';
            $types = array('pjpeg','jpeg','png');
            $typeName = $_FILES[$controlName]['type'];

            if(is_array($typeName))//如果是数组则控件name方式为model[attribute]，必须以属性为键才能取到值
            {
                if (!empty($typeName)) {
                    $typeName = $typeName[$attribute];
                }
            }
            $typeName = substr($typeName, 6);
            $picTmpPath = $_FILES[$controlName]['tmp_name'];
            if(is_array($picTmpPath))//如果是数组则控件name方式为model[attribute]，必须以属性为键才能取到值
            {
                if (!empty($attribute)) {
                    $picTmpPath = $picTmpPath[$attribute];
                }
            }

            if(!in_array($typeName, $types))
            {
                $error = '图片格式不正确'; //格式不符合
                unlink($picTmpPath);
                return false;
            }

            $imagesize = getimagesize($picTmpPath);


            $size = $_FILES[$controlName]['size'];
            if(!empty($attribute))
            {
                $size = $size[$attribute];
            }
            $minSize = 0;
            $maxSize = 500 * 1024; //byte

            $picsize = $size;
            //$size = floor($size/1024);//K
            if($size > $maxSize)
            {
                $error = '图片超过'. intval(strval($maxSize/1024.0)).'K';
                unlink($picTmpPath);
                return false;
            }

            $picNameOnly = uniqid('', true).'.jpg';//图片名称
            $path =$path.$picNameOnly;
            $realPath = $realPath.$picNameOnly;//物理路径
            \Yii::getLogger()->log('pth:'.$realPath, Logger::LEVEL_ERROR);
            $flag = move_uploaded_file($picTmpPath, $realPath);
            if(!$flag)
            {
                $error = '保存文件失败！';
                //unlink($_FILES[$controlName]['tmp_name']);
                return false;
            }
            $type = substr($picNameOnly, strrpos($picNameOnly, '.') + 1);
            $picUrl = 'http://'.$_SERVER['HTTP_HOST'].'/'. $path;
            $picInfo =array(
                'pic_name'=>$picNameOnly,//图片名称
                'http_pic_url'=>$picUrl,//图片url
                'pic_real_path'=>$realPath,//图片物理路径'type'=>'jpg',
                'pic_relate_url'=>'/'.$path,
                'size'=>$picsize,//图片大小
                'width'=>$imagesize[0],//图片宽度
                'height'=>$imagesize[1],//图片高度
                'type'=>$type, //文件类型
            );
            return true;
        }
        else
        {
            $error = '请上传图片  ';
        }
        return false;
    }


} 