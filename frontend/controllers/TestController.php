<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/13
 * Time: 11:47
 */

namespace frontend\controllers;

use common\components\IOSBuyUtil;
use common\components\IOSBuyGoldsUtil;
use frontend\business\ActivityUtil;
use frontend\business\FansGroupUtil;
use frontend\business\ClientUtil;
use frontend\business\GoldsGoodsUtil;
use frontend\zhiboapi\v2\ZhiBoCreateFansGroup;
use frontend\zhiboapi\v2\ZhiBoFansApplyApprove;
use frontend\zhiboapi\v2\ZhiBoFansApplyList;
use frontend\zhiboapi\v2\ZhiBoFansGroupApply;
use frontend\zhiboapi\v2\ZhiBoFansGroupDismiss;
use frontend\zhiboapi\v2\ZhiBoFansGroupList;
use frontend\zhiboapi\v2\ZhiBoGetClientInfo;
use frontend\zhiboapi\v2\ZhiBoGetFansGroupInfo;
use frontend\zhiboapi\v2\ZhiBoGetGifts;
use frontend\zhiboapi\v2\ZhiBoGroupMemberList;
use frontend\zhiboapi\v2\ZhiBoGroupMemberPrivilege;
use frontend\zhiboapi\v2\ZhiBoKickingFans;
use frontend\zhiboapi\v2\ZhiBoQiNiuCreateLiving;
use frontend\zhiboapi\v2\ZhiBoUpdateFansGroupInfo;
use yii\web\Controller;
use common\components\UsualFunForStringHelper;

use frontend\zhiboapi\v2\ZhiBoGetGoldsAccountInfo;
use frontend\zhiboapi\v2\ZhiBoGetGoldsGoodsList;
use frontend\zhiboapi\v2\ZhiBoDonateGoldsForUserLogin;

use frontend\business\GoldsAccountUtil;  
use frontend\business\IntegralAccountUtil;
use frontend\business\PresentGoldsRuleUtil;
use frontend\zhiboapi\v2\ZhiBoGetGoldsPayParams;
use \frontend\zhiboapi\v2\ZhiBoCancelGoldsPay;
use \frontend\zhiboapi\v2\ZhiBoIosBuyGoldsVerify;
use frontend\business\MultiUpdateContentUtil;

use frontend\business\FrontendCacheKeyUtil;

use  backend\business\SystemParamsUtil;

use yii\log\Logger;

