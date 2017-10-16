<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/11
 * Time: 下午4:58
 */

namespace backend\controllers\LaterActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\LaterParams;
use yii\base\Action;

class GetKeyAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('记录id不能为空');
        }
        $params = LaterParams::findOne(['id'=>$id]);
        if(!isset($params)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $selection = KeywordUtil::GetLaterKeyParams($id);//TODO:消息已有关键字配置
        $rights = KeywordUtil::GetLaterKeyWord();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('get_key',[
            'params'=>$params,
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }

}