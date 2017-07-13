<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 上午11:15
 */

namespace backend\controllers;


use common\components\UsualFunForStringHelper;
use Qiniu\Auth;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class MypicController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return require (__DIR__.'/MyPicActions/MyPicActionsConfig.php');
    }


    /*public function behaviors()
    {
        return require (__DIR__.'/MyPicActions/MyPicBehaviors.php');
    }*/

    public function behaviors()
    {
        return ArrayHelper::merge([
            [
                'class' => Cors::className(),
                'cors'=>[
                    'Origin'=>['*'],
                    'Access-Control-Request-Method'=>['GET','POST','HEAD','OPTIONS'],

                ]
            ],
        ], parent::behaviors());
    }

    public function actionGettoken()
    {
        $params = \Yii::$app->params['QiNiuOss'];
        $auth = new Auth($params['ak'],$params['sk']);
        $bucket = $params['bucket'];
        $token = $auth->uploadToken($bucket);
        $rst = ['token'=>$token];
        echo json_encode($rst);
    }

    public function actionGetsign()
    {
        $str = '0123456789`~!@#$%^&*()_+=-|qwertyuiopasdfghjklzxcvbnmMNBVCXZLKJHGFDSAPOIUYTREWQ';
        $rand_str = UsualFunForStringHelper::mt_rand_str(32,$str);
        $time = time();
        $token = sha1(UsualFunForStringHelper::CreateGUID());
        $params = [
            'rand_str'=>$rand_str,
            'time'=>$time,
            'token'=>$token
        ];

        return $params;
    }
}