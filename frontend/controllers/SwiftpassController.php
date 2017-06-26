<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * WxpayController
 */
class SwiftpassController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['alipay_notify','weixin_notify'],
                'rules' => [
                    [
                        'actions' => ['alipay_notify','weixin_notify', 'tenpay_notify'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'alipay_notify' => ['post'],
                    'weixin_notify'=>['post'],
                    'tenpay_notify'=>['post'],
                ],
            ],
        ];
    }

    public function actionAlipay_notify()
    {
        $this->notify(20);
    }

    public function actionWeixin_notify()
    {
        $this->notify(21);
    }

    public function actionTenpay_notify()
    {
        $this->notify(22);
    }

    private function notify($type)
    {
        $request = new \common\components\swiftpass\Request();
        $callback = function($params) {
            $recharge = new \frontend\business\OtherPay\OtherPayResultKinds\SwiftpassOtherPayResultForRecharge();
            $error = null;
            $rst = $recharge->DoOtherPayResult($params, $error);
            if (!$rst) {
                \Yii::error('兴业银行支付回调错误: error ' . $error . ':' . var_export($params, true));
            }
            \Yii::error('兴业银行支付回调成功:' . var_export($params, true));
        };
        $request->callback($type, $callback);
    }
}
