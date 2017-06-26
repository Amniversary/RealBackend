<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 10:08
 */

namespace backend\controllers\ScoreGiftActions;


use common\models\ActivityGiftscore;
use yii\base\Action;
/**
 * 新增活动
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        $model = new ActivityGiftscore();
        $this->controller->getView()->title = '新增活动';
        if ( $model->load(\Yii::$app->request->post()) )
        {
            //获得保存时的状态，状态为已结束时，把时间设置成当日按日期的前一天
            if($model['activity_status'] == "0"){
                $date = date("Y-m-d",strtotime("-1 day"));
                $model['end_time'] = $date;
            }

            if($model->save())
            {
                return $this->controller->redirect('index');
            }
        }
        else
        {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}