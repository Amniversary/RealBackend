<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/6
 * Time: 上午10:06
 */

namespace backend\controllers\PublicListActions;


use backend\business\TagUtil;
use yii\base\Action;

class GetTagListAction extends Action
{
    public function run(){
        $selection = [];
        $rights = TagUtil::GetTagListName();
        $this->controller->layout = 'main_empty';
        return  $this->controller->render('get_tag_list',
            [
                'rights'=>$rights,
                'selections'=>$selection
            ]);
    }
}