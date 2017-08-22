<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/17
 * Time: 上午2:06
 */

namespace backend\controllers\SignActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\Keywords;
use yii\base\Action;

class GetAuthAction extends Action
{
    public function run($key_id)
    {
        if(empty($key_id)) {
            ExitUtil::ExitWithMessage('关键字id不能为空');
        }
        $keyword = Keywords::findOne(['key_id'=>$key_id]);
        if(!isset($keyword)){
            ExitUtil::ExitWithMessage('关键子不存在');
        }
        $selection = KeywordUtil::GetKeyWordAuthById($key_id);//TODO:公众号已有配置
        $rights = KeywordUtil::GetAuthParams();//TODO: 配置列表
        $this->controller->layout='main_empty';
        return $this->controller->render('setauthlist',[
            'keyword'=>$keyword,
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }
}