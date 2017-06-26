<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 14:38
 */

namespace backend\controllers\ClientActions;


use familyend\components\ExitUtil;
use frontend\business\ClientUtil;
use frontend\business\JobUtil;
use yii\base\Action;
use yii\log\Logger;

class UpdatePicAction extends Action
{
    public function run($client_id)
    {
        $model = ClientUtil::GetClientById($client_id);
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('找不到用户信息');
        }

        $params = [];
        //page=1&per-page=5
        $page = \Yii::$app->request->getQueryParam('page');
        if(isset($page))
        {
            $params['page'] = $page;
        }
        $per_page = \Yii::$app->request->getQueryParam('per-page');
        if(isset($per_page))
        {
            $params['per-page'] = $per_page;
        }
        if ($model->load(\Yii::$app->request->post()) && $model->save())
        {
            if(!empty($model->pic))
            {
                $data = [
                    'client_id'=>$model->client_id,
                    'pic'=>$model->pic,
                ];
                if(!JobUtil::AddPicJob('deal_client_pic',$data,$error))
                {
                    \Yii::getLogger()->log($error.' pic job save error',Logger::LEVEL_ERROR);
                }
            }

            //当更新成功是删除榜单前4位的缓存和热门直播的缓存
            \yii::$app->cache->delete('hot_users');
            \yii::$app->cache->delete('mb_api_hot_living_list_1');

            return $this->controller->redirect(array_merge(['client_cover_index'],$params));
        }
        else
        {
            return $this->controller->render('updatepic', [
                'model' => $model,
                'params'=>$params
            ]);
        }
    }
} 