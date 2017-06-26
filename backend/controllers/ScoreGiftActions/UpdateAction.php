<?php
namespace backend\controllers\ScoreGiftActions;


use backend\business\ScoreGiftUtil;
use backend\components\ExitUtil;
use yii\base\Action;



class UpdateAction extends Action
{
    public function run($activity_id,$page)
    {
        $model = ScoreGiftUtil::GetScoreGiftById($activity_id);

        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('参数不存在');
        }


        if ($model->load(\Yii::$app->request->post()) )
        {
            //获得保存时的状态，状态为已结束时，把时间设置成当日按日期的前一天
            if($model['activity_status'] == "0"){
                $date = date("Y-m-d",strtotime("-1 day"));
                $model['end_time'] = $date;
            }

            if($model->save())
            {
                \yii::$app->cache->delete('get_score_activity');
                return $this->controller->redirect(['index?page='.$page]);
            }
        }
        else
        {
            return $this->controller->render('update', [
                'model' => $model,
            ]);
        }
    }
}