<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午2:45
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class UpdateCarousels implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $url = '';
        if (isset($dataProtocol['data']['url'])) {
            $url = $dataProtocol['data']['url'];
        }
        $id = $dataProtocol['data']['id'];
        $Carousel = CarouselsUtil::GetCarousel($id);
        if (empty($Carousel)) {
            $error = '轮播图记录不存在';
            return false;
        }
        $Carousel->action_type = $dataProtocol['data']['action_type'];
        $Carousel->pic_url = $dataProtocol['data']['pic_url'];
        $Carousel->url = $url;
        $Carousel->description = $dataProtocol['data']['description'];
        $Carousel->status = $dataProtocol['data']['status'];
        $Carousel->update_time = date('Y-m-d H:i:s');
        if (!CarouselsUtil::SaveCarousel($Carousel, $error)) {
            return false;
        }
        \Yii::$app->cache->delete('carousels_info');
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $fields = ['id', 'pic_url', 'action_type;'];
        $fieldLabels = ['轮播图id', '图片url', '轮播图类型'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        if (!isset($dataProtocol['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}