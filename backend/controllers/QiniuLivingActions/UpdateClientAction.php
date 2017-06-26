<?php

namespace backend\controllers\QiniuLivingActions;


use backend\business\GoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use backend\models\ParametersMore;
use frontend\business\ClientQiNiuUtil;
use yii\base\Action;
/**
 * 更新用户参数信息
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class UpdateClientAction extends Action
{
    public function run($relate_id)
    {
        $model = ClientQiNiuUtil::GetClientLivingParams($relate_id);
        //从模型的关联表里面取出主播ID 传给 编辑页面
        $client_no = ClientQiNiuUtil::GetClientNoLivingParams($relate_id);

        //判断从模型里面拿到值存在且不为空
        if((!isset($model)|| empty($model)) && (!isset($client_no) || empty($client_no)))
        {
            ExitUtil::ExitWithMessage('参数信息不存在');
        }

        //$model 是数据库的单个行，parameters_more为里面的字段（因为字段是JSON数据 这里转化为数组）
        if(!isset($model->parameters_more) || empty($model->parameters_more)){
            ExitUtil::ExitWithMessage('parameters_more 未设置');
        }else{
            $data_params = json_decode($model->parameters_more,true);


            //对$data_params进行遍历取值，主要是为了在ParametersMore 进行验证
            foreach($data_params as $data_v){
                $model_params = new ParametersMore();
                $model_params->fps = $data_v['fps'];
                $model_params->height = $data_v['height'];
                $model_params->video_bit_rate = $data_v['video_bit_rate'];
                $model_params->order_no = $data_v['order_no'];
                $model_params->quality = $data_v['quality'];
                $model_params->width = $data_v['width'];
                $model_params->profilelevel = $data_v['profilelevel'];

                $model_par[] = $model_params;
            }
        }





        if (\Yii::$app->request->post('ParametersMore'))
        {
            //将拿到的数据进行排序
            $arrUsers = \Yii::$app->request->post('ParametersMore');
            $new_arr = [];
            foreach($arrUsers as $key =>$val){
                $new_arr[$val['order_no']] = $val;
            }
            sort($new_arr);
//            print_r('<pre>');
//            print_r($new_arr);


            //将排序过后的数据保存到数据库
            $model -> parameters_more = json_encode($new_arr);
            if($model-> save())
            {
                return $this->controller->redirect(['client_params']);
            }
        }
        else
        {
            return $this->controller->render('update_client', [
                'client_no' => $client_no,
                'model' => $model_par,
            ]);
        }
    }
} 