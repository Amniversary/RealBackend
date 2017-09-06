<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/6
 * Time: 上午11:14
 */

namespace backend\controllers\TagActions;


use backend\business\KeywordUtil;
use backend\business\TagUtil;
use backend\components\ExitUtil;
use common\models\SystemTag;
use yii\base\Action;

class GetAuthAction extends Action
{
    public function run($id)
    {
        if(empty($id)) {
            ExitUtil::ExitWithMessage('标签id 不能为空');
        }
        $Tag = SystemTag::findOne(['id'=>$id]);
        if(empty($Tag) || !isset($Tag)) {
            ExitUtil::ExitWithMessage('标签记录不存在');
        }
        $selections = TagUtil::getAuthByTagId($id);
        $rights = KeywordUtil::GetAuthParams();
        $this->controller->layout = 'main_empty';
        return $this->controller->render('get_auth',[
            'tag'=> $Tag,
            'selections' => $selections,
            'rights' => $rights
        ]);
    }
}