<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/26
 * Time: 上午10:42
 */

namespace frontend\components;


use common\components\UsualFunForNetWorkHelper;

class AuthComponent
{
    /**
     * 描述：登录校验，返回id和skey
     * @param $code
     * @param $appid
     * @param $secret
     * @return array|int
     */
    public function get_id_skey($code, $encrypt_data, $appid, $iv = "old")
    {
        $cappinfo_data = $this->get_cappinfo($appid);
        $ret['appid'] = !isset($cappinfo_data['appid']) ? '' : $cappinfo_data['appid'];
        $ret['secret'] = !isset($cappinfo_data['secret']) ? '' : $cappinfo_data['secret'];
        $ret['returnData'] = '';
        if (empty($cappinfo_data) || ($cappinfo_data == false)) {
            $ret['returnCode'] = ReturnCode::MA_NO_APPID;
            $ret['returnMessage'] = 'NO_APPID';
            return $ret;
        }
        $id = $cappinfo_data['id'];
        $appid = $cappinfo_data['appid'];
        $secret = $cappinfo_data['secret'];
        $login_duration = $cappinfo_data['login_duration'];
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $code . '&grant_type=authorization_code';
        $json_rst = UsualFunForNetWorkHelper::http_get($url);
        if (!$json_rst) {
            $ret['returnCode'] = ReturnCode::MA_WEIXIN_NET_ERR;
            $ret['returnMessage'] = 'WEIXIN_NET_ERR';
            return $ret;
        }
        $json_message = json_decode($json_rst,true);
        if (isset($json_message['errcode']) && isset($json_message['errmsg'])) {
            $ret['returnCode'] = ReturnCode::MA_WEIXIN_CODE_ERR;
            $ret['returnMessage'] = 'WEIXIN_CODE_ERR';
            return $ret;
        }
        //TODO:  初始化用户解密信息
        if (isset($json_message['openid']) && isset($json_message['session_key']) && isset($json_message['expires_in'])) {
            $uuid = md5((time() - mt_rand(1, 10000)) . mt_rand(1, 1000000));//TODO: 生成UUID
            $skey = md5(time() . mt_rand(1, 1000000));// TODO:生成 skey
            $create_time = date('Y-m-d H:i:s', time());   //TODO: 初始化用户创建时间
            $last_visit_time = date('Y-m-d H:i:s', time());  //TODO: 最后登录时间
            $openid = $json_message['openid'];
            $session_key = $json_message['session_key'];
            $errCode = 0;
            $user_info = false;
            //TODO : 兼容旧的解密算法
            if ($iv == "old") {
                $user_info = $this->aes128cbc_Decrypt($encrypt_data, $session_key);
                $user_info = base64_encode($user_info);
            } else {
                $pc = new DecryptComponent($appid, $session_key);
                $errCode = $pc->decryptData($encrypt_data, $iv, $user_info);
                $user_info = base64_encode($user_info);
            }
            if ($user_info === false || $errCode !== 0) {
                $ret['returnCode'] = ReturnCode::MA_DECRYPT_ERR;
                $ret['returnMessage'] = 'DECRYPT_FAIL';
                return $ret;
            }

            $params = [
                'id'=> $id,     //TODO:  微信id
                'uuid' => $uuid,
                'skey' => $skey,
                'create_time' => $create_time,
                'last_visit_time' => $last_visit_time,
                'openid' => $openid,
                'session_key' => $session_key,
                'user_info' => $user_info,
                'login_duration' => $login_duration
            ];
            //TODO: 注册用户信息
            $CSession = new CSessionInfoComponent();
            $change_result = $CSession->ChangeSessionInfo($params);
            if($change_result === false) {
                $ret['returnCode'] = ReturnCode::MA_CHANGE_SESSION_ERR;
                $ret['returnMessage'] = 'CHANGE_SESSION_ERR';
                return $ret;
            }
            if ($change_result === true) {
                $id = $CSession->GetUserByOpenId($id, $openid)['uuid'];
                $arr_result['id'] = $id;
                $arr_result['skey'] = $skey;
                $arr_result['user_info'] = json_decode(base64_decode($user_info));
                $arr_result['duration'] = $json_message['expires_in'];
                $ret['returnCode'] = ReturnCode::MA_OK;
                $ret['returnMessage'] = 'NEW_SESSION_SUCCESS';
                $ret['returnData'] = $arr_result;
            } else {
                $arr_result['id'] = $change_result;
                $arr_result['skey'] = $skey;
                $arr_result['user_info'] = json_decode(base64_decode($user_info));
                $arr_result['duration'] = $json_message['expires_in'];
                $ret['returnCode'] = ReturnCode::MA_OK;
                $ret['returnMessage'] = 'UPDATE_SESSION_SUCCESS';
                $ret['returnData'] = $arr_result;
            }
        } else {
            $ret['returnCode'] = ReturnCode::MA_WEIXIN_RETURN_ERR;
            $ret['returnMessage'] = 'WEIXIN_RETURN_ERR';
        }
        return $ret;
    }

