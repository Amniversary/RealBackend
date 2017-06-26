<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 16:58
 */

namespace backend\controllers\GoldsAccountActions;


use backend\business\GoodsUtil;
use backend\business\UserUtil;
use backend\components\ExitUtil;
use frontend\business\ClientUtil;

use frontend\business\RechargeListUtil;
use backend\models\GoldsAccountLogSearch;
use frontend\business\GoldsPrestoreUtil;
use frontend\business\GoldsAccountLogUtil;

use yii\base\Action;

class DetailAction extends Action
{
    public function run($gold_account_id,$user_id)
    {       
        if(!isset($gold_account_id)){
            ExitUtil::ExitWithMessage('充值id不能为空');
        }
        $searchModel = new GoldsAccountLogSearch(); 
        $params = \Yii::$app->request->queryParams;
        if(empty($params['GoldsAccountLogSearch']['create_time'])){
            $params['GoldsAccountLogSearch']['create_time'] = date('Y-m-d 00:00:00').'|'.date('Y-m-d H:i:s');
        }
        $dataProvider = $searchModel->search($params);
        $this->controller->layout='main_empty';
        return $this->controller->render('detail',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider
            ]
        );
    }
} 