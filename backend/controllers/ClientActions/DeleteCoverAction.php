<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\ClientActions;


use common\models\Client;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteCoverAction extends Action
{
    public function run($client_no)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($client_no))
        {
            $rst['msg']='用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $cover = ClientUtil::GetClientNo($client_no);
        if(!isset($cover))
        {
            $rst['msg']='用户信息不存在';
            echo json_encode($rst);
            exit;
        }
        $cover->pic = 'http://mbpic.mblive.cn/meibo-test/feonghao.png';
        $cover->middle_pic = '';
        $cover->main_pic = '';
        $cover->icon_pic ='';

        if (!$cover->save())
        {
             $error = '删除用户图片失败';
            \Yii::getLogger()->log($error.' :'.$cover->getErrors(),Logger::LEVEL_ERROR);
            return false;
        }else{
            //当删除成功是删除榜单前4位的缓存
            \yii::$app->cache->delete('hot_users');
            \yii::$app->cache->delete('mb_api_hot_living_list_1');

            $rst=['code'=>'0','msg'=>'删除成功'];
            echo json_encode($rst);
            exit;
        }



//        $p1 = '&per';
//        $p2 = 'page=';
//        //获取前一个页面的地址（为了得到参数）
//        $pages = $_SERVER['HTTP_REFERER'];
//        $pagess1 = strpos($pages,$p1);
//        $pagess2 = strpos($pages,$p2)+5;
//        $p3 = $pagess1-$pagess2;
//        $page = substr($pages,$pagess2,$p3);

//          return $this->controller->redirect('/client/client_cover_index');
    }
}
?>