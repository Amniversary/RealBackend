<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:02
 */

namespace frontend\api\version;


use common\models\Books;
use frontend\api\IApiExecute;
use frontend\business\BookUtil;

class CreateBook implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)) {
            return false;
        }
        $weekly_id = $data['data']['weekly_id'];
        $title = $data['data']['title'];
        $model = new Books();
        $model->title = $title;
        $model->weekly_id = $weekly_id;
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        if(!BookUtil::SaveBooks($model, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }


    private function check_params($dataProtocal,&$error){
        $fields = ['weekly_id','title'];
        $fieldLabels = ['周刊id', '书籍标题'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}