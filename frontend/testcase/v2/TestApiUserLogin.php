<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/17
 * Time: 13:29
 */

namespace frontend\testcase\v2;

use frontend\business\ApiCommon;
use frontend\testcase\IApiExcute;
use common\models\Client;

use frontend\zhiboapi\v2\ZhiBoUpdateKey;
use common\components\AESCrypt;
use common\components\UsualFunForStringHelper;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;
use linslin\yii2\curl\Curl;
use frontend\testcase\v2\Common;
use frontend\business\JobUtil;

class TestApiUserLogin implements  IApiExcute
{
    public $outInfo;

    public function __construct()
    {
        $this->excute_action(null,$rstData,$error);
        $this->outInfo = $rstData;
    }

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //调用updateKey接口,生成Token
        $isLogin = true;

        $clientsKey = "client_key_count";
        $countClients = \Yii::$app->cache->get( $clientsKey );
        if( !$countClients )
        {
            $query = (new Query())
                ->select(['count(*) as clients'])
                ->from('mb_client')
                ->where("register_type !=1 and client_type=3")
                ->one();
            $countClients = $query['clients'];
            \Yii::$app->cache->set( $clientsKey,$countClients,60*60*2 );
        }

        $loop = 1;
        do{

            $randSQL = "SELECT *  FROM mb_client WHERE register_type !=1 and client_type=3 ORDER BY RAND() LIMIT 1";
            $clientModel = Client::findBySql($randSQL)->one();
            if( !\Yii::$app->cache->get('mb_api_login_'.$clientModel['unique_no'] ) )
            {
                $isLogin = false;
            }
            $loop ++;
        }while( ( $isLogin == false && $loop <= $countClients ) ||  $loop == $countClients );

        if( $isLogin ){
            $rstData = [];
            return $rstData;
        }


        $imdata = [
            'key_word'=>'set_tencent_im',
            'user_id' =>$clientModel['client_id'],
            'nick_name'=>$clientModel['nick_name'],
            'pic'=>'',
        ];
        //注册腾讯用户
        if(!JobUtil::AddImJob('tencent_im',$imdata,$error))
        {
            \Yii::getLogger()->log('im job save error is in test user login:'.$error,Logger::LEVEL_ERROR);
        }

        $ZhiBoUpdateKey  = new ZhiBoUpdateKey();
        $ZhiBoUpdateKey->excute_action(null,$rstData,$error);
        $token = $rstData['data']['token'];
        $sign_key = $rstData['data']['sign_key'];
        $crypt_key = $rstData['data']['crypt_key'];

        $data = [
            'nick_name' => $clientModel['nick_name'],
            'register_type' => $clientModel['register_type'],
            'pic' => $clientModel['pic'] ,
            'unique_no' => $clientModel['unique_no'],
            'validate_code' => '',
            'sex' => $clientModel['sex'],
            'getui_id' => $clientModel['getui_id'],
        ];
        $rstData = Common::PackagingParams($token,$sign_key,$crypt_key,'qiniu_login',$data,$clientModel['device_no'],
                                           $clientModel['device_type'],'json');

        return $rstData;
    }
}