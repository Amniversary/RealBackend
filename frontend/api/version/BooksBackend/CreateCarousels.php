<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午5:20
 */

namespace frontend\api\version\BooksBackend;


use common\models\Carousel;
use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class CreateCarousels implements IApiExecute
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
        $model = new Carousel();
        $model->pic_url = $dataProtocol['data']['pic_url'];
        $model->action_type = $dataProtocol['data']['type'];
        $model->description = $dataProtocol['data']['description'];
        $model->url = $url;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        if (!CarouselsUtil::SaveCarousel($model, $error)) {
            return false;
        }
        \Yii::$app->cache->delete('carousels_info');
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }


    private function check_params($dataProtocol, &$error)
    {
        $fields = ['pic_url', 'type'];
        $fieldLabels = ['图片url', '轮播图类型'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            //\Yii::error('datform:' . $dataProtocolProtocal['data'][$fields[$i]]);
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}