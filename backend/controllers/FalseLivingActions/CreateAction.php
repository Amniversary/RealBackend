<?php

namespace backend\controllers\FalseLivingActions;


use common\components\GameRebotsHelper;
use common\models\Client;
use common\models\Living;
use frontend\business\LivingUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateFalseLivingSaveByTrans;
use yii\base\Action;
use yii\log\Logger;

/**
 * 新增假直播
 * Class CreateAction
 * @package backend\controllers\CreateAction
 */
class CreateAction extends Action
{
    public function run()
    {
        do{
            $randSQL = "SELECT *  FROM mb_client WHERE register_type !=1 and client_type=3 ORDER BY RAND() LIMIT 1";
            $clientModel = Client::findBySql($randSQL)->one();
            $living_info = Living::findOne(['living_master_id' => $clientModel['client_id']]);
        }while(!empty($living_info));

        $room_no = GameRebotsHelper::GetJobDates('roomNoBeanstalk','room_no');   //获取唯一房间号
        if(!LivingUtil::SetRoomNoIsUse($room_no['room_no'],$error))
        {
            return $this->controller->redirect('index');
        }

        $data['data']['living_master_id'] = $clientModel['client_id'];
        $data['data']['room_no'] = $room_no['room_no'];
        $data['data']['living_pic_url'] = $clientModel['pic'];
        $transActions[] = new CreateFalseLivingSaveByTrans($data);
        if(!RewardUtil::RewardSaveByTransaction($transActions,$outInfo, $error))
        {
            return $this->controller->redirect('index');
        }
        return $this->controller->redirect('index');
    }

}