    /**
     * 描述：登录态验证
     * @param $id  //UUID
     * @param $skey  //skey
     * @return bool
     */
    public function auth($id, $skey, $appid)
    {
        //TODO: 根据Id和skey 在cSessionInfo中进行鉴权，返回鉴权失败和密钥过期
        $cappinfo_data = $this->get_cappinfo($appid);
        $ret['appid'] = !isset($cappinfo_data['appid']) ? '' : $cappinfo_data['appid'];
        $ret['secret'] = !isset($cappinfo_data['secret']) ? '' : $cappinfo_data['secret'];
        if (empty($cappinfo_data) || ($cappinfo_data == false)) {
            $ret['returnCode'] = ReturnCode::MA_NO_APPID;
            $ret['returnMessage'] = 'NO_APPID';
            $ret['returnData'] = '';
            return $ret;
        }
        $login_duration = $cappinfo_data['login_duration'];
        $session_duration = $cappinfo_data['session_duration'];
        $params = [
            'id' => $cappinfo_data['id'],
            'uuid' => $id,
            'skey' => $skey,
            'login_duration' => $login_duration,
            'session_duration' => $session_duration
        ];

        $CSession = new CSessionInfoComponent();
        $auth_result = $CSession->CheckSessionForAuth($params);
        if(!$auth_result) {
            $ret['returnCode'] = ReturnCode::MA_AUTH_ERR;
            $ret['returnMessage'] = 'AUTH_FAIL';
            $ret['returnData'] = '';
            return $ret;
        }
        $result['user_info'] = json_decode(base64_decode($auth_result['user_info']),true);
        $result['user_info']['id'] = $auth_result['id'];
        $ret['returnCode'] = ReturnCode::MA_OK;
        $ret['returnMessage'] = 'AUTH_SUCCESS';
        $ret['returnData'] = $result;
        return $ret;
    }


    /**
     * 描述：解密数据
     * @param $id
     * @param $skey
     * @param $encrypt_data
     * @return bool|string
     */
    public function decrypt($id, $skey, $encrypt_data)
    {
        //1、根据id和skey获取session_key。
        //2、session_key获取成功则正常解密,可能解密失败。
        //3、获取不成功则解密失败。
        $CSession = new CSessionInfoComponent();
        $params = array(
            "id" => $id,
            "skey" => $skey
        );
        $result = $CSession->GetSessionInfo($params['id'], $params['skey']);
        if(empty($result) || !isset($result)) {
            $ret['returnCode'] = ReturnCode::MA_DECRYPT_ERR;
            $ret['returnMessage'] = 'GET_SESSION_KEY_FAIL';
            $ret['returnData'] = '';
            return $ret;
        }
        $session_key = $result['session_key'];
        $data = $this->aes128cbc_Decrypt($encrypt_data, $session_key);
        if ($data !== false) {
            $ret['returnCode'] = ReturnCode::MA_OK;
            $ret['returnMessage'] = 'DECRYPT_SUCCESS';
            $ret['returnData'] = $data;
        } else {
            $ret['returnCode'] = ReturnCode::MA_DECRYPT_ERR;
            $ret['returnMessage'] = 'GET_SESSION_KEY_SUCCESS_BUT_DECRYPT_FAIL';
            $ret['returnData'] = '';
        }
        return $ret;
    }

    /**
     * 获取cAppinfo
     * @param $AppId
     * @return array|false
     */
    private function get_cappinfo($AppId)
    {
        $sql = 'select * from cAppinfo where appid = :ad';
        $rst = \Yii::$app->db->createCommand($sql,[':ad' => $AppId])->queryOne();
        return $rst;
    }

    /**
     * 描述：解密数据
     * @param $encrypt_data
     * @param $session_key
     * @return bool|string
     */
    public function aes128cbc_Decrypt($encrypt_data, $session_key)
    {
        $aeskey = base64_decode($session_key);
        $iv = $aeskey;
        $encryptedData = base64_decode($encrypt_data);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $aeskey, $encryptedData, MCRYPT_MODE_CBC, $iv);
        return $this->stripPkcs7Padding($decrypted);
    }

    /**
     * 对解密后的明文进行补位删除
     * @param text //解密后的明文
     * @return //删除填充补位后的明文
     */
    function stripPkcs7Padding($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}