<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 19:13
 */
namespace backend\controllers\ClientActions;

use common\models\Client;
use Yii\Db\Query;
use common\components\tenxunlivingsdk\TimRestApi;
use yii\base\Action;

class GroupNospeakingAction extends Action
{
    public function run()
    {
        $method = \Yii::$app->request->getQueryParam('method', null);

        switch ($method) {
            case 'query':
                $result = [
                    "status" => 100,
                    "msg" => '',
                ];
                $cliendNo = \Yii::$app->request->getQueryParam('client_no', null);
                $livingClientNo = \Yii::$app->request->getQueryParam('living_client_no', null);
                /**
                 * @var Client $cliend
                 */
                $cliend = Client::findOne(['client_no' => $cliendNo]);
                /**
                 * @var Client $living
                 */
                $living = Client::findOne(['client_no' => $livingClientNo]);

                if (empty($cliend) || empty($living)) {
                    $result['msg'] = '蜜播ID不正确';
                    return json_encode($result);
                }

                $query = new Query();
                $query->select('other_id')
                    ->from('mb_living')
                    ->leftJoin('mb_chat_room', 'mb_living.living_id = mb_chat_room.living_id')
                    ->where(['living_master_id' => $living->client_id]);
                $other = $query->one();
                if (empty($other)) {
                    $result['msg'] = '主播蜜播ID不存在';
                    return json_encode($result);
                }
                $other = current($other);
                $members = TimRestApi::get_group_shutted_uin($other, $error);
                if (!empty($error)) {
                    $result['msg'] = $error;
                    return json_encode($result);
                }
                if (!empty($members['ErrorCode'])) {
                    $result['msg'] = $members['ErrorInfo'];
                    return json_encode($result);
                }
                $shuttedUinList = $members['ShuttedUinList'];
                $shuttedUntil = 0;
                foreach ($shuttedUinList as $row) {
                    if ($row['Member_Account'] == $cliend->client_id) {
                        $shuttedUntil = $row['ShuttedUntil'];
                        break;
                    }
                }
                $result['status'] = 0;
                $result['client_id'] = $cliend->client_id;
                $result['other_id'] = $other;
                $result['shutted_until'] = empty($shuttedUntil) ? 0 : $shuttedUntil - time();
                return json_encode($result);
                break;
            case 'save':
                $result = [
                    "status" => 100,
                    "msg" => '',
                ];
                $cliendId = \Yii::$app->request->getQueryParam('client_id', null);
                $otherId = \Yii::$app->request->getQueryParam('other_id', null);
                $shutted_until = \Yii::$app->request->getQueryParam('shutted_until', null);
                TimRestApi::group_forbid_send_msg($otherId, $cliendId, (int)$shutted_until, $error);
                if (!empty($error)) {
                    $result['msg'] = $error;
                    return json_encode($result);
                }
                if (!empty($members['ErrorCode'])) {
                    $result['msg'] = $members['ErrorInfo'];
                    return json_encode($result);
                }
                $result['status'] = 0;
                return json_encode($result);
                break;
            default:
                $this->controller->getView()->title = '客户禁言管理';
                return $this->controller->render('groupnospeaking');
        }
    }
} 