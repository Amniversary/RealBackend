<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/17
 * Time: 上午2:06
 */

namespace backend\controllers\BatchKeyWordActions;


use backend\business\KeywordUtil;
use backend\components\ExitUtil;
use common\models\Keywords;
use yii\base\Action;

class SetAuthListAction extends Action
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
        $params = \Yii::$app->request->post('title');
        $selection = KeywordUtil::GetKeyWordAuthById($key_id);//TODO:公众号已有配置
        $rights = KeywordUtil::GetAuthParams();//TODO: 配置列表
        if(isset($params))
        {
            $rst = ['code' => '1', 'msg' => ''];
            $error = '';
            if(!KeywordUtil::SaveAuthParams($params,$key_id,$error)) {
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = '0';
            echo json_encode($rst);
            exit;
        }
        $this->controller->layout='main_empty';
        return $this->controller->render('setauthlist',[
            'keyword'=>$keyword,
            'rights'=>$rights,
            'selections' =>$selection
        ]);
    }
}