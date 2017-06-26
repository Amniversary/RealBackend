<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-17
 * Time: 下午9:47
 */

namespace frontend\business;
use common\components\PhpLock;
use common\models\Carousel;
use yii\log\Logger;

class CarouselUtil
{
    /**
     * 保存轮播图记录
     * @param $carousel
     * @param $error
     */
    public static function SaveCarousel($carousel, &$error)
    {
        if(!($carousel instanceof Carousel))
        {
            $error = '不是轮播图记录';
            return false;
        }
        if(!$carousel->save())
        {
            $error = '轮播图记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($carousel->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 根据id查询记录
     * @param $carousel_id
     * @return null|static
     */
    public static function GetCarouselById($carousel_id)
    {
        return Carousel::findOne(['carousel_id'=>$carousel_id]);
    }

    /**
     * 获取所有轮播图信息
     * @param $status
     */
    public static function  GetCarouselList($status)
    {
        $carouselList = Carousel::find()->orderBy('order_no asc')->where([
            'status'=>$status
        ])->all();
        return $carouselList;
    }

    /**
     * 格式化轮播图记录
     * @param $recordList
     * @return bool
     */
    public static function GetFormateCarouselList($recordList)
    {
        $out = [];
        if(!isset($recordList) || empty($recordList))
        {
            return $out;
        }
        //换行符都用<br>替换，自己替换回去
        foreach($recordList as $model)
        {
            $dis = $model->discribtion;
            if(!empty($dis))
            {
                $dis = str_replace("\r",'',$dis);
                $dis = str_replace("\n",'<br>',$dis);
            }
            $ary = [
                'carousel_id'=>$model->carousel_id,
                'title'=>$model->title,
                'discribtion'=>$dis,
                'pic_url'=>$model->pic_url,
                'action_type'=>$model->action_type,
                'action_content'=>$model->action_content,
                'activity_type' => $model->activity_type
            ];
            $out[] = $ary;
        }

        return $out;

    }

    /**
     * 获取轮播图信息
     */
    public static function GetCarouselInfo($status,$reflash = false)
    {
        if($reflash)
        {
            $carousel = self::GetCarouselList($status);
            $rst = self::GetFormateCarouselList($carousel);
            $pStr = serialize($rst);
            \Yii::$app->cache->set('carousels_info',$pStr);
        }
        else
        {
            $cnt = \Yii::$app->cache->get('carousels_info');
            if(!isset($cnt))
            {
                $lock = new PhpLock('get_carousels');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('carousels_info');
                if(!isset($cnt))
                {
                    $carousel = self::GetCarouselList($status);
                    $rst = self::GetFormateCarouselList($carousel);
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('carousels_info',$pStr);
                }
                else
                {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            }
            else
            {
                $rst = unserialize($cnt);
            }
        }
        return $rst;
    }
} 