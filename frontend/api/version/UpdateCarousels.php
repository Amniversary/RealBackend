<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午2:45
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class UpdateCarousels implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)) {
            return false;
        }
        $url = '';
        if(isset($data['data']['url'])) {
            $url = $data['data']['url'];
        }
        $id = $data['data']['id'];
        $Carousel = CarouselsUtil::GetCarousel($id);
        if(empty($Carousel)) {
            $error = '轮播图记录不存在';
            return false;
        }
        $Carousel->pic_url = $data['data']['pic_url'];
        $Carousel->url = $url;
        $Carousel->description = $data['data']['description'];
        $Carousel->status = $data['data']['status'];
        $Carousel->update_time = date('Y-m-d H:i:s');
        if(!CarouselsUtil::SaveCarousel($Carousel, $error)) {
            return false;
        }
        \Yii::$app->cache->delete('carousels_info');
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocal,&$error){
        $fields = ['id', 'pic_url'];
        $fieldLabels = ['轮播图id', '图片url'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        if(!isset($dataProtocal['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}