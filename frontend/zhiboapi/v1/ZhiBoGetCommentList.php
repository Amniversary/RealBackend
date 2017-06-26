<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/11
 * Time: 13:44
 */

namespace frontend\zhiboapi\v1;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\zhiboapi\IApiExcute;

/**
 * 获取动态评论列表协议接口 hbh
 * Class ZhiBoGetCommentList
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetCommentList implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }

        $page_no = intval($dataProtocal['data']['page_no']);
        if(empty($page_no) || ($page_no <= 0))
        {
            $page_no = 1;
        }

        $page_size = intval($dataProtocal['data']['page_size']);
        if(empty($page_size) || ($page_size <= 0))
        {
            $page_size = 5;
        }
        if($page_size > 50)
        {
            $page_size = 50;
        }
        $dynamic_id = $dataProtocal['data']['dynamic_id'];
        $Dynamic = DynamicUtil::GetDynamicById($dynamic_id);
        if(!isset($Dynamic))
        {
            $error = '动态记录不存在';
            return false;
        }

        $comment_list = DynamicUtil::GetCommentListInfo($Dynamic,$page_no,$page_size);

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = $comment_list;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','dynamic_id','page_no','page_size'];
        $fieldLabels = ['唯一号','动态id','页数','每页记录数'];
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
} 