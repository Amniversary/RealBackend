<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\FuckActions;



use yii\base\Action;

class TestHtml5Action extends Action
{

    public function run()
    {
        //\Yii::$app->response->getHeaders()->set('Content-Type','text/event-stream')
        //->set('Cache-Control','no-cache');
        //var_dump(\Yii::$app->response->getHeaders());
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');

        $time = date('Y-m-d H:i:s');
        //$time = date('r');
        echo "data: The server time is: {$time}\n\n";
        //flush();
        //ob_end_flush();
    }

} 