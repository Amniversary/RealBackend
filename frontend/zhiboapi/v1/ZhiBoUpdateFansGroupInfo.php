<?php
/**
 * Created by PhpStorm.
 * User: Zff
 * Date: 2016/9/10
 * Time: 15:00
 */

namespace frontend\zhiboapi\v1;

use frontend\business\FansGroupUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

class ZhiBoUpdateFansGroupInfo implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['group_id'];
        $fieldLabels = ['粉丝群ID'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData, &$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        if(!FansGroupUtil::UpdateFansGroupInfo($dataProtocal, $error)){
            return false;
        }

        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = '群信息修改成功';
        return true;
    }
} 