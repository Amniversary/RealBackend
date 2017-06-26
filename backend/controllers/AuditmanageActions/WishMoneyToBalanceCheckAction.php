<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\AuditmanageActions;


use backend\business\BackendBusinessCheckUtil;
use frontend\business\BaseCerificationUtil;
use frontend\business\BusinessCheckUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\UserActiveUtil;
use yii\base\Action;
use backend\components\ExitUtil;
use yii\data\ArrayDataProvider;

/**
 * 提现审核显示页面
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class WishMoneyToBalanceCheckAction extends Action
{
    public function run($relate_id,$check_id)
    {
        $this->controller->getView()->title = '愿望金额提现审核明细';
        $this->controller->layout = 'main_empty';
        $checkRecord = BusinessCheckUtil::GetBusinessCheckById($check_id);
        if(!isset($checkRecord))
        {
            ExitUtil::ExitWithMessage('审核记录不存在');
        }
        $user = PersonalUserUtil::GetAccontInfoById($checkRecord->create_user_id);
        if(!isset($user))
        {
            ExitUtil::ExitWithMessage('用户信息不存在');
        }
        $userActive = UserActiveUtil::GetUserActiveByUserId($user->account_id);
        if(!isset($userActive))
        {
            ExitUtil::ExitWithMessage('用户活跃度信息不存在');
        }
        $base_centification = BaseCerificationUtil::GetBaseCertificationInfoByUserId($user->account_id);
        if(!isset($base_centification))
        {
            $base_centification=[];
            //ExitUtil::ExitWithMessage('初级认证信息不存在');
        }
        $check_type = $checkRecord->business_type;
        $relateData = null;
        $error = '';
/*        if(!BackendBusinessCheckUtil::GetBusinessCheckData($checkRecord,$relateData,$error))
        {
            ExitUtil::ExitWithMessage($error);
        }*/
        $refusedDataColumns = [
            [
                'label'=>'审核类型',
                'attribute'=>'check_type',
            ],
            [
                'label'=>'审核时间',
                'attribute'=>'check_time',
            ],
            [
                'label'=>'审核人',
                'attribute'=>'check_user',
            ],
            [
                'label'=>'拒绝原因',
                'attribute'=>'refused_reason',
            ],
        ];
        $dataStr = $userActive->check_refused_content;
        $dataModels = [];
        if(!empty($dataStr))
        {
            $dataModels = unserialize($dataStr);
        }
        $dataProvider = new ArrayDataProvider();
        $dataProvider->allModels = $dataModels;
        echo $this->controller->render('check_detail_for_wishmoneytobalance',
                [
                    'user'=>$user,
                    'check_record' => $checkRecord,
                    'user_active' => $userActive,
                    'relate_data'=>$relateData,
                    'check_type'=>$check_type,
                    'data_columns'=>$refusedDataColumns,
                    'dataProvider'=>$dataProvider,
                    'other_view'=>'relateview_'.strval($check_type),
                    //'relate_data'=>$relateData,
                    'check_id'=>$check_id,
                    'base_centification'=>$base_centification,
                ]
            );
    }

    /**
     * 获取审核相关数据
     * @param $relate_id
     * @param $check_type
     */
    private function GetRelateData($relate_id, $check_type)
    {

    }
} 