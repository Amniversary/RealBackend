<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\alipay\AlipayUtil;

/**
 * Site controller
 */
class AlipayController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['alipay_notify'],
                'rules' => [
                    [
                        'actions' => ['alipay_notify'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'alipay_notify' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionAlipay_notify()
    {
        set_time_limit(0);
        $rst = AlipayUtil::DealNotify();
        echo $rst;
    }
}
