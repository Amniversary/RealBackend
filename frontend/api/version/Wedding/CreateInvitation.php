<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/9
 * Time: 下午5:50
 */

namespace frontend\api\version\Wedding;


use common\models\CInvitationCard;
use frontend\api\IApiExecute;
use frontend\business\AppInfoUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByTransactions\SaveByTransaction\AddInvitationSaveByTrans;

class CreateInvitation implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) return false;
        $data = $dataProtocol['data'];
        $header = \Yii::$app->request->headers;
        $AppInfo = AppInfoUtil::GetAppInfo($header['appid']);
        $User = AppInfoUtil::GetUserByAppId($AppInfo->id, $header['openid']);
        if (empty($User)) {
            $error = '用户未授权, 请授权小程序';
            return false;
        }
        $time = time();
        $wedding = strtotime($data['wedding_time']);
        if ($wedding < $time) {
            $error = '婚礼日期不正确';
            return false;
        }
        $model = new CInvitationCard();
        $model->bride = $data['bride'];
        $model->bridegroom = $data['bridegroom'];
        $model->phone = $data['phone'];
        $model->site = $data['site'];
        $model->wedding_time = $data['wedding_time'];
        $model->pic = isset($data['pic']) ? $data['pic'] : '';
        $model->latitude = $data['latitude'];
        $model->longitude = $data['longitude'];
        $model->status = 1;
        $model->create_time = time();
        $transAction[] = new AddInvitationSaveByTrans($model, ['id' => $User['id']]);
        if (!SaveByTransUtil::SaveByTransaction($transAction, $error, $out)) {
            return false;
        }
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['bride', 'bridegroom', 'phone', 'site', 'wedding_time', 'latitude', 'longitude'];
        $filesLabel = ['新娘名称', '新郎名称', '联系电话', '婚礼地址', '婚礼时间', '纬度', '经度'];
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