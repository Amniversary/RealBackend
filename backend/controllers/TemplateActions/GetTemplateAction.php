<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/1
 * Time: 下午6:45
 */

namespace backend\controllers\TemplateActions;


use backend\business\TemplateUtil;
use backend\business\WeChatUserUtil;
use backend\components\TemplateComponent;
use yii\base\Action;

class GetTemplateAction extends Action
{
    public function run($id)
    {
        $this->controller->getView()->title = '设置模板消息';
        $cache = WeChatUserUtil::getCacheInfo();
        $templateClass = new TemplateComponent(null,$cache['record_id']);
        $templateData = TemplateUtil::GetTemplateById($id);
        $content = $templateData->content;
        $data = $templateClass->FormatTemplate($content);

        return $this->controller->render('get_template',[
            'data'=>$data,
            'template'=>$templateData
        ]);
    }
}