<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午2:22
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\Carousel;
use yii\db\Query;

class CarouselsUtil
{
    /**
     * 获取轮播图信息
     */
    public static function GetCarouselInfo($reflash = false)
    {
        if ($reflash) {
            $carousel = self::GetCarouselList();
            $rst = self::GetFormateCarouselList($carousel);
            $pStr = serialize($rst);
            \Yii::$app->cache->set('carousels_info', $pStr);
        } else {
            $cnt = \Yii::$app->cache->get('carousels_info');
            if ($cnt == false) {
                $lock = new PhpLock('get_carousels');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('carousels_info');
                if ($cnt == false) {
                    $carousel = self::GetCarouselList();
                    $rst = self::GetFormateCarouselList($carousel);
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('carousels_info', $pStr);
                } else {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            } else {
                $rst = unserialize($cnt);
            }
        }
        return $rst;
    }

    /**
     * 获取所有轮播图信息
     */
    public static function GetCarouselList()
    {
        return Carousel::find()->orderBy('order_no asc')->all();
    }

    /**
     * 格式化轮播图记录
     * @param $recordList
     * @return bool
     */
    public static function GetFormateCarouselList($recordList)
    {
        $out = [];
        if (!isset($recordList) || empty($recordList)) {
            return $out;
        }
        //换行符都用<br>替换，自己替换回去
        foreach ($recordList as $model) {
            /*$dis = $model->discribtion;
            if(!empty($dis))
            {
                $dis = str_replace("\r",'',$dis);
                $dis = str_replace("\n",'<br>',$dis);
            }*/
            $ary = [
                'id' => $model->carousel_id,
                'title' => $model->title,
                'pic_url' => $model->pic_url,
                'type' => $model->action_type,
                'description' => $model->description,
                'url' => $model->url,
                'status' => $model->status,
                'create_time' => $model->create_time,
                'update_time' => $model->update_time
            ];
            $out[] = $ary;
        }

        return $out;

    }

    /**
     * 保存轮播图记录
     */
    public static function SaveCarousel($carousel, &$error)
    {
        if (!($carousel instanceof Carousel)) {
            $error = '不是轮播图记录';
            return false;
        }
        if (!$carousel->save()) {
            $error = '轮播图记录保存失败';
            \Yii::error($error . ' :' . var_export($carousel->getErrors(), true));
            return false;
        }
        return true;
    }

    /**
     * 根据轮播图id 获取轮播图记录
     * @param $id
     * @return null|Carousel
     */
    public static function GetCarousel($id)
    {
        return Carousel::findOne(['carousel_id' => $id]);
    }


    /**
     * 获取轮播图信息
     */
    public static function GetWebCarouselInfo($reflash = false)
    {
        if ($reflash) {
            $carousel = self::GetCarousels();
            $rst = self::GetFormatCarousels($carousel);
            $pStr = serialize($rst);
            \Yii::$app->cache->set('web_carousels_info', $pStr);
        } else {
            $cnt = \Yii::$app->cache->get('web_carousels_info');
            if ($cnt == false) {
                $lock = new PhpLock('get_web_carousels');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('web_carousels_info');
                if ($cnt == false) {
                    $carousel = self::GetCarouselList();
                    $rst = self::GetFormateCarouselList($carousel);
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('web_carousels_info', $pStr);
                } else {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            } else {
                $rst = unserialize($cnt);
            }
        }
        return $rst;
    }


    public static function GetCarousels()
    {
        return Carousel::find()->where(['action_type' => 1, 'status' => 1])->orderBy('order_no asc')->all();
    }

    public static function GetFormatCarousels($recordList)
    {
        $out = [];
        if (!isset($recordList) || empty($recordList)) {
            return $out;
        }
        foreach ($recordList as $model) {
            $ary = [
                'id' => $model->carousel_id,
                'pic_url' => $model->pic_url,
                'url' => $model->url,
                'description' => $model->description,
            ];
            $out['list'][] = $ary;
        }
        $out['qrcode_url'] = '';
        return $out;
    }

    public static function GetWebCarousels($Params)
    {
        $ArticleParams = self::GetArticleParams($Params);
        if(empty($ArticleParams)) {
            $carousel = self::GetCarousels();
            $rst = self::GetFormatCarousels($carousel);
            return $rst;
        }

        $carousel = ArticlesUtil::GetArticleMenu($ArticleParams['id']);
        $rst['qrcode_url'] = $ArticleParams['qrcode_url'];
        foreach($carousel as $item) {
            $rst['list'][] = [
                'id' => $item['carousel_id'],
                'pic_url' => $item['pic_url'],
                'url' => $item['url'],
                'description' => $item['description'],
            ];
        }
        return $rst;
    }

    public static function GetArticleParams($book_id)
    {
        $params = sprintf('select system_id from wc_book_params_menu where book_id = %s', $book_id);
        $condition = 'id = ('.$params.')';
        $query = (new Query())
            ->select(['id', 'qrcode_url'])
            ->from('wc_article_system_params')
            ->where($condition)
            ->one();

        return $query;
    }

    public static function getBookCarousel()
    {
        $recordList = Carousel::find()->where(['action_type' => 3, 'status' => 1])->orderBy('order_no asc')->all();
        $out = [];
        if (!isset($recordList) || empty($recordList)) {
            return $out;
        }
        foreach ($recordList as $model) {
            $ary = [
                'id' => $model->carousel_id,
                'pic_url' => $model->pic_url,
                'url' => $model->url,
                'description' => $model->description,
            ];
            $out[] = $ary;
        }
        return $out;
    }
}