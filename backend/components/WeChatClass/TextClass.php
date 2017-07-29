<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/1
 * Time: 下午2:42
 */

namespace backend\components\WeChatClass;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\business\WeChatUserUtil;
use backend\components\MessageComponent;
use backend\components\ReceiveType;
use common\components\UsualFunForNetWorkHelper;
use common\models\Keywords;
use yii\db\Query;

class TextClass
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 处理文本事件
     */
    public function Text()
    {
        $msgObj = new MessageComponent($this->data,1);
        $content = $msgObj->VerifySendMessage();

        return $content;
    }
}