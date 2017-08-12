<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午5:20
 */

namespace frontend\api\version;


use common\models\Carousel;
use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class CreateCarousels implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData= [])
    {
        \Yii::error('data:'.var_export($data,true));
        if(!$this->check_params($data, $error)) {
            return false;
        }
        $url = '';
        if(isset($data['data']['url'])) {
            $url = $data['data']['url'];
        }
        $model = new Carousel();
        $model->pic_url = $data['data']['pic_url'];
        $model->action_type = $data['data']['type'];
        $model->url = $url;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        $model->order_no = 50;
        if(!CarouselsUtil::SaveCarousel($model,$error)) {
            return false;
        }
        \Yii::$app->cache->delete('carousels_info');
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }


    private function check_params($dataProtocal,&$error){
        $fields = ['pic_url','type'];
        $fieldLabels = ['图片url', '轮播图类型'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            \Yii::error('datform:'.$dataProtocal['data'][$fields[$i]]);
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}