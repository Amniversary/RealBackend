<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/14
 * Time: 上午10:17
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\CarouselsUtil;

class GetCarousel implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (empty($data['data']['id'] || !isset($data['data']['id']))) {
            $error = '轮播图id , 不能为空';
            return false;
        }
        $id = $data['data']['id'];
        $carousel = CarouselsUtil::GetCarousel($id);
        if (empty($carousel)) {
            $error = '获取轮播图失败, 记录已删除或不存在';
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = [
            'id' => intval($carousel->carousel_id),
            'pic_url' => $carousel->pic_url,
            'description' => $carousel->description,
            'type' => $carousel->action_type,
            'url' => $carousel->url,
            'status' => $carousel->status,
            'create_time' => $carousel->create_time,
            'update_time' => $carousel->update_time
        ];
        return true;
    }
}