<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:44
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\ClientActions\IndexAction',
    ],
    'contract'=>[
        'class'=> 'backend\controllers\ClientActions\ContractInfoAction',
    ],
    'setstatus'=>[
        'class'=> 'backend\controllers\ClientActions\SetStatusAction',
    ],
    'setinner'=>[
        'class'=> 'backend\controllers\ClientActions\SetInnerAction',
    ],
    'cash_rite'=>[
        'class'=> 'backend\controllers\ClientActions\CashRiteAction',
    ],
    'client_finance_index'=>[
        'class'=> 'backend\controllers\ClientActions\ClientFinanceIndexAction',
    ],
    'client_cover_index'=>[
        'class'=> 'backend\controllers\ClientActions\ClientCoverIndexAction',
    ],
    'client_robot_index'=>[
        'class'=> 'backend\controllers\ClientActions\ClientRobotIndexAction',
    ],
    'update_bean'=>[
        'class'=> 'backend\controllers\ClientActions\UpdateTicketAction',
    ],
    'unbindwecat'=>[
        'class'=> 'backend\controllers\ClientActions\UnbindWeCatAction',
    ],
    'moneydetail'=>[
        'class'=> 'backend\controllers\ClientActions\MoneyDetailAction',
    ],
    'set_client_type'=>[
        'class'=> 'backend\controllers\ClientActions\SetClientTypeAction',
    ],
    'ticket_detail'=>[
        'class'=> 'backend\controllers\ClientActions\TicketCountDetailAction',
    ],
    'real_bean'=>[
        'class'=> 'backend\controllers\ClientActions\UpdateBeanAction',
    ],
    'set_client_id'=>[
        'class'=> 'backend\controllers\ClientActions\SetClientIdAction',
    ],
    'delete_cover'=>[
        'class'=> 'backend\controllers\ClientActions\DeleteCoverAction',
    ],
    'robot_no'=>[
        'class'=> 'backend\controllers\ClientActions\SetRobotNoAction',
    ],
    'set_freeze'=>[
        'class'=> 'backend\controllers\ClientActions\SetFreezeStatusAction',
    ],
    'updatepic'=>[
        'class'=>'backend\controllers\ClientActions\UpdatePicAction',
    ],
    'send_gift_detail'=>[
        'class'=>'backend\controllers\ClientActions\SendGiftDetailAction',
    ],
    'receive_gift_detail'=>[
        'class'=>'backend\controllers\ClientActions\ReceiveGiftDetailAction',
    ],
    'set_sign_name'=>[
        'class'=>'backend\controllers\ClientActions\SetSignNameAction',
    ],
    'set_client_name'=>[
        'class'=>'backend\controllers\ClientActions\SetClientNameAction',
    ],
    'delete_ticket'=>[
        'class'=>'backend\controllers\ClientActions\DeleteTicketAction',
    ],
    'checkbatch'=>[
        'class'=>'backend\controllers\ClientActions\CheckBatchAction',
    ],
    'nospeaking'=>[
        'class'=>'backend\controllers\ClientActions\NospeakingAction',
    ],
    'get_nospeaking'=>[
        'class'=>'backend\controllers\ClientActions\GetnospeakingAction',
    ],
    'set_nospeaking'=>[
        'class'=>'backend\controllers\ClientActions\SetnospeakingAction',
    ],
    'set_nospeaking_bacth'=>[
        'class'=>'backend\controllers\ClientActions\SetnospeakingbatchAction',
    ],
    'im_msg_monitor'=>[
        'class'=>'backend\controllers\ClientActions\IMMsgMonitorAction',
    ],
    'im_msg'=>[
        'class'=>'backend\controllers\ClientActions\IMMsgAction',
    ],
    'set_status_normal'=>[
        'class'=>'backend\controllers\ClientActions\SetStatusNormalAction',
    ],
    'apporve_user'=>[
        'class'=>'backend\controllers\ClientActions\ApporveUserAction',
    ],
    'gift_repair' => [
        'class'=>'backend\controllers\ClientActions\GiftRepairAction',
    ],
    'living' => [
        'class'=>'backend\controllers\ClientActions\LivingAction',
    ],
    'group_nospeaking'=>[
        'class'=>'backend\controllers\ClientActions\GroupNospeakingAction',
    ],
];