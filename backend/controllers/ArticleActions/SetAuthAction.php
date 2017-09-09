<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/9
 * Time: 下午6:10
 */

namespace backend\controllers\ArticleActions;


use backend\business\KeywordUtil;
use common\models\ArticleOrder;
use yii\base\Action;

class SetAuthAction extends Action
{
    public function run()
    {
        $params = \Yii::$app->request->post('title');
        if(isset($params)) {
            $error = '';
            if(!KeywordUtil::SaveOrderAuthParams($params, $error)){
                $rst['msg'] = $error;
                echo json_encode($rst);
                exit;
            }
            $rst['code'] = 0;
            echo json_encode($rst);
            exit;
        }else{
            (new ArticleOrder())->deleteAll();
            $rst['code'] = 0;
            echo json_encode($rst);
        }
    }
}