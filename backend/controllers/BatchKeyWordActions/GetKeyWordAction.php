<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/17
 * Time: 下午4:22
 */

namespace backend\controllers\BatchKeyWordActions;


use backend\business\KeywordUtil;
use backend\business\WeChatUserUtil;
use backend\components\ExitUtil;
use common\models\AttentionEvent;
use yii\base\Action;

class GetKeyWordAction extends Action
{
    public function run($record_id)
    {
        if(empty($record_id)) {
            ExitUtil::ExitWithMessage('记录id不能为空');
        }
        $msg = AttentionEvent::findOne(['record_id'=>$record_id]);
        if(!isset($msg)){
            ExitUtil::ExitWithMessage('消息记录不存在');
        }
        $selection = KeywordUtil::GetGlobalMessageKeyParams($record_id);//TODO:消息已有关键字配置
        $rights = KeywordUtil::GetGlobalKeyWord();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('get_keylist',[
            'msg'=>$msg,
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }
}