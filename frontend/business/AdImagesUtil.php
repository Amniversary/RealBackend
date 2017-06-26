<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 16:31
 */

namespace frontend\business;

use common\models\AdImages;
use common\models\UserAdImages;
use yii\db\Query;

/**
 * 弹窗广告业务Util
 * Class AdImagesUtil
 * @package frontend\business
 */
class AdImagesUtil {
    /**
     * 通过ad_id获取获取mb_ad_images表单个记录
     * @param $ad_id
     * @return null|static
     */
    public static function GetAdImagesById($ad_id)
    {
        return AdImages::findOne(['ad_id' => $ad_id]);
    }

    /**
     * 通过获取ad_id获取mb_user_ad_images表单个记录
     * @param $user_id
     * @return null|static
     */
    public static function GetUserAdImagesById($user_id)
    {
        return UserAdImages::findOne(['user_id' => $user_id]);
    }

    /**
     * 获取mb_user_ad_images表列表
     * @param $user_id
     * @return array
     */
    public static function GetUserAdImagesList($user_id)
    {
        $query = (new Query())
            ->select(['u_id','user_id','ad_id'])
            ->from('mb_user_ad_images')
            ->where('user_id=:uid',[':uid' => $user_id])
            ->all();
        return $query;
    }

    /**
     * 获取弹窗广告列表
     * @param $ad_ids
     * @return array
     */
    public static function GetImagesList($ad_ids)
    {
        $query = (new Query())
            ->select(['ad_id','description','link_url','image_url','weights','start_time','end_time'])
            ->from('mb_ad_images')
            ->where('status=1')
            ->andWhere(['not in','ad_id',$ad_ids])
            ->orderBy(['weights' => SORT_DESC])
            ->all();

        return $query;
    }

} 