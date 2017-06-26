<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\FuckActions;


use backend\business\StatisticActiveUserUtil;
use backend\business\StatisticLivingPersonCountUtil;
use backend\business\StatisticNiuNiuGameMoneyUtil;
use backend\business\StatisticRechargeMoneyUtil2;
use common\models\Approve;
use common\models\CloseIdLog;
use common\models\HitSuperMan;
use common\models\OffUserLiving;
use console\controllers\BackendActions\OperateStatisAction;
use frontend\business\ActivityUtil;
use frontend\business\AddregnumDayUtil;
use frontend\business\BusinessCheckUtil;
use frontend\business\ClientUtil;
use frontend\business\OperateStatisUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class WldAction extends Action
{

    public function run()
    {

        $white_user_id = OffUserLiving::findOne(['client_no'=>'22568544']);

        if(empty($white_user_id))
        {
            echo 'aaa';
            exit;
        }
        print_r('<pre/>');
        print_r($white_user_id);
        exit;

        $query = (new Query())
            ->select(['log_id','client_no'])
            ->from('mb_close_id_log')
            ->where('management_type=2')
            ->all();

        print_r('<pre/>');
        print_r($query);


//        \yii::$app->cache->set('online_user_num',0);
        //记录在线用户
//        $datetime = date('Y-m-d H', mktime(date('H') - 1));
//
//        OperateStatisAction::SetLastHouseAddOnlineUser($datetime,$error);

//        if(!empty($online_user_num)||$online_user_num == 0)
//        {
//            $online_user_num++;
//            \yii::$app->cache->set('online_user_num',$online_user_num);
//        }else
//        {
//            \yii::$app->cache->set('online_user_num',0);
//        }




//        $sqaa  = HitSuperMan::findAll('');
//
//        foreach ($sqaa as $vv)
//        {
//            $s = array_search($vv,$sqaa);
//            $data[$s]['name'] = $vv['man_name'];
//            $data[$s]['num'] = $vv['hit_num'];
//        }
//
//        $rst['code'] = '0';
//        $rst['msg'] = $data;
//        print_r('<pre/>');
//        print_r($rst);


        exit;
















        $query = ClientUtil::AddActiveUser(2);

        print_r('<pre/>');
        print_r($query);
        exit;


        $query = (new Query())
            ->select(['cb.log_id','mcb.balance_id','mcb.user_id','mcb.device_type','mcb.operate_type','mcb.relate_id','mr.pay_type','before_balance','mcb.operate_value','after_balance','mcb.create_time','mub.account_balance'])
            ->from('mb_client_balance_log mcb')
            ->leftJoin('mb_recharge mr','mcb.user_id = mr.user_id and mcb.relate_id = mr.recharge_id')
            ->leftJoin('mb_ticket_to_cash mtt','mcb.user_id = mtt.user_id and mcb.relate_id = mtt.record_id')
            ->leftJoin('mb_update_balance_record mub','mcb.operate_type = mub.operate_type and mcb.create_time = mub.create_time')
            ->where('mcb.operate_type in(1,3,6,12,14,15,16,17,18,19,20,21,27,28) and mcb.user_id=51 and mcb.remark1=\'bean_balance\'')
            ->orderBy('mcb.create_time,mcb.log_id desc')
            ->all();

        print_r('<pre/>');
        print_r($query);
        exit;

//        echo date("Y-m-d",strtotime("-1 day"));
        $sqq = StatisticActiveUserUtil::CheckDateIsExist(1,date("Y-m-d",strtotime("-1 day")),$error);


//        $sqq = OperateStatisUtil::GetOneHouseActiveAnchorNumDate();

//        $sql = OperateStatisUtil::GetOneHouseAddregnumDate();
//
//        $time = date('Y-m-d').' '.date('H', strtotime("-0"));


        print_r("<pre/>");
        print_r($sqq.'<br/>');
//        print_r($time2);

        if(!$sqq){
            echo "aaa";
        }




        exit;

        $retVal =  ClientUtil::AddActiveUser(6);

            echo "aaaa";

        exit;

        print_r("<pre/>");
        print_r($retVal);



        exit;

//        $aa = date('Y-m-d', strtotime("-1 day"));
//        print_r($aa);


        $query = OperateStatisUtil::GetThirtyRechargeNumDate();

        print_r("<pre/>");
        print_r($query);

        exit;

        $query = (new Query())
            ->select(['statistics_time','statistics_num'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 1 and DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(statistics_time)')
            ->orderBy('statistics_time asc')
            ->all();



        print_r("<pre/>");
//        print_r($query);



        foreach($query as $vv)
        {
            $s = array_search($vv,$query);
            for($i=1;$i<=30;$i++)
            {
                if($vv['statistics_time'] != date('Y-m-d', strtotime("-$i day")))
                {
                    $v[$s] = 0;
                }
                else{
                    $v[$s] = $vv['statistics_num'];
                    break;
                }

            }
        }

        print_r($v);


        exit;



        //获取注册的24小时的人数

        $query = (new Query())
            ->select(['statistics_type','statistics_time','statistics_num'])
            ->from('mb_add_reg_num')
            ->where('statistics_type = 4 and to_days(statistics_time)=to_days(now())')
            ->orderBy('statistics_time desc')
            ->all();

        print_r('<pre/>');
        print_r($query);

        exit;

        //根据当天获取本周的的日期
        $tswk = date('Y-W');
        //根据本周一获取本周的日期
        $tswk_1 = date('Y-W', strtotime(date('Y-m-d', (time() - ((date('w') == 0 ? 7 : date('w')) - 1) * 24 * 3600))));

        if ($tswk != $tswk_1) {
            $tswk = $tswk_1;
        }

        print_r($tswk);

        //查找数据库的统计时间 查看是否有与当前时间一致的统计时间
        $query = (new Query())
            ->select(['statistics_time', 'statistics_type'])
            ->from('mb_add_reg_num')
            ->where('statistics_time in (:day,:week,:month,:house)', [':day' => date('Y-m-d'), ':week' => $tswk, ':month' => date('Y-m'),':house'=>date('Y-m-d H')])
            ->all();


        foreach ($query as $v) {
            if ($v['statistics_type'] == 3) {
                $query['month'] = $v;
            }
            if ($v['statistics_type'] == 2) {
                $query['week'] = $v;
            }
            if ($v['statistics_type'] == 1) {
                $query['day'] = $v;
            }
            if ($v['statistics_type'] == 4) {
                $query['house'] = $v;
            }
        }



        //判断本周
        if (!empty($query['week']) && $query['week']['statistics_time'] == $tswk ) {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 2';
            $update_result = \Yii::$app->db->createCommand($updateDay, [
                ':timed' => $tswk,
            ])->execute();

            if ($update_result <= 0) {
                echo '修改注册人数失败';
                return false;
            }

        } else {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (2,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay, [
                'timed' => $tswk,
            ])->execute();

            if ($insert_result <= 0) {
                echo '新增注册人数记录失败';
                return false;
            }
        }

        //判断本月
        if (!empty($query['month'])) {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 3';
            $update_result = \Yii::$app->db->createCommand($updateDay, [
                ':timed' => date('Y-m'),
            ])->execute();

            if ($update_result <= 0) {
                echo '修改注册人数失败';
                return false;
            }

        } else {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (3,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay, [
                'timed' => date('Y-m'),
            ])->execute();

            if ($insert_result <= 0) {
                echo '新增注册人数记录失败';
                return false;
            }
        }

        //判断本日
        if (!empty($query['day'])) {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 1';
            $update_result = \Yii::$app->db->createCommand($updateDay, [
                ':timed' => date('Y-m-d'),
            ])->execute();

            if ($update_result <= 0) {
                echo '修改注册人数失败';
                return false;
            }
        } else {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (1,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay, [
                'timed' => date('Y-m-d'),
            ])->execute();

            if ($insert_result <= 0) {
                echo '新增注册人数记录失败';
                return false;
            }
        }

        //判断是否有这个小时
        if (!empty($query['house'])) {
            $updateDay = 'update mb_add_reg_num set statistics_num = statistics_num+1 where statistics_time= :timed and statistics_type = 4';
            $update_result = \Yii::$app->db->createCommand($updateDay, [
                ':timed' => date('Y-m-d H'),
            ])->execute();

            if ($update_result <= 0) {
                echo '修改注册人数失败';
                return false;
            }
        } else {
            $insertDay = 'INSERT INTO mb_add_reg_num(statistics_type,statistics_time,statistics_num) VALUES (4,:timed,1)';;
            $insert_result = \Yii::$app->db->createCommand($insertDay, [
                'timed' => date('Y-m-d H'),
            ])->execute();

            if ($insert_result <= 0) {
                echo '新增注册人数记录失败';
                return false;
            }
        }
    }


}




class Car
{
    //增加构造函数与析构函数
    function __construct()
    {
        print "构造函数被调用\n";
    }

    function __destruct()
    {
        print "析构函数被调用\n";
    }
}