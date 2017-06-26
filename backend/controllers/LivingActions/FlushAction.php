<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 13:04
 */

namespace backend\controllers\LivingActions;

use yii\base\Action;
use backend\business\LivingControl;

class FlushAction extends Action
{

    public function run()
    {
        $params = \Yii::$app->request->getQueryParams();
        $result = [
            'status' => 1,
            'errorMessge' => '',
            'data' => []
        ];
        try {
            $method = isset($params['method']) ? $params['method'] : 'flush';
            $control = new LivingControl();
            switch ($method) {
                case 'add_users':
                    $result['data'] = $control->addUsers($params['users']);
                    break;
                case 'remove_users':
                    $result['data'] = $control->removeUsers($params['users']);
                    break;
                case 'flush':
                default:
                    $result['data'] = $control->getUserRel($params['user_id']);

            }
        } catch (\Exception $e) {
            $result['status'] = 0;
            $result['errorMessge'] = $e->getMessage();
        }
        exit(json_encode($result));
    }
} 