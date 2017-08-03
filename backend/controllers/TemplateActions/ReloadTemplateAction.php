<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/1
 * Time: 下午3:44
 */

namespace backend\controllers\TemplateActions;


use backend\business\WeChatUserUtil;
use backend\components\TemplateComponent;
use common\models\Template;
use yii\base\Action;
use yii\db\Exception;

class ReloadTemplateAction extends Action
{
    public function run()
    {
        $rst = ['code'=>1, 'msg'=>''];
        $cache = WeChatUserUtil::getCacheInfo();
        $accessToken = $cache['authorizer_access_token'];
        $template = new TemplateComponent(null,$accessToken);
        $tempData = $template->GetTemplateList();
        if(!$tempData || $tempData['errcode'] != 0) {
            $rst['msg'] = "获取模板失败: Code:".$tempData['errcode'].' msg: '.$tempData['errmsg'];
            \Yii::error($rst['msg'] . 'app_id: '. $cache['record_id']);
            echo json_encode($rst);exit;
        }

        $tempList = $tempData['template_list'];
        try{
            $table = \Yii::$app->db;
            $trans = $table->beginTransaction();
            Template::deleteAll(['app_id'=>$cache['record_id']]);
            foreach($tempList as $list) {
                $model = new Template();
                $model->template_id = $list['template_id'];
                $model->app_id = $cache['record_id'];
                $model->title = $list['title'];
                $model->primary_industry = !isset($list['primary_industry']) ? '': $list['primary_industry'];
                $model->deputy_industry = !isset($list['deputy_industry']) ? '':$list['deputy_industry'] ;
                $model->content = !isset($list['content']) ? '':$list['content'];
                $model->example = !isset($list['example']) ? '':$list['example'];
                if(!$model->save()) {
                    $rst['msg'] = "保存模板记录失败";
                    \Yii::error($rst['msg'] . ' : '.var_export($model->getErrors(),true));
                    echo json_encode($rst);
                    exit;
                }
            }
            $trans->commit();
        }catch(Exception $e){
            $trans->rollBack();
            $rst['msg'] = $e->getMessage();
            echo json_encode($rst);exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}