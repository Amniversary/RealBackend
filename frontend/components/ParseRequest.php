<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/26
 * Time: 上午10:40
 */

namespace frontend\components;


class ParseRequest
{
    /**
     * 描述：解析接口名称
     * @param $request_json
     * @return array|int
     */
    public function parse_json($request_json)
    {
        $ret['version'] = 1;
        $ret['componentName'] = 'MA';
        $ret['returnData'] = '';
        if ($this->is_json($request_json)) {
            $decode_data = json_decode($request_json, true);
            //TODO : 验证接口名是否为空
            if (!isset($decode_data['interface']['interfaceName'])) {
                $ret['returnCode'] = ReturnCode::MA_NO_INTERFACE;
                $ret['returnMessage'] = 'NO_INTERFACENAME_PARA';
                return json_encode($ret);
            }
            if (!isset($decode_data['interface']['para'])) { //TODO: 验证code,encrypt_data,iv,appid
                $ret['returnCode'] = ReturnCode::MA_NO_PARA;
                $ret['returnMessage'] = 'NO_PARA';
                return json_encode($ret);
            }
            $interfaceName = $decode_data['interface']['interfaceName'];
            $data = $decode_data['interface']['para'];
            switch ($interfaceName) {
                case 'qcloud.cam.id_skey':
                    $ret = $this->id_skey($data['code'], $data['encrypt_data'], $data['iv'], $data['appid']);
                    break;
                case 'qcloud.cam.auth':
                    $ret = $this->auth($data['id'], $data['skey'], $data['appid']);
                    break;
                case 'qcloud.cam.decrypt':
                    $ret = $this->decrypt($data['id'], $data['skey'], $data['encrypt_data']);
                    break;
                case 'qcloud.cam.initdata':
                    $ret = $this->initdata($data['appid'], $data['secret'], $data['qcloud_appid'], $data['ip'],
                        $data['cdb_ip'], $data['cdb_port'], $data['cdb_user_name'], $data['cdb_pass_wd']);
                    break;
                default:
                    $ret['returnCode'] = ReturnCode::MA_INTERFACE_ERR;
                    $ret['returnMessage'] = 'INTERFACENAME_PARA_ERR';
                    break;
            }
        } else {
            $ret['returnCode'] = ReturnCode::MA_REQUEST_ERR;
            $ret['returnMessage'] = 'REQUEST_IS_NOT_JSON';
        }
        //\Yii::error('rst_json : '. json_encode($ret));
        return json_encode($ret);
    }

    /**
     * 验证字符串是不是合法的json
     * @param $str
     * @return bool
     */
    private function is_json($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 用户登录
     * 获取id 和 skey
     * @param $code
     * @param $encrypt_data
     * @param $iv
     * @return array|int
     */
    private function id_skey($code, $encrypt_data, $iv, $appid)
    {
        if (isset($code) && isset($encrypt_data)) {
            $auth = new AuthComponent();
            if (!isset($iv)) {
                $ret = $auth->get_id_skey($code, $encrypt_data, $appid);
            } else {
                $ret = $auth->get_id_skey($code, $encrypt_data, $appid, $iv);
            }
        } else {
            $ret['returnCode'] = ReturnCode::MA_PARA_ERR;
            $ret['returnMessage'] = 'PARA_ERR';
            $ret['returnData'] = '';
        }
        return $ret;
    }

    /**
     * 检测用户登录
     * 获取用户基本信息
     * @param $id
     * @param $skey
     * @param $appid
     * @return bool
     */
    private function auth($id, $skey, $appid)
    {
        if (isset($id) && isset($skey) && isset($appid)) {
            $auth = new AuthComponent();
            $ret = $auth->auth($id, $skey, $appid);
        } else {
            $ret['returnCode'] = ReturnCode::MA_PARA_ERR;
            $ret['returnMessage'] = 'PARA_ERR';
            $ret['returnData'] = '';
        }
        return $ret;
    }

    /**
     * 解密数据
     * @param $id
     * @param $skey
     * @param $encrypt_data
     * @return bool|string
     */
    private function decrypt($id, $skey, $encrypt_data)
    {
        if (isset($id) && isset($skey) && isset($encrypt_data)) {
            $auth = new AuthComponent();
            $ret = $auth->decrypt($id, $skey, $encrypt_data);
        } else {
            $ret['returnCode'] = ReturnCode::MA_PARA_ERR;
            $ret['returnMessage'] = 'PARA_ERR';
            $ret['returnData'] = '';
        }
        return $ret;
    }

    /**
     * @param $appid
     * @param $secret
     * @param $qcloud_appid
     * @param $ip
     * @param $cdb_ip
     * @param $cdb_port
     * @param $cdb_user_name
     * @param $cdb_pass_wd
     * @return mixed
     */
    private function initdata($appid, $secret, $qcloud_appid, $ip, $cdb_ip, $cdb_port, $cdb_user_name, $cdb_pass_wd)
    {
        if (isset($appid) && isset($secret) && isset($qcloud_appid) && isset($ip)
            && isset($cdb_ip) && isset($cdb_port) && isset($cdb_user_name) && isset($cdb_pass_wd)) {
            $auth = new AuthComponent();
            $ret = $auth->init_data($appid, $secret, $qcloud_appid, $ip, $cdb_ip, $cdb_port, $cdb_user_name, $cdb_pass_wd);
        } else {
            $ret['returnCode'] = ReturnCode::MA_PARA_ERR;
            $ret['returnMessage'] = 'PARA_ERR';
            $ret['returnData'] = '';
        }
        return $ret;
    }
}