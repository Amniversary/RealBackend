<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:02
 */

namespace frontend\api\version\BooksBackend;


use common\models\BookParamsMenu;
use common\models\Books;
use frontend\api\IApiExecute;

class CreateBook implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $weekly_id = $dataProtocol['data']['weekly_id'];
        $title = $dataProtocol['data']['title'];
        $model = new Books();
        $model->title = $title;
        $model->weekly_id = $weekly_id;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        if (!($model instanceof Books)) {
            $error = '不是书籍记录对象';
            return false;
        }
        if (!$model->save()) {
            $error = '保存书籍记录失败';
            \Yii::error($error . ' :' . var_export($model->getErrors(), true));
            return false;
        }
        if (!empty($dataProtocol['data']['config_id']) || isset($dataProtocol['data']['config_id'])) {
            $BookParams = new BookParamsMenu();
            $BookParams->book_id = $model->book_id;
            $BookParams->system_id = $dataProtocol['data']['config_id'];
            $BookParams->save();
        }
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }


    private function check_params($dataProtocol, &$error)
    {
        $fields = ['weekly_id', 'title'];
        $fieldLabels = ['周刊id', '书籍标题'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}