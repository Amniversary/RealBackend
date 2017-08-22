<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午3:49
 */

namespace backend\controllers\SignActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\SignParams;
use yii\base\Action;

class GetKeyAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('记录id不能为空');
        }
        $params = SignParams::findOne(['id'=>$id]);
        if(!isset($params)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $selection = KeywordUtil::GetSignKeyParams($id);//TODO:消息已有关键字配置
        $rights = KeywordUtil::GetSignKeyWord();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('get_key',[
            'params'=>$params,
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }
}