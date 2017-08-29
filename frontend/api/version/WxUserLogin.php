<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午2:27
 */

namespace frontend\api\version;


use backend\models\RealtechLoginSearch;
use frontend\api\IApiExecute;

class WxUserLogin implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!isset($data['data']['type'])) {
            $error = 'type类型, 不能为空';
            return false;
        }
        if (!isset($data['data']['RealtechLoginSearch']) ||
            empty($data['data']['RealtechLoginSearch'])
        ) {
            $error = '参数缺少 RealtechLoginSearch';
            return false;
        }
        $type = $data['data']['type'];
        $post = $data['data']['RealtechLoginSearch'];
        $model = new RealtechLoginSearch();
        if (!$model->load($post)) {
            \Yii::error('post::' . var_export($post, true));
            $error = '无法加载对象数据';
            return false;
        }
        $model->type = $type;
        if (!$model->validatePassword($error)) {
            return false;
        }
        $token = md5($model->username . '123123123');
        \Yii::$app->cache->set($token, $model->username, 60 * 60 * 12);
        $rstData['code'] = 0;
        $rstData['data'] = [
            'username' => $model->username,
            'token' => $token
        ];
        return true;
    }
}