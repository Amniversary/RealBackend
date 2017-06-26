<?php
namespace frontend\controllers;

use common\components\wxpay\WxAppPay;
use common\components\wxpay\WxNotifyUtil;
use Yii;
use yii\log\Logger;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayUnifiedOrder;
use common\components\wxpay\JsApiPay;

/**
 * ZhifupayController
 */
class ZhifupayController extends Controller
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
                'only' => ['zhifupay_notify','zhifupay_notify_app'],
                'rules' => [
                    [
                        'actions' => ['zhifupay_notify','zhifupay_notify_app'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'wxpay_notify' => ['post'],
                    'getapppayparams'=>['post','get'],
                    'wxpay_notify_app'=>['post'],
                ],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionZhifupay_notify()
    {
        set_time_limit(0);
        $str = var_export($_POST,true);
        \Yii::getLogger()->log('zhifupay:'.$str,Logger::LEVEL_ERROR);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionZhifupay_notify_app()
    {
        set_time_limit(0);

    }


}
