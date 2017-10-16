<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午2:39
 */
namespace frontend\api\version\BooksBackend;


use common\models\Carousel;
use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class DelCarousels implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!isset($dataProtocol['data']['id']) || empty($dataProtocol['data']['id'])) {
            $error = '轮播图 id 不能为空';
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $Carousels = CarouselsUtil::GetCarousel($id);
        if (empty($Carousels) || !isset($Carousels)) {
            $error = '轮播图记录不存在';
            return false;
        }

        Carousel::deleteAll(['carousel_id' => $id]);
        \Yii::$app->cache->delete('carousels_info');
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }
}