class TestController extends Controller{
    public function actionTest2()
    {
       return  ActivityUtil::GetMBActivityUrl(8, ['unique_no'=>15857172934],'activities');


    }
    public function actionTestzhong()
    {
        ActivityUtil::GetScoreBoardByActivityID(2);
    }
    public function actionGet()
    {
        //创建粉丝群
        $data['data']['group_id'] = 8;
        $aa = new ZhiBoGetFansGroupInfo();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    public function actionUpdate()
    {
        //创建粉丝群
        $data['data']['unique_no'] = 5;
        $data['data']['group_id'] = 8;
        $data['data']['group_name'] = '888群';
        $data['data']['pic'] = '1.jpg';
        $data['data']['advance_notice'] = '9月10号开播，请期待';
        $aa = new ZhiBoUpdateFansGroupInfo();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
        //$list = json_encode($list);
        //var_dump($list);
    }
    public function actionCreate()
    {
        //创建粉丝群
        $data['data']['user_id'] = 2;
        $aa = new ZhiBoCreateFansGroup();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    public function actionIndex()
    {
        //粉丝群
        //获取群成员
        $data['data']['group_id'] = 9;
        $data['data']['page'] = 1;
        $data['data']['page_size'] = 2;

        /*$aa = new ZhiBoGroupMemberList();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);*/
        $query = FansGroupUtil::GetGroupMemberList(7,8,1,10);
        var_dump($query);
    }
    public function actionApply()
    {
        //申请入群
        $data['data']['group_id'] = 8;
        $data['data']['user_id'] = 5;
        $aa = new ZhiBoFansGroupApply();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    public function actionGrouplist()
    {
        //群列表
        $data['data']['user_id'] = 2;
        $aa = new ZhiBoFansGroupList();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
        //$list = json_encode($list);
        //var_dump($list);
    }
    public function actionApplylist()
    {
        /*//群列表
        $data['data']['group_id'] = 8;
        $aa = new ZhiBoFansApplyList();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);*/
        $user_id = 406;
        $query = FansGroupUtil::GetFansApplyList($user_id, $error);
        var_dump($query);
    }
    public function actionAddadmin()
    {
        //添加管理员
        /*$data['data']['group_id'] = 8;
        $data['data']['user_id'] = 5;
        $data['data']['group_member_type'] = 1;
        $aa = new ZhiBoGroupMemberPrivilege();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);*/
        $data['data']['group_id'] = 7;
        $data['data']['user_id'] = [3,4];
        $data['data']['group_member_type'] = 1;
        FansGroupUtil::GroupMemberPrivilege($data, $error);
    }
    public function actionKicking()
    {
        //踢人
        $data['data']['group_id'] = 8;
        $data['data']['user_id'] = 4;
        $aa = new ZhiBoKickingFans();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    public function actionApprove()
    {
        //踢人
        $data['data']['group_id'] = 8;
        $data['data']['user_id'] = 5;
        $data['data']['apply_status'] = 1;
        $aa = new ZhiBoFansApplyApprove();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    public function actionDismiss()
    {
        //解散
        $data['data']['group_id'] = 8;
        $data['data']['user_id'] = 1;
        $aa = new ZhiBoFansGroupDismiss();
        if(!$aa->excute_action($data, $rstData, $error, $extendData= array()))
        {
            print_r($error);
            exit;
        }

        print_r($rstData['data']);
    }
    
     public function actionLogin(){
           //$str = \Yii::$app->cache->get('mb_api_login_'.$unique_no);
           $d = [
                'device_no'=>'352514065605192',
                'verify_code' => '',
                'user_id'=>'4',
                'unique_no' => '18268082898', //  //18268082898  18223663859
                'nick_name' =>'价值',
                'client_type' =>'',
                ];
            $str = serialize($d);
           \Yii::$app->cache->set('mb_api_login_'.$d['unique_no'],$str);
     }
    
    
     public function actionGoldsaccount(){
         
         echo $start_time = date('Y-m-d', time());
         /*
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_golds_account_info",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18223663859",
                    "register_type" => 1,
                ]
        ];
        $golds = new ZhiBoGetGoldsAccountInfo();
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
       // print_r($rstData);
        echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';*/
     }
     
     public function actionGoldsgoodslist(){
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_golds_account_info",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18223663859",
                    "register_type" => 1,
                ]
        ];
        $golds = new ZhiBoGetGoldsGoodsList();
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
       // print_r($rstData);
        echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
     }
     
     
    public function actionDonategolds(){
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "add_golds_for_user_login",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18268082898", 
                    "register_type" => 1,
                ]
        ];
        
        $golds = new ZhiBoDonateGoldsForUserLogin();
        $b = $golds->excute_action($data,$rstData,$error);
        
        print_r('<pre>');
       // print_r($rstData);
        echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
     }
     
      public function actionAddgoldsacount(){
          GoldsAccountUtil::CreateAnGoldsAccountForNewRegisUser(7779);
          
         // IntegralAccountUtil::CreateAnIntegralAccountForNewRegisUser(777999);
      }
      
      public function actionC(){
         //echo $time = strtotime('-1 days');
        //echo  $date = date('Y-m-d',$time);
          //echo $day = date('Y-m-d',time());
          //var_dump( PresentGoldsRuleUtil::GetPresentGoldsRuleByScene('login') );
          
          var_dump(\frontend\business\GoldsAccountLogUtil::GetGoldsAccountLogModelByOneDayOneTime(1, 1));
          /*
         $GoldsAccountLogInfo = new \common\models\GoldsAccountLog();
         $GoldsAccountLogInfo->gold_account_id = 1;
         $GoldsAccountLogInfo->user_id = 1;
         $GoldsAccountLogInfo->device_type = 1;
         $GoldsAccountLogInfo->operate_type = 5;
         $GoldsAccountLogInfo->operate_value = 7000;
         $GoldsAccountLogInfo->before_balance = 2000;
         $GoldsAccountLogInfo->after_balance = 9000;
         
          \frontend\business\GoldsAccountLogUtil::AddGoldsAccountLog($GoldsAccountLogInfo);*/
      }
      
      public function actionQ(){
          $gold_account_id = 1162;
          $user_id = 253500;
          $device_type = 1;
          $operateType = 5;
          $operateValue = 70;
          GoldsAccountUtil::UpdateGoldsAccountToAdd($gold_account_id, $user_id, $device_type, $operateType, $operateValue,$error);
          //GoldsAccountUtil::UpdateGoldsAccountToLessen($gold_account_id, $user_id, $device_type, $operateType, $operateValue,$error);
      }
      
      public function actionX(){
          \Yii::$app->cache->set("xx",'Yy');
          $is = \Yii::$app->cache->get("xx");
          if($is=='Y'){
              echo $is;
          }
      }
     
      public function actionPay(){
        
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_other_golds_pay_params",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18223663859",
                    "register_type" => 1,
                    "pay_type"=>3,
                    "pay_target"=>'prestore',
                    "params"    =>
                     [
                          "gold_goods_id"=>1
                     ]

                ]
        ];
        $golds = new ZhiBoGetGoldsPayParams();
    
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
       // print_r($rstData);
        echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
      }
      
      public function actionWeixinpay(){
        
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_other_golds_pay_params",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18223663859",
                    "register_type" => 1,
                    "pay_type"=>4,
                    "pay_target"=>'prestore',
                    "params"    =>
                     [
                          "gold_goods_id"=>1
                     ]

                ]
        ];
        $golds = new ZhiBoGetGoldsPayParams();
    
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
       // print_r($rstData);
        echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
      }
      
      public function actionCancelpay(){
        
         $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_other_golds_pay_params",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18223663859",
                    "register_type" => 1,
                    "pay_type"=>3,
                    "pay_target"=>'prestore',
                    "bill_no"=>'ZHF-RG-16-10-150014'
                ]
        ];
         
        $golds = new ZhiBoCancelGoldsPay();
    
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
        print_r($rstData);
        //echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
      }
    
      
      public function actionIospay(){
        
         $data = [
            "api_version" => "v2",
             "app_version_inner"=>"",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "ios_buy_golds_verify",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "18268082898",
                    "register_type" => 1,
                    "pay_type"=>6,
                    "pay_target"=>'prestore',
                    "receipt-data"=>'',
                    "gold_goods_id"=>'1'
                ]
        ];
         
        $golds = new ZhiBoIosBuyGoldsVerify();
    
        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
        print_r($rstData);
        var_dump($error);
        //echo(json_encode($rstData));
       // var_dump($error);
       // echo'ok';
      }
      
      public function actionCreaternd(){
           echo UsualFunForStringHelper::mt_rand_str(40);
      }
      
      public function actionM(){
            $id = 4;
            $key = 'present_golds_for_login_'.$id.'_'.date('Y-m-d');
            \Yii::$app->cache->set($key,'Yes'); 
            $value = \Yii::$app->cache->get($key); 
            if($value == "Yes"){
                 echo "Yes OK  ssss";
            }
      }

    public function actionR(){
        $data = [
            "api_version" => "v2",
            "device_type" => "1",
            "device_no" => "352514065605192",
            "action_name" => "get_client_info",
            "has_data" => "1",
            "data_type" => "json",
            "data" =>
                [
                    "unique_no" => "oVKOWs0--oCyzruYUi_cY2tYabxE",
                    "register_type" => 1,
                ]
        ];

        $golds = new ZhiBoGetClientInfo();

        $b = $golds->excute_action($data,$rstData,$error);
        print_r('<pre>');
        print_r($rstData);
        var_dump($error);
    }

    public function actionTios(){
        //$receipt_data = "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";
        //$data = IOSBuyGoldsUtil::GetIosBuyVerify($receipt_data,true);

        //var_export($data);
        echo __DIR__;
       echo dirname(__DIR__);
    }

    public function actionTp(){
      if(  MultiUpdateContentUtil::CheckVersionInCheck(1119990982,'ios_version',1)){
          echo "true";
      }else{
          echo "false";
      }
    }

    public function actionTs(){
        \Yii::$app->session['wxlive_openid'] = "oVKOWs9Swb5dqGW4wKiQ1pbO7H8cdfdffdsa";
        \Yii::$app->session['wxlive_unionid'] = "oVKOWs9Swb5dqGW4wKiQ1pbO7H8cdfdffdsa";

    }

    public function actionShow(){
        echo \Yii::$app->session['wxlive_openid'];
    }

    public function actionSale(){
        $model = GoldsGoodsUtil::GetGoldsGoodsListBySaleType(8);
        var_dump($model);
    }

    public function actionOpen(){
        $qiNiu = new ZhiBoQiNiuCreateLiving();

        $b = $qiNiu->excute_action(null,$rstData,$error);
        print_r('<pre>');
        print_r($rstData);
        var_dump($error);
    }

    public function actionWx(){
        $str = "{\"openid\":\"oAwVuwInA8hFbjnqPCMNmG_diTaw\",\"nickname\":\"王子成\",\"sex\":1,\"language\":\"zh_CN\",\"city\":\"杭州\",\"province\":\"浙江\",\"country\":\"中国\",\"headimgurl\":\"http:\\/\\/wx.qlogo.cn\\/mmopen\\/ajNVdqHZLLBGrFG5YZF8kAAVpQIBMjibM6nSytrdicFBt0hZfaaItviadut2nIJJ4f1iaEmN9OO6YxvOWt0KW8QKMQ\\/0\",\"privilege\":[],\"unionid\":\"oVKOWs68xtiijYJPPOj5gRISzBMI\"}";

        $s = json_decode($str);
        echo $s->unionid;
    }

    public function actionRr(){
        $isOpenLive = \Yii::$app->cache->get("is_open_living");
        if( $isOpenLive ){
            if( $isOpenLive == 2 ){
                $error = '直播暂时关闭';
                \Yii::getLogger()->log('living-msg:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }else{
            $model = SystemParamsUtil::GetSystemParamsByCode('is_open_living');
            if( $model->value1 == 2 ){
                $error = '直播暂时关闭';
                \Yii::getLogger()->log('living-msg:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
        }
    }

    public function actionKey(){
        echo md5('gift_id=199&lucky_gift=1');
    }

    public function actionGift(){

        $gift = new ZhiBoGetGifts();
        $gift->excute_action(null, $rstData,$error);
        var_dump($rstData);
        $key =  FrontendCacheKeyUtil::FRONTEND_V2_ZHIBOGETGIFTS_LIST_ALL;
        echo '--------------------------------------';
        //$data = \Yii::$app->cache->get($key);
        //var_dump($data);
    }

}