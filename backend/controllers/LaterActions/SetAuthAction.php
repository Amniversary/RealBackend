<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/27
 * Time: 下午1:50
 */

namespace backend\controllers\LaterActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use common\models\BatchKeywordList;
use common\models\Keywords;
use yii\base\Action;

class SetAuthAction extends Action
{
    public function run($key_id)
    {
        if (empty($key_id)) {
            ExitUtil::ExitWithMessage('关键字id不能为空');
        }
        $keyword = Keywords::findOne(['key_id' => $key_id]);
        if (!isset($keyword)) {
            ExitUtil::ExitWithMessage('关键子不存在');
        }
        $params = \Yii::$app->request->post('title');
        if (isset($params)) {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if (!KeywordUtil::SaveAuthParams($params, $key_id, $error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        } else {
            (new BatchKeywordList())->deleteAll(['key_id' => $key_id]);//TODO: 删除用户原有权限数据
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
    }
}