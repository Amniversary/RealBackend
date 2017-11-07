<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/19
 * Time: 下午5:08
 */

namespace frontend\controllers;


use frontend\components\ChatTunnelHandler;
use frontend\components\ParseRequest;
use QCloud_WeApp_SDK\Auth\LoginService;
use QCloud_WeApp_SDK\Tunnel\TunnelService;
use yii\web\Controller;

class WaferController extends Controller
{
    /**
     * 小程序用户登录接口
     *  /wafer/login
     */
    public function actionLogin()
    {
        //TODO: wafer 用户登录 得到用户 UserInfo
        $result = LoginService::login();
        if($result['code'] == 0) {
            //TODO: 相关业务逻辑处理
        }
    }

    /**
     * 小程序获取用户信息接口
     *  /wafer/user
     */
    public function actionUser()
    {
        //TODO: 会话服务 检测用户
        $result = LoginService::check();
        echo json_encode($result,JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * 小程序信道服务接口
     *  /wafer/tunnel
     */
    public function actionTunnel()
    {
        $handler = new ChatTunnelHandler();
        TunnelService::handle($handler, ['checkLogin' => true]);
    }

}