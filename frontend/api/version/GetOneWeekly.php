<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/14
 * Time: 上午10:37
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class GetOneWeekly implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(empty($data['data']['weekly_id']) || !isset($data['data']['weekly_id'])) {
            $error = '周刊id , 不能为空';
            return false;
        }
        $weekly_id = $data['data']['weekly_id'];
        $weekly = WeeklyUtil::GetWeekly($weekly_id);
        if(empty($weekly))  {
            $error = '获取周刊失败, 记录已删除或不存在';
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = [
            'id' => $weekly->weekly_id,
            'title' => $weekly->title,
            'weeks' => $weekly->weeks,
            'status' => $weekly->status,
            'create_time' => $weekly->create_time,
            'update_time' => $weekly->update_time,
        ];
        return true;
    }
}