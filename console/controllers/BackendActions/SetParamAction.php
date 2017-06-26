<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 14:41
 */

namespace console\controllers\BackendActions;

use yii\base\Action;
use common\models\SystemParams;
use yii\db\Query;

class SetParamAction extends Action
{

    public function run($key, $value)
    {
        /**
         * @var SystemParams $systemParam
         */
        $systemParam = SystemParams::findOne([
            'code' => $key,
        ]);

        if (!$systemParam) {
            echo "$key is not find";
        } else {
            $oldValue = $systemParam->getAttribute('value1');
            $systemParam->setAttribute('value1', $value);
            $rst = $systemParam->save();
            echo $rst . ' old value:' . $oldValue;
            echo "\n";
        }
    }
}