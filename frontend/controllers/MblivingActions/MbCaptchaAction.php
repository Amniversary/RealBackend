<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/17
 * Time: 10:26
 */

namespace frontend\controllers\MblivingActions;


use yii\captcha\CaptchaAction;
use yii\log\Logger;

class MbCaptchaAction extends CaptchaAction
{
    public function getVerifyCode($regenerate = true)
    {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }

        $session = \Yii::$app->getSession();
        $session->open();
        $name = $this->getSessionKey();
        if ($session[$name] === null || $regenerate) {
            $session[$name] = $this->generateVerifyCode();
            $session[$name . 'count'] = 1;
        }

        return $session[$name];
    }
    public function run()
    {
        \Yii::$app->session['pc_recharge']='1';//登录多一个标识
        $referer = \Yii::$app->request->headers->get('referer');
        $host = str_replace('http://','',$referer);
        $host = str_replace('https://','',$host);
        $host = explode('/',$host);
        $host = $host[0];
        $myHost = $_SERVER['HTTP_HOST'];
        if($host !== $myHost)
        {
           // exit;
        }
        //\Yii::getLogger()->log(var_export($referer,true),Logger::LEVEL_ERROR);
        return parent::run();
    }
} 