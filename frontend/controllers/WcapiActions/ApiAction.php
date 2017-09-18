<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/10
 * Time: 下午4:37
 */

namespace frontend\controllers\WcapiActions;


use yii\base\Action;
use yii\base\Exception;
use yii\log\Logger;

class ApiAction extends Action
{
    /**
     * 检查参数
     * @param $error
     * @return bool
     */
    private function check_post_params(&$error)
    {
        $error = '';
        $POST = json_decode(file_get_contents("php://input"), true);

        if (!isset($POST['action_name']) ||
            !isset($POST['data'])
        ) {
            $error = '参数缺少';
            \Yii::error('lost param:' . var_export($POST, true), Logger::LEVEL_ERROR);
            return false;
        }
        if (empty($POST['action_name']) ||
            empty($POST['data'])
        ) {
            \Yii::error('not empty:' . var_export($POST, true), Logger::LEVEL_ERROR);
            $error = '参数不能为空';
            return false;
        }
        return true;
    }

    public function run()
    {
        $rstOut = ['code' => 1, 'msg' => ''];
        $rst = ['code' => 0, 'data' => '', 'msg' => ''];
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            \Yii::$app->response->statusCode = 200;
            echo json_encode($rst);
            exit;
        }
        if (!$this->check_post_params($error)) {
            $rstOut['msg'] = $error;
            echo json_encode($rstOut);
            exit;
        }
        $POST = json_decode(file_get_contents("php://input"), true);
        //\Yii::error("POST:".var_export($POST,true));
        $action_name = $POST['action_name'];
        $configFile = \Yii::$app->getBasePath() . '/api/Config.php';
        if (!file_exists($configFile)) {
            $rstOut['msg'] = '找不到配置接口文件';
            echo json_encode($rstOut);
            exit;
        }
        $funData = require($configFile);
        if (!isset($funData[$action_name])) {
            $rstOut['msg'] = '找不到对应接口';
            \Yii::error($rstOut['msg'] . '  action : ' . $action_name);
            echo json_encode($rstOut);
            exit;
        }
        $actionClass = 'frontend\api\version\\' . $funData[$action_name];
        if (!class_exists($actionClass)) {
            $rstOut['msg'] = '找不到对应接口类';
            \Yii::error($rstOut['msg'] . '  actionClass :' . $actionClass);
            echo json_encode($rstOut);
            exit;
        }
        $class = new $actionClass;
        try {
        if (!$class->execute_action($POST, $rst, $error)) {
            $rstOut['msg'] = $error;
            \Yii::error($error . ' 执行异常 action : ' . $action_name);
            echo json_encode($rstOut);
            exit;
        }
        } catch (Exception $e) {
            \Yii::error('Exception Error : ' . $e->getMessage());
            exit;
        }
        echo json_encode($rst, JSON_UNESCAPED_UNICODE);
    }
}