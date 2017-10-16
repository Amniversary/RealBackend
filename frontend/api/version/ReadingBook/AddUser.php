<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 上午11:08
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\ClientUtil;
use frontend\components\DuobbComponent;

class AddUser implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) return false;
        $code = $dataProtocol['data']['code'];
        $rawData = json_decode($dataProtocol['data']['rawData'], true);
        $signature = $dataProtocol['data']['signature'];
        $encryptedData = $dataProtocol['data']['encryptedData'];
        $iv = $dataProtocol['data']['iv'];

        $Component = new DuobbComponent();
        $session_rst = $Component->getCodeBySessionKey($code);
        if(!$session_rst) {
            $error = '获取sessionKey 失败';
            return false;
        }
        //{"session_key":"g1svubS5f\\/uQvjPqkYJ2Dw==","expires_in":7200,"openid":"oNirq0FTxP1WARQmz2pzJq2nYbkc"}
        $open_id = $session_rst['openid'];
        $User = ClientUtil::getBookUserForUnionId($open_id);
        if (!isset($User) || empty($User)) {
            $data = [
                'union_id' => isset($session_rst['unionid']) ? $session_rst['unionid'] : '',
                'open_id' => $open_id,
                'nick_name' => $rawData['nickName'],
                'sex' => $rawData['gender'],
                'pic' => $rawData['avatarUrl'],
                'city' => isset($rawData['city']) ? $rawData['city'] : '',
                'province' => isset($rawData['province']) ? $rawData['province'] : '',
                'country' => isset($rawData['country']) ? $rawData['country'] : '',
                'remark1' => date('Y-m-d H:i:s')
            ];
            $model = ClientUtil::NewBookUserModel($data);
            if (!$model->save()) {
                $error = '保存用户授权信息失败';
                \Yii::error($error . ' ' . var_export($model->getErrors(), true));
                return false;
            }
            $User = $model;
        }

        $rstInfo = [
            'user_id' => intval($User->client_id),
            'nick_name' => $User->nick_name,
            'pic' => $User->pic,
            'sex' => $User->sex
        ];

        $rstData['code'] = 0;
        $rstData['data'] = $rstInfo;
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['code', 'rawData','signature', 'encryptedData', 'iv'];
        $filesLabel = ['登录凭证', '原始串', '签名串', '加密串', '初始向量'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}