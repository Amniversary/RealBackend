<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\FuckActions;


use backend\business\StatisticActiveUserUtil;
use backend\business\StatisticLivingProfitAndTime;
use common\components\AESCrypt;
use common\components\Des3Crypt;
use common\components\DeviceUtil;
use common\components\GameRebotsHelper;
use common\components\getui\GeTuiUtil;
use common\components\IOSBuyUtil;
use common\components\OssUtil;
use common\components\PicHelper;
use common\components\QiNiuUtil;
use common\components\rebots\RebotsUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForNetWorkHelper;
use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\components\wxpay\lib\WxPayAppResults;
use common\components\Yii2ValidateCode;
use common\models\Client;
use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\business\ApiLogUtil;
use frontend\business\AttentionUtil;
use frontend\business\BalanceUtil;
use frontend\business\CarouselUtil;
use frontend\business\ChatFriendsUtil;
use frontend\business\ChatGroupUtil;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\ChatUtil;
use frontend\business\ClientGoodsUtil;
use frontend\business\ClientInfoUtil;
use frontend\business\ClientLivingParamtersUtil;
use frontend\business\ClientQiNiuUtil;
use frontend\business\ClientUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\JobUtil;
use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use frontend\business\NiuNiuGameHelper;
use frontend\business\OtherPayUtil;
use frontend\business\RedPacketsUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateLivingSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddTicket;
use frontend\business\TicketToCashUtil;
use Green\Request\V20160621\ImageDetectionRequest;
use OSS\OssClient;
use Pheanstalk\PheanstalkInterface;
use Pili\Stream;
use yii\base\Action;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

/**
 * Class TestLxyAction
 * @package frontend\controllers\FuckActions
 */
class TestLxyAction extends Action
{

    private function Resign($url_str)
    {
        $items = explode('?',$url_str);
        if(!is_array($items) || count($items) < 2)
        {
            return $url_str;
        }
        $params = explode('&',$items[1]);
        if(count($params) < 2)
        {
            return $url_str;
        }
        $signParams = [];
        foreach($params as $oneParam)
        {
            $subItems = explode('=',$oneParam);
            if(!is_array($subItems) || count($subItems) !== 2)
            {
                continue;
            }
            $signParams[$subItems[0]] = $subItems[1];
        }
        unset($signParams['sign']);
        $signStr = ActivityUtil::GetActivitySign($signParams);
        $signParams['sign']=$signStr;
        $tempStr = '';
        foreach($signParams as $key => $v)
        {
            $tempStr .= sprintf('%s=%s&',$key,$v);
        }
        if(!empty($tempStr))
        {
            $tempStr = substr($tempStr,0,strlen($tempStr)-1);
        }
        $url_str = sprintf('%s?%s',$items[0],$tempStr);
        return $url_str;
    }

    public function run()
    {
        $arr = ['json' => 'has-data'];
        $rst = json_encode($arr);
        UsualFunForNetWorkHelper::HttpsPost('http://www.test.cn/fuck/bh', $rst,  ['OpenId:111', 'AppId:d222']);
        exit;
        $str = 'a:7:{s:9:"device_no";s:36:"D7CB7EC5-EE22-4FF4-B745-46F8712E6C8C";s:7:"user_id";i:238924;s:11:"verify_code";N;s:11:"client_type";i:2;s:9:"unique_no";s:11:"15669097386";s:9:"nick_name";s:6:"甜甜";s:8:"is_inner";s:1:"2";}';
        $info = unserialize($str);
        var_dump($info);
        var_dump('ttttt');
        exit;
        $uniqueNo = '15669097386';
        if(ApiCommon::GetLoginInfo($uniqueNo,$loginInfo,$error))
        {
            var_dump($loginInfo);
        }
        else
        {
            var_dump($error);
        }
        exit;
        $aa = 'sjaofjas@#$@#$2就搜附近偶师傅案说法为dddddd';
        $rst = $aes->encrypt($aa);
        var_dump($rst);
        $rst = '0K1HDHeXV0eI3pcPF6RzpRG00SwET1bLQQaUAs2ULoRoULMjC2tPiokwZXXSRNSczXGwbbOR8B/dgM9LBLPzJQ==';

        $rst = $aes->decrypt($rst);
        var_dump($rst);

//        $key = 'yc)H%un3=&CTnyKUK+Yj7&Hx0~In`V6l';
//        $str = 'i+cnpToi8Yu2vVGxRVHmJVyBPUimoVmDFpDJzull8Kb+0LRZ7rqcjtZ6ESn4bvv7yY6aO2zdpeYrEhlhkJoIN4XybTQ7bNxbvX6MX1Re3WIX4tRSZ2dgF4I0M1VpSJUGSDZkgJ9RbSAZ+26JeChj3qujMS9W68MBOzvjbSy25jcRbUvvvgc4zAnDPzfdrB/RrxZhSsxa1FHXaq6GD2KRbKUH8t4XtY2O2RrIdqqHzAp2gjubUdcx8eEEHxp7CVXGK3OIM2W/cP6L7aP+xyFbD2tI+NY00guFlyiNaT83WZsVGwErqYB7e7xxR6mE0M4yDyWWnbnLvW8fleBPSqsgmQ==';
//        $rst = $aes->decrypt($str);
//        var_dump($rst);
        exit;
        $dir = \Yii::$app->getBasePath().'/web/tttt';
        $picList = [];
        $files = scandir($dir);
        $picStrList = '';
        foreach($files as $file)
        {
            $items = explode('\\',$file);
            $len = count($items);
            $file_name =$items[$len -1];
            if(strpos($file_name,'.mp4') === false)
            {
                continue;
            }
            $file = $dir.'/'.$file;
            $fName = str_replace('.mp4','',$file_name);
            $suffix = 'mp4';
            $picUrl = '';
            $error = '';
            if(!OssUtil::UploadFile($fName,$suffix,'ios-check',$file,$picUrl,$error))//mibo-tuiguang
            {
                var_dump($error);
                exit;
            }
            $picStrList .= $picUrl."\r\n";
            $picList[]=$picUrl;
        }
        var_dump($picList);
        $fileStore = $dir.'/picurl.txt';
        file_put_contents($fileStore,$picStrList);
        exit;
        try
        {
            $ac = new Client();
            $ac->getui1_id = 'ddddddddddddddf';
            if(!$ac->save())
            {
                $error = '更新个推id失败';
                \Yii::getLogger()->log($error.' :'.var_export($ac->getErrors(),true),Logger::LEVEL_ERROR);
                var_dump($error);
                return false;
            }

        }
        catch(Exception $e)
        {
            var_dump($e->getMessage());
            exit;
        }
        var_dump('ok');
        exit;
        $unique_no = '18820005847';
        $query = new Query();
        $logInfo = $query->select(['client_id as user_id','device_no','ifnull(vcode,\'\') as verify_code','unique_no','nick_name','client_type'])
            ->from(['mb_client'])
            ->where(['unique_no'=>$unique_no])
            ->one();
        var_dump($logInfo);
        exit;
        $str = 'a:3:{s:5:"token";s:40:"981935e849c9d3180f83675efb0083c96dbac086";s:8:"sign_key";s:32:"h74HrZnV&0GlUM9P!dt0p@X=us=8zabu";s:9:"crypt_key";s:32:"1vIi@OuW^Klr*Fn4paPGJZUvaNsMVarN";}';
        var_dump(strlen($str));
        exit;
        $key = 'my_api_key_61d77f9a8d74e4b8f008ab58dc014a10db0fb853';
        $rst = \Yii::$app->cache->get($key);
        var_dump($rst);
        exit;
        $unique_op_no = UsualFunForStringHelper::CreateGUID();
        var_dump($unique_op_no);
        exit;

        $key_word = '21331';
        $page_no = 1;
        $page_size = 20;
        $user_id = 5;
        $friendsList = ClientUtil::SearchUser($key_word,$page_no,$page_size,$user_id);
        var_dump($friendsList);
        exit;
        $user_id = 4;
        $pageNo = 1;
        $pageSize = 10;
        $result = AttentionUtil::GetAttentionLiving($user_id,$pageNo,$pageSize);
        var_dump($result);
        exit;

        $card = [3,12,13,5,6];
        $cow = NiuNiuGameHelper::JudgeCowCow($card);
        var_dump($cow);
        exit;
        $device_no = '99000660749632';
        $rst = DeviceUtil::IsErrorDevice($device_no);
        var_dump($rst);
        exit;
        GoldsAccountUtil::CreateAnGoldsAccountForNewRegisUser(1652);
        exit;


        $rst = GameRebotsHelper::GetRebots(5);
        var_dump($rst);
        exit;
        $rebotsList = [
            [
                'client_id'=>'3',
                'nick_name'=>'王有才',
                'pic'=>'http://q.qlogo.cn/qqapp/1105405817/CC0E513C3DA8C30ACBFB33052DC9B387/100',//小图标即可
                'sex'=>'男'
            ],
            [
                'client_id'=>'5',
                'nick_name'=>'查了',
                'pic'=>'http://mbpic.mblive.cn/client-pic/ocimg_5788b7d4b7116.jpg',//小图标即可
                'sex'=>'男'
            ],
        ];
        $rst = GameRebotsHelper::GenRebots($rebotsList,$error);
        if($rst === false)
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;


        if(!JobUtil::GetCustomJob('gameRebotBeanstalk','',$rebot,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump($rebot);
        exit;




        $name = RebotsUtil::GetNickName();
        var_dump($name);
        exit;

        $url = 'http://q.qlogo.cn/qqapp/1105405817/AAC327C748BB96F83381111D33257EF4/100';
        if(!PicHelper::SavePicFromWeb($url,$fileOut,$error))
        {
            var_dump('error:'.$error);
            exit;
        }
        var_dump($fileOut);
        exit;
        $key = 'mb_api_login_oVKOWs3ChmyhZB3anMLyBtvP4hvI';
        $key = 'mb_api_login_oXhDOwkMSSefEgXg7cjJBwaYydEY';
        $rst = \Yii::$app->cache->get($key);
        var_dump($rst);
        exit;
        $user_list = [
                [
                    'cid' => '8732115235baf728f65751fa9966f9c9',
                    'alias' => '18820005847',
                ],
        ];
        $text_content = '5-451-@TGS#3N3GEQBEF-3221-hh';
        $showContent = '您的好友哈戳戳正在直播，快去瞅瞅吧！';
        if(!GeTuiUtil::PushListMessage($showContent,$text_content,$user_list,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('send ok');
        exit;
        $sqltest = 'update mb_activity_prize set last_number = if(last_number-1<0,0,last_number-1) where prize_id=:pid';
        $rst = \Yii::$app->db->createCommand($sqltest,[':pid'=>1])->execute();
        var_dump($rst);
        \Yii::getLogger()->log('rst:'.($rst > 0 ? 'ok':'error'),Logger::LEVEL_ERROR);
        exit;
        $users = LivingHotUtil::GetLivingAudienceFromContribution(56);
        var_dump($users);
        exit;
        $sqltest = 'select * from mb_client';
        $data = \Yii::$app->db->createCommand($sqltest)->queryAll();
        var_dump($data);
        exit;
        //
        $rate_arr = [
            0=>20,
            1=>60,
            2=>10
        ];
        $rand_max = count($rate_arr)-1;
        $rand_data =  mt_rand(0,$rand_max);      //随机取出某个满足条件的概率值
        $k = $rand_data;
        var_dump($k);
        $rand_data = $rate_arr[$rand_data];
        $rate_arr = [];
        $rate_arr[$k] = $rand_data;

        $rate_arr[] = 100-$rand_data;  //未出现的机率 百分比
        var_dump($rate_arr);
        exit;
        $url = 'http://www.baidu.com?activity_id=3&unique_no=erdrseeed&rand_str=sjowjofw&sign=ttttttt';
        $url = $this->Resign($url);
        var_dump($url);
        exit;
        $list = StatisticActiveUserUtil::TotalLivingMaster(392);
        var_dump($list);
        exit;
        $list = LivingUtil::GetNewestLivingList('1',-2);
        var_dump($list);
        exit;
        $result = AttentionUtil::GetAttentionLiving('1',1);
        var_dump($result);
        exit;

        $cid3='2d788533f5c97c223348847cde2bcdbb';//安卓自己
        //$cid2='3bda89c52008ce554923bfe62452e6ec';
        $cid4 = '269bfda1fefcc984086d2f8089e18b98';//安卓新手机
        //9faa6cea1cb02a692433abb1ec3105a7
        $userList =[$cid4=>'10953522'];
        //@TGS#3CUSIKBEH  5-137-424-469-猫王
        if(!GeTuiUtil::PushListMessage('您好好友[微笑的苹果]开始直播了，快去瞅瞅！','5-137-424-469-猫王',$userList,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;
        //$cid3='3bda89c52008ce554923bfe62452e6ec';
        $cid3 = '2d788533f5c97c223348847cde2bcdbb';
        $cid3='269bfda1fefcc984086d2f8089e18b98';
        $alias= '10953522';
        //$alias = '13337861';
        if(!GeTuiUtil::BatchBindClientAlias([['cid'=>$cid3,'alias'=>$alias]],$error))
        {
            var_dump($error);
            return;
        }
        var_dump('ok1');
        exit;


        //打赏者汇总队列
        $sum_data = [
            'week' => date('Y-W'),
            'send_user_id' => 4,
            'gift_value' => 20,
        ];
        if(!JobUtil::AddCustomJob('livingTicketBeanstalk','send_gift_sum',$sum_data,$error))
        {
            var_dump($error);
            return false;
        }
        var_dump('ok');
        exit;
        $unique_no = 'CE3FB4A468CDC164158580206436D60E';
        if(!ApiCommon::GetLoginInfo($unique_no,$loginInfo, $error))
        {
            var_dump($error);
            return false;
        }
        var_dump($loginInfo);
        exit;
        $v = 1.05;
        var_dump(sprintf('%d',$v));
        exit;
        $unique_no = '15110008732';
        $rst = ClientLivingParamtersUtil::GetClientLivingParamtersByUniqueNo($unique_no,$error);
        if($rst ===false)
        {
            var_dump($error);
            exit;
        }
        var_dump($rst);
        exit;
        $query = (new Query())
            ->select(['client_id as user_id','unique_no','register_type','nick_name','level_no as level_id'])
            ->from('mb_client bc')
            ->innerJoin('mb_client_active ca','bc.client_id = ca.user_id')
            ->join('join','(SELECT ROUND(RAND() * (SELECT MAX(client_id) FROM `mb_client`)) + 100 AS id) AS t2')
            ->where(['and','status = 1','level_no < 15','bc.client_id between t2.id - 100 and t2.id'])
            ->orderBy('bc.client_id ASC')
            ->limit(55)
            ->all();
        exit;
        $n = 3;
        $sum = 4;
        $index_max = 0;
        $rst = UsualFunForStringHelper::GenRandRePacketsData($n,$sum,$index_max,$error);
        if($rst == false)
        {
            var_dump($error);
            exit;
        }
        var_dump($rst);
        var_dump($index_max);
        var_dump($rst[$index_max]);
        exit;
        $data = [
            [
                'id'=>'11111',
                'you'=>'23333',
                'wos'=>'334444'
            ],
            [
                'id'=>'555555',
                'you'=>'666666',
                'wos'=>'777777'
            ]
        ];
        $rst = [];
        foreach($data as $d)
        {
            $d['you']='hhhhhhhhhhhhhhhhhh';
            $rst[] = $d;
        }
        var_dump($rst);
        exit;
        //9959C51B614EC26F24736EECD37863D8

        set_time_limit(0);
        $data = [
            'name'=>'dddddd',
            'sex'=>'dsfsf'
        ];
        $jobId =\Yii::$app->peopleBeanstalk->putInTube('tube',$data);
        var_dump($jobId);
        $jobId =\Yii::$app->peopleBeanstalk->putInTube('tube',$data,PheanstalkInterface::DEFAULT_DELAY,5);
        var_dump($jobId);
        $jobId =\Yii::$app->peopleBeanstalk->putInTube('tube',$data);
        var_dump($jobId);
        exit;
        $data = [
            '555'=>'213',
        ];
        var_dump(strlen(json_encode($data)));
        var_dump(strlen(serialize($data)));
        exit;
        $client_id = 279;
        $qiniuInfo = ClientQiNiuUtil::GetQiNiuInfoByClientId($client_id);
        $qInfo = json_decode($qiniuInfo->qiniu_info,true);
        $stream_id= $qInfo['id'];
        $statusRst = QiNiuUtil::QueryStatus($stream_id,$error);
        if($statusRst === false)
        {
            var_dump($error);
            exit;
        }
        var_dump($statusRst);
        exit;
        $send_data = [
            'user_num_1' => 33,
            'statistic_time_1' => '2016-08-01',
            'statistic_type_1' => 1,
            'user_num_2' => 44,
            'statistic_time_2' => '2016-08-01',
            'statistic_type_2' => 2
        ];
        $sign_data = ApiLogUtil::SetApiLogSign($send_data);
        $send_data['rand_str'] = $sign_data['rand_str'];
        $send_data['time'] = $sign_data['time'];
        $send_data['p_sign'] = $sign_data['p_sign'];
        $url = 'http://manage1.mblive.cn/site/statisticapilog';
        $url = 'http://manage1.mblive.cn/mytest/test';
        $return_data = UsualFunForNetWorkHelper::HttpsPost($url,$send_data); //统计完成向正式服务器发送数据
        var_dump($return_data);
        echo '<br/>';

        for($i=0;$i<strlen($return_data);$i++){
            var_dump(dechex(ord($return_data[$i])));
        }
        echo '<br/>';
        $return_data = UsualFunForNetWorkHelper::HttpGet($url);
        for($i=0;$i<strlen($return_data);$i++){
            var_dump(dechex(ord($return_data[$i])));
        }
        exit;
        var_dump(sha1('rand_str=d7552621b70e4b45ee110ad268ef6a8091f2fced&time=2016-08-01&key=Wg1Y3PRwrw3NOwMToQq5drGpv9uw7alPyMbVc8uL'));
        exit;
/*        $uniqueNo = '18820005847';
        $model = ClientLivingParamtersUtil::GetClientLivingParamtersByUniqueNo($uniqueNo,$error);
        var_dump($model);
        exit;*/
        //$ak = parse_ini_file("aliyun.ak.ini");
//请替换成你自己的accessKeyId、accessKeySecret
        $iClientProfile = \DefaultProfile::getProfile("cn-hangzhou", ALIYUN_ACCESS_ID, ALIYUN_ACCESS_SECRET); // TODO

        $client = new \DefaultAcsClient($iClientProfile);
// 图片检测
        $request = new ImageDetectionRequest();
//设置参数
//设置为同步调用
        $request->setAsync("false");
//设置图片链接
//同步只支持单张图片
        //
        $request->setImageUrl(json_encode(array("http://img4.imgtn.bdimg.com/it/u=4200526803,1263180125&fm=21&gp=0.jpg")));
//设置检测的场景
//同步支持多个场景同时识别
//porn: 黄图检测
//ocr： ocr文字识别
//illegal: 暴恐渉政识别
//场景请根据自己业务来选择, 同步调用一次不建议过多个场景，可能超时概率会很高
        $request->setScene(json_encode(array("porn")));
        try {
            $response = $client->getAcsResponse($request);
            print_r($response);
            //返回状态值成功时进行处理
            if("Success" == $response->Code){
                $imageResults = $response->ImageResults->ImageResult;
                foreach ($imageResults as $imageResult) {
                    //黄图结果处理
                    $pornResult = $imageResult->PornResult;
                    if(!empty($pornResult)) {
                        //打印黄图分值,0-100
                        print_r($pornResult->Rate);
                        print_r("\n");
                        //打印参考建议, 0表示正常，1表示色情，2表示需要review
                        print_r($pornResult->Label);
                        print_r("\n");
                    }
                    //暴恐渉政结果处理
                    $illegalResult = $imageResult->IllegalResult;
                    if(!empty($illegalResul)) {
                        //打印暴恐渉政分值,0-100
                        print_r($illegalResult->Rate);
                        print_r("\n");
                        //打印参考建议, 0表示正常，1表示暴恐渉政，2表示需要review
                        print_r($illegalResult->Label);
                        print_r("\n");
                    }
                    //ocr结果处理
                    //打印ocr结果
                    if(!empty($ocrResults)) {
                        $ocrResult = $imageResult->OcrResult;
                        print_r($ocrResult->Text);
                        print_r("\n");
                    }
                }
            }
        } catch (Exception $e) {
            print_r($e);
        }
        exit;
        $user_id=22786;
        $balance = BalanceUtil::GetUserBalanceByUserId($user_id);
        if(!BalanceUtil::CheckBalance($balance,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;
        $user_id=27258;
        $info = ClientUtil::GetClientById($user_id);
        var_dump($info);
        exit;

        $title='livingtestoVKOWs5ExTIJG-tahfFl8jJLFQGst';
        \Yii::getLogger()->log('title'.$title,Logger::LEVEL_ERROR);
        $info = QiNiuUtil::CreateStream($title,$error);
        if($info === false)
        {
            var_dump($error);
            exit;
        }
        var_dump(json_encode($info));
        $st = new Stream(null,$info);
        var_dump($st->rtmpLiveUrls());
        var_dump($st->httpFlvLiveUrls());
        var_dump($st->hlsLiveUrls());
        exit;
        $error = [
            'errno'=>'1105',
            'errmsg' =>'直播已结束'
        ];
        throw new Exception($error);
        exit;

        $page_size = 5;
        $living_id = 99;
        $info = LivingHotUtil::GetLivingAudienceFromContribution($living_id,$page_size);;
        var_dump($info);
        exit;
        $living_id = 99;
        $data = LivingUtil::GetFinishLivingInfo($living_id);
        var_dump($data);
        //$heart_count = $data['heart_count'];
        //$to_heart_dis_time = SystemParamsUtil::GetSystemParam('heart_dis_time',false,'value1'); //心跳间隔时间
        $s = (empty($data['finish_time'])?time():strtotime($data['finish_time']))- strtotime($data['create_time']);
        var_dump($s);
        $timeStr = UsualFunForStringHelper::GetHHMMSSBySeconds($s);
        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data']=[
            'attend_user_count'=>$data['person_count_total'],
            'tickets_num'=>$data['tickets_num'],
            'living_time'=>$timeStr
        ];
        var_dump($rstData);
        exit;
        $t1 = strtotime('2016-07-26 13:38:55');
        $t2 = strtotime('2016-07-26 13:39:11');
        $formate_time = UsualFunForStringHelper::GetHHMMSSBySeconds($t2 - $t1);
        var_dump($formate_time);
        exit;
        $page_no = 1;
        $page_size = 10;
        $data = LivingHotUtil::GetHotLivingList($page_no,$page_size);
        var_dump($data);
        exit;

        $unique_no='18820005847';
        $info = LivingUtil::GetLivingAndUserInfoByUniqueId($unique_no);
        var_dump($info);
        exit;
        $key = 'qiniu_living_15888888888';
        $str='{"id":"z1.mibolive.livingtest15888888888","createdAt":"2016-07-22T10:19:21.736+08:00","updatedAt":"2016-07-22T10:19:21.736+08:00","title":"livingtest15888888888","hub":"mibolive","disabledTill":0,"disabled":false,"publishKey":"196d08fadbfc4d3e","publishSecurity":"static","hosts":{"publish":{"rtmp":"pili-publish.mblive.cn"},"live":{"hdl":"pili-live-hdl.mblive.cn","hls":"pili-live-hls.mblive.cn","http":"pili-live-hls.mblive.cn","rtmp":"pili-live-rtmp.mblive.cn","snapshot":"pili-live-snapshot.mblive.cn"},"playback":{"hls":"10001vl.playback1.z1.pili.qiniucdn.com","http":"10001vl.playback1.z1.pili.qiniucdn.com"},"play":{"http":"pili-live-hls.mblive.cn","rtmp":"pili-live-rtmp.mblive.cn"}}}';
        $qiniu_info = \Yii::$app->cache->get($key);
        if($qiniu_info === false)
        {
            $error = '七牛直播信息丢失';
            var_dump($error);
            return false;
        }
        var_dump($qiniu_info);
        exit;



        $unique_no = 'sfdasfasfdasfdasdff';
        $register_type = '2';
        ClientUtil::RegisterUser($unique_no,'sdsfsd2wfsfwf',[],'sdffsfsdf',$error);
        var_dump('ok');
        exit;
        if(!ClientGoodsUtil::GetInsideBuy('1',$outInfo,$error))
        {
            var_dump($error);
            return false;
        }
        var_dump($outInfo);
        exit;
        $client_id = 34211;
        $pic = 'http://mbpic.mblive.cn/user/6b3ab796ce9d88ab413798702ffc3f17.jpg';
        if(!JobUtil::AddPicJob('deal_client_pic',['client_id'=>$client_id,'pic'=>$pic],$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;
        $url = 'http://mbpic.mblive.cn/user/59a7afb8806d347c045de2be5c556675.jpg';
        $data = UsualFunForNetWorkHelper::HttpGetImg($url,$cnt_type);
        var_dump($cnt_type);
        if($data === false)
        {
            var_dump('无法访问图片');
        }
        else
        {
            $file = '';
            if(PicHelper::SavePicFromWeb($url,$file,$error))
            {
                var_dump($file);
            }
            else
            {
                var_dump('图片保存失败：'.$error);
            }
            var_dump('是图片');
        }
        exit;



        $credentials = new \Qiniu\Credentials(ACCESS_KEY, SECRET_KEY); #=> Credentials Object
        $hub = new \Pili\Hub($credentials, HUB); # => Hub Object

// Create a new Stream
        try {

            $title           = 'my-mibo-1';     // optional, auto-generated as default
            $publishKey      = null;     // optional, auto-generated as default
            $publishSecurity = null;     // optional, can be "dynamic" or "static", "dynamic" as default

            $stream = $hub->createStream($title, $publishKey, $publishSecurity,$error); # => Stream Object
            if($stream === false)
            {
                var_dump($error);
            }
            echo "createStream() =>\n";
            var_export($stream);
            echo "\n\n";

        } catch (Exception $e) {
            echo 'tttt-error';
            exit;
            echo 'createStream() failed. Caught exception: ',  $e->getMessage(), "\n";
        }
        exit;
        echo '<br>';
        echo '<br>';
        echo '<br>';
        try {

            $streamId = $stream->id;

            $stream = $hub->getStream($streamId); # => Stream Object

            echo "getStream() =>\n";
            var_export($stream);
            echo "\n\n";

        } catch (Exception $e) {
            echo "getStream() failed. Caught exception: ",  $e->getMessage(), "\n";
        }
        echo '<br>';
        echo '<br>';
        echo '<br>';
        try {

            $marker       = NULL;      // optional
            $limit        = NULL;      // optional
            $title_prefix = NULL;      // optional
            $status       = NULL;      // optional, "connected" only

            $result = $hub->listStreams($marker, $limit, $title_prefix, $status); # => Array

            echo "listStreams() =>\n";
            var_export($result);
            echo "\n\n";

        } catch (Exception $e) {
            echo "listStreams() failed. Caught exception: ",  $e->getMessage(), "\n";
        }
        exit;
        $time1 = microtime(true);
        sleep(1);
        $time2 = microtime(true);
        var_dump($time2 - $time1);
        exit;
/*        $url = 'http://tva4.sinaimg.cn/crop.0.1.640.640.180/d6b9183bjw8eophbotr26j20hs0humxn.jpg';
        $data = UsualFunForNetWorkHelper::HttpGetImg($url,$cnt_type);
        var_dump($cnt_type);
        if($data === false)
        {
            var_dump('无法访问图片');
        }
        else
        {
            var_dump($url);
        }
        exit;*/
        $client_id = 3;
        $pic_url = 'http://mibodemo.oss-cn-shanghai.aliyuncs.com/user/5c39f53904f28035e8331729f7f9e7c9.jpg';
        if(!ClientUtil::GenClientPicThumb($client_id,$pic_url,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;
        $file = 'E:/xampp/htdocs/xuexinbao_weixin/zhibao/frontend/web/upload/web_pic_57887d9f3bf19.jpg';
        if(ClientUtil::IsShouldDealPic($file))
        {
            if(!PicHelper::img2ThumbMuilt($file,$dst_imgs,[640,240,60],[640,240,60],[],[],$error))
            {
                var_dump($error);
            }
            else
            {
                var_dump($dst_imgs);
            }
        }
        exit;

        $url='http://q.qlogo.cn/qqapp/1105405817/CC0E513C3DA8C30ACBFB33052DC9B387/100';
        $data = UsualFunForNetWorkHelper::HttpGetImg($url,$cnt_type);
        var_dump($cnt_type);
        if($data === false)
        {
            var_dump('无法访问图片');
        }
        else
        {
            $file = '';
            if(PicHelper::SavePicFromWeb($url,$file,$error))
            {
                var_dump($file);
            }
            else
            {
                var_dump('图片保存失败：'.$error);
            }
            var_dump('可以访问的图片');
        }
        $url='http://mblive.oss-cn-hangzhou.aliyuncs.com/user/65cf850e5f25159f16b43fe0daebccb2.jpg';
        $data = UsualFunForNetWorkHelper::HttpGetImg($url,$cnt_type);
        var_dump($cnt_type);
        if($data === false)
        {
            var_dump('无法访问图片');
        }
        else
        {
            $file = '';
            if(PicHelper::SavePicFromWeb($url,$file,$error))
            {
                var_dump($file);
            }
            else
            {
                var_dump('图片保存失败：'.$error);
            }
            var_dump('可以访问的图片');
        }
       // $obj = simplexml_load_string($data);
       // var_dump($obj);
        exit;
        $file = $_SERVER['DOCUMENT_ROOT'].'/57c8a563a10fcbc4e6cb2bf3970c5205.jpg';
        file_put_contents($file,$data);
        var_dump($file);
        exit;
        $file = 'http://q.qlogo.cn/qqapp/1105405817/CC0E513C3DA8C30ACBFB33052DC9B387/100';
        $ext = PicHelper::fileext($file);
        var_dump($ext);
        exit;
        $rst_file = $_SERVER['DOCUMENT_ROOT'].'/test_rst.jpg';
        $rst_file1 = $_SERVER['DOCUMENT_ROOT'].'/test_rst1.jpg';
        $rst_file2 = $_SERVER['DOCUMENT_ROOT'].'/test_rst2.jpg';
        $file_pic = $_SERVER['DOCUMENT_ROOT'].'/test1.jpg';
        $size = getimagesize($file_pic);
        var_dump($size);
        $width = $size[0];
        $height = $size[1];
        //if($width > 640)
        //{
        if(!PicHelper::img2Thumb($file_pic,$rst_file,640,640))
        {
            var_dump('gen pic error');
            exit;
        }
        if(!PicHelper::img2Thumb($file_pic,$rst_file1,240,240))
        {
            var_dump('gen pic error');
            exit;
        }
        if(!PicHelper::img2Thumb($file_pic,$rst_file2,60,60))
        {
            var_dump('gen pic error');
            exit;
        }
        //}
        echo 'ok';
        exit;

        $unique_no = 'tsfewsfwfsdfsdfsfdf';
        $client_id = 256;
        ClientUtil::UpdateUniqueNo($unique_no,$client_id);
        var_dump('ok');
        exit;
        $str ='<xml><openid>oonAev69PL5Dpa4mcuiGEt1UgcIE</openid><sub_mch_id>1217189101</sub_mch_id><return_code>SUCCESS</return_code><time_end>20160713210516</time_end><mch_id>1357976702</mch_id><trade_type>APP</trade_type><sign>40F1F803E2A69B4BE615E93EFB0B6F4A</sign><cash_fee>600</cash_fee><is_subscribe>N</is_subscribe><bank_type>CFT</bank_type><out_trade_no>ZHF-RG-16-07-120078</out_trade_no><transaction_id>4000692001201607128859511312</transaction_id><total_fee>600</total_fee><appid>wxf91a7e689f98d15c</appid><noncestr>20150531233956996351</noncestr><result_code>SUCCESS</result_code></xml>';
        $rst = WxPayAppResults::Init($str);
        var_dump($rst);
        exit;
        set_time_limit(0);
        $sqltest = 'select client_id,unique_no from mb_client where register_type=3';
        $data = \Yii::$app->db->createCommand($sqltest)->queryAll();
        $url = 'https://api.weibo.com/2/account/get_uid.json?access_token=%s';
        $update_sql = 'update mb_client set xinlang_uid=:uid where client_id=:cid';
        $ok_count = 0;
        $fail_count = 0;
        foreach($data as $one)
        {
            $rst_url = sprintf($url,$one['unique_no']);
            $d = UsualFunForNetWorkHelper::HttpGet($rst_url);
            $rst = json_decode($d,true);
            $uid = 'overtime';
            if(isset($rst['uid']))
            {
                $uid = $rst['uid'];
                $ok_count ++;
            }
            else
            {
                $fail_count ++;
            }
            $r_t = \Yii::$app->db->createCommand($update_sql,[':uid'=>$uid,':cid'=>$one['client_id']])->execute();
            if($r_t <= 0)
            {
                echo 'client_id:'.$one['client_id'].'excute_error uid:'.$uid.'<br/>';
            }
        }
        echo '<br/> ok_count:'.$ok_count.' fail_count:'.$fail_count.'<br/>';
        exit;


/*        $key = sprintf('apple_pay_%s_%s',40,date('Y-m-d'));
        \Yii::$app->cache->delete($key);
        var_dump('ok');
        exit;*/

        $user_id = 5;
        $payMoney = 20;
        $rst = OtherPayUtil::IsOverPay($user_id,$payMoney);
        var_dump($rst);
        exit;
        $loginInfo['user_id']=61;
        $page_no = 1;
        $page_size = 100;
        $user_id = 26239;
        $friendsList = ChatFriendsUtil::GetContributionBoard($user_id,$page_no,$page_size,$loginInfo['user_id']);
        var_dump($friendsList);
        exit;

        $fields = [
            'client_id'=>'client_id as user_id',
            'nick_name'=>'nick_name',
            'client_no'=>'client_no',
            'alipay_no'=>'alipay_no',
            'pic'=>'pic',
            'sex'=>'sex',
            'age'=>'age',
            'city'=>'city',
            'cash_rite'=>'cash_rite',
            'level_id'=>'level_id',
            'level_pic'=>'ls.level_pic',
            'font_size'=>'font_size',
            'color'=>'color',
            'level_bg'=>'level_bg',
            'sign_name'=>'sign_name',
            'send_ticket_count'=>'send_ticket_count',
            'attention_num'=>'attention_num',
            'funs_num'=>'funs_num',
            'ticket_count_sum'=>'ticket_count_sum',
            'ticket_count'=>'ticket_count',
            'ticket_real_sum'=>'ticket_real_sum',
            'today_ticket_num'=>'IFNULL((select real_ticket_num from mb_time_livingmaster_ticketcount where livingmaster_id = :ld and hot_type = 1 and statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\')),0) as today_ticket_num',
            'bean_balance'=>'bean_balance',
            'virtual_bean_balance'=>'virtual_bean_balance',
            'is_bind_weixin'=>'is_bind_weixin',
            'is_bind_alipay'=>'is_bind_alipay',
            'is_contract'=>'is_contract',
            'is_centification'=>'is_centification as real_validate'
        ];
        $filesInput =[];
        $filedRst = [];

        if(!empty($filesInput))
        {
            foreach($filesInput as $field)
            {
                if(!isset($fields[$field]))
                {
                    $error = '请求的字段不存在';
                    return false;
                }
                $filedRst[] = $fields[$field];
            }
        }
        else
        {
            foreach($fields as $field)
            {
                $filedRst[] = $field;
            }
        }
        $userInfo = [];
        $to_user = 56;
        $self_user_id =56;
        if(!ClientInfoUtil::GetUserData($filedRst,$to_user,$self_user_id,$userInfo,$error))
        {
            var_dump($error);
            return false;
        }
        var_dump($userInfo);
        exit;
        $status =1;
        $carousel = CarouselUtil::GetCarouselList($status);
        $carouselInfo = CarouselUtil::GetFormateCarouselList($carousel);
        var_dump($carouselInfo);
        exit;

        $living_id = 2;
        $user_id= 4;
        if(!ChatGroupUtil::QuitRoom($living_id,$user_id,'1',$error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        $rst = \Yii::$app->cache->set('aaa','11111');
        var_dump($rst);
        $rst = \Yii::$app->cache->get('aaa');
        var_dump($rst);
        exit;

        $room_id = 105;
        if(!ChatPersonGroupUtil::AddUserToGroup($room_id,4,3,$error,$groupUser))
        {
            var_dump($error);
            exit;
        }
        var_dump($groupUser);
        exit;
        $living_id = 98;
        ChatPersonGroupUtil::CreatelLivingAddRebotsMulti($living_id,'1');
        var_dump('ok');
        exit;
        $key = 'test';
        $v = \Yii::$app->cache->set($key,'dddddddddddddddd');
        $v1 = \Yii::$app->cache->get($key);
        var_dump($v1);
        //$v = \Yii::$app->cache->get($key);
        //var_dump(phpinfo());
        var_dump($v);
        exit;
        $client_id = 4;
        $msg = '您发的未领取红包，已退回，豆数:4个';
        if(!TimRestApi::openim_send_custom_msg($client_id,$msg,$error))
        {
            \Yii::getLogger()->log('发送腾讯云通知消息异常：'.$error,Logger::LEVEL_ERROR);
            var_dump($error);
            exit;
        }
        var_dump('ok');
        exit;
        $data = RedPacketsUtil::GetBackRedPacket();
        var_dump($data);
        exit;

        $key1= 'ttetretretert';
        //$rst = \Yii::$app->cache->set($key1,'55555555',30);
        $rst = \Yii::$app->cache->set($key1,'88888888');
        var_dump($rst);
        $v = \Yii::$app->cache->get($key1);
        var_dump($v);
        exit;
        $time1 = microtime(true);
        $waterNum = WaterNumUtil::GetUniqueIdFromTable($error);
        $time2 = microtime(true);
        $dis = $time2 - $time1;
        \Yii::getLogger()->log('distime:'.strval($dis),Logger::LEVEL_ERROR);
        if($waterNum === false)
        {
            echo $error."\n";
            exit;
        }
        echo $waterNum;
        exit;

        $key1= 'ttetretretert';
        //$rst = \Yii::$app->cache->set($key1,'22222222',30);
        //var_dump($rst);
        $v = \Yii::$app->cache->get($key1);
        var_dump($v);
        exit;
        TimRestApi::init();
        $rst =TimRestApi::generate_user_sig(strval(3));
        var_dump($rst[0]);
        exit;
/*        $str = '<xml>
<return_code><!--[CDATA[SUCCESS]]--></return_code>
<return_msg><!--[CDATA[]]--></return_msg>
<mch_appid><!--[CDATA[wxb01ee35e86b5d74a]]--></mch_appid>
<mchid><!--[CDATA[1302837301]]--></mchid>
<device_info><!--[CDATA[]]--></device_info>
<nonce_str><!--[CDATA[PVLpy4DQa4N8ohKmrys71dmwn6t8dBe4]]--></nonce_str>
<result_code><!--[CDATA[SUCCESS]]--></result_code>
<partner_trade_no><!--[CDATA[13028373011605190000000001]]--></partner_trade_no>
<payment_no><!--[CDATA[1000018301201605190306734465]]--></payment_no>
<payment_time><!--[CDATA[2016-05-19 19:13:18]]--></payment_time>
</xml>';

        //$str = str_replace('<!--[','<![',$str);
        //$str =str_replace(']]-->',']]>',$str);
        $obj = simplexml_load_string($str,'SimpleXMLElement',LIBXML_NOCDATA);
        var_dump($obj->return_code);
        var_dump((string)$obj->return_code);
        var_dump(trim($obj->return_code));*/

        $str  = '<xml>
<return_code><![CDATA[SUCCESS]]></return_code>
<return_msg><![CDATA[参数错误:输入的用户openid有误.]]></return_msg>
<result_code><![CDATA[FAIL]]></result_code>
<err_code><![CDATA[PARAM_ERROR]]></err_code>
<err_code_des><![CDATA[参数错误:输入的用户openid有误.]]></err_code_des>
</xml>';
        $obj = simplexml_load_string($str,'SimpleXMLElement',LIBXML_NOCDATA);
        var_dump($obj->return_code);
        var_dump((string)$obj->return_code);
        var_dump(trim($obj->return_code));
        var_dump(json_decode(json_encode($obj),true));
        exit;

        $key1= 'ttetretretert';
        $rst = \Yii::$app->cache->set($key1,'22222222',20);
        var_dump($rst);
        $v = \Yii::$app->cache->get($key1);
        var_dump($v);
        //exit;
        $key='tsfefsfew_sfwfwefwef';
        $rst = \Yii::$app->cache->set($key,'eeeeeee');
        var_dump($rst);
        $v = \Yii::$app->cache->get($key);
        var_dump($v);
        exit;
        $str = '{"payload":"5-7-@TGS#33ARWVAEZ-4-http:\/\/image.matewish.cn\/user\/f0ea7848fe507072c0610c58e74109a3.jpg-\ud83d\ude33\ud83d\ude33\ud83d\ude33\ud83d\ude4a\ud83d\ude4a","aps":{"sound":"default","badge":"defaultValue","alert":{"body":"您的好友[😳😳😳🙊🙊]正在直播，快去瞅瞅吧！"}}}';
        $str1 = '5-7-@TGS#33ARWVAEZ-4-http://wx.qlogo.cn/mmopen/HXiap8jVpnE0VWX8hDicPUzowW4ibx5HRFD5aO2XDhJNRRuhKVeZAoR0yRf4F3U6LQdzQAROOJ4XlzaVYmCeM6atMMeaeSZ3nCq/0-\ud83d\ude33\ud83d\ude33\ud83d\ude33\ud83d\ude4a\ud83d\ude4a';
        var_dump(strlen($str1));
        $str1 = gzcompress($str1,9,ZLIB_ENCODING_DEFLATE);
        var_dump(base64_encode($str1));
        var_dump($str1);
        //$lastMonth = date('Y-m',strtotime('-1 month'));
        var_dump(strlen($str1));
        exit;
        //月统计
        if(!StatisticActiveUserUtil::MonthActive($error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        //日统计
        if(!StatisticActiveUserUtil::DayActive($error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        if(!StatisticLivingProfitAndTime::LivingTimeAndTicketsStatistic($error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        $str='{"payload":"5-137-@TGS#3CRAUWAEJ-1283-http://bmpic.matewish.cn/user/40080e4a527031d50f794ddbe3db14b5.jpg-啊啊啊啊啊啊啊啊啊啊","aps":{"sound":"default","badge":"defaultValue","alert":{"body":"您的好友[啊啊啊啊啊啊啊啊啊啊]正在直播，快去瞅瞅吧！"}}}';
        $str = urldecode($str);
        var_dump($str);
        var_dump(strlen($str));
        exit;
        $cid = '5d242258bfe5b45f6b58319f57246f99';
        $cid1 = 'df76d318237ee969d2b4c857bd2353c0';
        $cid2 = '9faa6cea1cb02a692433abb1ec3105a7';
        //9faa6cea1cb02a692433abb1ec3105a7

        if(!GeTuiUtil::PushListMessage('您好好友[微笑的苹果]开始直播了，快去瞅瞅！','5-137-2053-1283-猫王',[$cid,$cid1,$cid2],$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        if(!GeTuiUtil::PushSingleMessage('您好好友[微笑的苹果]开始直播了，快去瞅瞅！','5-137-2053-1283-猫王',$cid,$error))
        {
            var_dump($error);
            exit;
        }
        exit;

        $date = '2016-06-07';

        var_dump(date('Y-m-t 23:59:59',strtotime($date)));
        exit;

        if(!GeTuiUtil::PushSingleMessage('dsfsfsdfdsfdsf',$cid,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok1');
        if(!GeTuiUtil::PushAppMessage('sfdasfasfasfsafasf',$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok2');
        exit;
        $data= ['t','dddd','ccccc'];
        var_dump($data);
        $data='["t","dddd","ccccc"]';
        var_dump(json_decode($data));
        exit;
        $rst = \Yii::$app->beanstalk->watch('living_heart')
            ->reserve();
        var_dump($rst);
        exit;

        var_dump(date('Y-W'));
        $first_week_date = date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600));
        $last_week_date = date('Y-m-d',(time()+(7-(date('w')==0?7:date('w')))*24*3600));
        var_dump($first_week_date);
        var_dump($last_week_date);
        exit;
        $group_id='@TGS#3HAQ5VAEL';
        $sendInfo = [
            'type'=>501,
            'msg'=>'',
            'userinfo'=>[]
        ];
        $user_id = 1000;

        $to_user = 33;
        if(!empty($dataProtocal['data']['user_id']))
        {
            $to_user = $dataProtocal['data']['user_id'];
        }
        $self_user_id =3;
        $fieldsSource = ['client_id','nick_name','client_no','alipay_no','pic','level_id','level_pic','sex','color','level_bg','font_size','age','city','sign_name','send_ticket_count','attention_num','funs_num','ticket_count_sum','ticket_count','ticket_real_sum','today_ticket_num','bean_balance','virtual_bean_balance','is_bind_weixin','is_bind_alipay','is_contract','is_centification'];
        $fields = ['client_id'=>'client_id as user_id','nick_name'=>'nick_name','client_no'=>'client_no','alipay_no'=>'alipay_no','pic'=>'pic','level_id'=>'level_id','level_pic'=>'ls.level_pic','font_size'=>'font_size','color'=>'color','level_bg'=>'level_bg','sex'=>'sex','age'=>'age','city'=>'city','sign_name'=>'sign_name', 'send_ticket_count'=>'send_ticket_count','attention_num'=>'attention_num','funs_num'=>'funs_num','ticket_count_sum'=>'ticket_count_sum','ticket_count'=>'ticket_count','ticket_real_sum'=>'ticket_real_sum','today_ticket_num'=>'IFNULL((select real_ticket_num from mb_time_livingmaster_ticketcount where livingmaster_id = :ld and hot_type = 1 and statistic_date = DATE_FORMAT(NOW(),\'%Y-%m-%d\')),0) as today_ticket_num','bean_balance'=>'bean_balance','virtual_bean_balance'=>'virtual_bean_balance','is_bind_weixin'=>'is_bind_weixin','is_bind_alipay'=>'is_bind_alipay','is_contract'=>'is_contract','is_centification'=>'is_centification as real_validate'];
        $filedRst = [];

        if(!empty($fieldsSource))
        {
            foreach($fieldsSource as $field)
            {
                if(!isset($fields[$field]))
                {
                    var_dump('请求的字段不存在'.$field);
                    return false;
                }
                $filedRst[] = $fields[$field];
            }
        }
        if(!ClientInfoUtil::GetUserData($filedRst,$to_user,$self_user_id,$userInfo,$error))
        {
            var_dump($error);
            return false;
        }
        $sendInfo['userinfo']=$userInfo;
        $sv = json_encode($sendInfo);
        if(!TimRestApi::group_send_group_msg_custom($user_id,$group_id,$sv,$error))
        {
            var_dump($error);
            return false;
        }
        var_dump($sv);
        exit;
        $member_id = 10;
        $rst = TimRestApi::group_add_group_member('@TGS#3QMLUVAE4',strval($member_id),1, $error);
        if(!$rst)
        {
            var_dump($error);
            return false;
        }
        var_dump($rst);
        var_dump('ok');
        exit;

        $data = ChatPersonGroupUtil::GetReBots(3,30);
        var_dump($data);
        exit;
        $data = LivingUtil::GetOffLineLiving();
        var_dump($data);
        exit;
        $data = AttentionUtil::GetFunForGeTui(33,'1',50);
        var_dump($data);
        exit;
        $cid = 'e44c9c66370e99dc8c3278879d9472db';//徐勤超 3ff7fb51ee48edc330eca538824b0bc2
        $cid1 = '7b6f46b040dac50e6002625403a55257';
        if(!GeTuiUtil::PushListMessage('test haha',[$cid,$cid1],$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok');
        if(!GeTuiUtil::PushSingleMessage('dsfsfsdfdsfdsf',$cid,$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok1');
        if(!GeTuiUtil::PushAppMessage('sfdasfasfasfsafasf',$error))
        {
            var_dump($error);
            exit;
        }
        var_dump('ok2');
        exit;
        $rst = \Yii::$app->cache->set('ttttt','11111');
        var_dump($rst);
        $rst = \Yii::$app->cache->get('ttttt');
        var_dump($rst);
        exit;
        //增加虚拟豆
        if(!BalanceUtil::AddVirtualBeanNum(7,1000,$error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        //增加实际豆
        if(!BalanceUtil::AddReadBeanNum(7,1000,$error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;

        $receipt_data = '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';
        $data =IOSBuyUtil::GetIosBuyVerify($receipt_data);
        var_dump($data);
        var_dump($data['receipt']['in_app'][0]['transaction_id']);
        exit;


        $s = 3600*24 + 500;
        $time = gmstrftime('%M:%S', $s);
        var_dump($time);
        exit;

        $openid = 'ovfNUw9ajEfiGcLg_nF98kbejV5A';
        $total_amount = '1';
        $nick_name = '贺烈强';
        $res = TicketToCashUtil::SendWeChatPay($openid,$total_amount,$nick_name,$error);
        var_dump($res);
        exit;
        $str = '{"nick_name":"\u521a\u521a\u624dvv\ud83d\ude2c\ud83d\ude17\ud83d\ude17\ud83d\ude21\ud83d\ude2a\ud83d\ude2f\ud83d\ude0d\ud83d\ude35\ud83d\ude26\ud83d\ude07"}';
        $data = json_decode($str,true);
        var_dump($data);
        $sqltest ='update mb_client set nick_name=:na where client_id=6';
        $rst = \Yii::$app->db->createCommand($sqltest,[':na'=>$data['nick_name']])->execute();
        var_dump($rst);
        exit;

        //unique_no=2.00PAXnvDdIK36B31546883ddXBw_oD&rand_str=BNTDBUBSMRJYFKOPKBIVYIGNVVYRLZUGKUOLFHET&time=1463636097&p_sign=66da5a1581005f3f321aa06cd1d795c973defa51&living_id=141
        $params = [
            'unique_no' => '2.00PAXnvDdIK36B31546883ddXBw_oD',
            'living_id' =>'141',
            'time' => '1463636097',
            'rand_str' =>'BNTDBUBSMRJYFKOPKBIVYIGNVVYRLZUGKUOLFHET',
        ];
        $sign = ClientUtil::GetClientSign($params);  //签名验证
        var_dump('66da5a1581005f3f321aa06cd1d795c973defa51');
        var_dump($sign);
        exit;
        //TRe8RewYNewRwPWPrY9Tw9NMgewRM9Tw98gONwMe
        $str = 'sdf3sdffswfsf23sdsdf';
        $byteAry = sha1($str,true);
        var_dump(base64_encode($byteAry));
        exit;

        $user_id = 73;
        $page_no = 1;
        $page_size = 50;
        $data = ChatFriendsUtil::GetContributionBoard($user_id,$page_no,$page_size,$user_id);
        var_dump($data);
        exit;
        $inputCode = 'wdga';
        var_dump(Yii2ValidateCode::ValidatePicCode($inputCode));
        exit;
        list($controller,$route) = \Yii::$app->createController('mbliving');
        if(!($controller instanceof \yii\web\Controller))
        {
            var_dump('不是webcontroller');
            exit;
        }
        $action = $controller->createAction('piccode');
        $vcode = $action->getVerifyCode(false);
        var_dump($vcode);
        exit;

        return $this->render('test1');

        $user_id = 4;
        $friendsList = ChatPersonGroupUtil::GetChatRoomManager($user_id);
        var_dump($friendsList);
        exit;
        set_time_limit(0);
        $no = WaterNumUtil::GetRandUniqueNo();
        $sqltest = 'insert ignore into test_unique(unique_no) values(:no)';
        $rst = \Yii::$app->db->createCommand($sqltest,[':no'=>$no])->execute();
        if($rst < 0)
        {
            \Yii::getLogger()->log('出现重复了：'.$no,Logger::LEVEL_ERROR);
        }
        echo 'ok';
        exit;


        //生成新协议
        $protocals = require(__DIR__.'/../zhiboapi/ConfigV1.php');
        $ary = [];
        foreach($protocals as $key=>$value)
        {
            $ary[] = $key;
        }
        sort($ary);
        $rst = implode("\n",$ary);
        $path = $_SERVER['DOCUMENT_ROOT'].'/protocal.txt';
        file_put_contents($path,$rst);
        echo 'ok';
        exit;
        $data = [];
        $error = '';
        $outInfo = null;
        $cl = new CreateLivingSaveByTrans($data);
        if(!$cl->SaveRecordForTransaction($error,$outInfo))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;


        $str = 'sdfdsfsfwefsfwfsdf';
        var_dump(md5($str));
        var_dump(sha1($str));
        exit;

        //测试aes加密
        $crypt_key = '05f3176e0d0e6772d5b6a78b939889d6';
        $cryptManager= new AESCrypt($crypt_key);
        $data = 'sdfsfsfdsf我们打赏jos我放假时间FPS我20佛@%……#￥%￥……%……￥';
        var_dump($data);
        $dataSource  = $cryptManager->encrypt($data);
        var_dump($dataSource);
        $rst = $cryptManager->decrypt($dataSource);
        var_dump($rst);
        exit;
        $user_id = 4;
        $living_id = 1;
        $shutup_user_id = 5;
        $op_type = '1';

        if(!ChatGroupUtil::ShutupForGrooupMember($living_id,$user_id,$shutup_user_id,$op_type,$error))
        {
            var_dump($error);
            return false;
        }
        echo 'ok';
        exit;
        //取消黑名单
        $user_id = 8;
        $black_id = 7;
        if(!AttentionUtil::CancelBlack($user_id,$black_id,$error))
        {
            var_dump($error);
            return false;
        }
        echo 'ok';
        exit;
        //设置黑名单
        $user_id = 8;
        $black_id = 7;

        if(!AttentionUtil::SetBlack($user_id,$black_id,$error))
        {
            var_dump($error);
            return false;
        }
        echo 'ok';
        exit;
        //取消关注
        $user_id  = 8;
        $attention_id=7;
        $data=[
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
            'op_type'=>'cancel'
        ];
        if(!JobUtil::AddJob('user_attention',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }
        echo 'ok';
        exit;
        //查询好友
        TimRestApi::init();
        $list = TimRestApi::sns_friend_check2('7',['8','9','10'],'CheckResult_Type_Both');
        var_dump($list);
        exit;
        //取消关注
        $user_id  = 10;
        $attention_id=7;
        if(!ChatUtil::CancelAttention($user_id,$attention_id,$error))
        {
            var_dump($error);
            return false;
        }

        //加入异步任务处理
        $data=[
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
            'op_type'=>'cancel'
        ];
        if(!JobUtil::AddJob('user_attention',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }
        echo 'ok';
        exit;
        //关注
        $user_id  = 9;
        $attention_id=7;
        //相互加为好友，单向关注
        if(!ChatUtil::Attention($user_id,$attention_id,$error))
        {
            var_dump($error);
            return false;
        }

        //加入异步任务处理
        $data=[
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
            'op_type'=>'attention'
        ];
        if(!JobUtil::AddJob('user_attention',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        //腾讯云注册用户
        $user_id = '10';
        if (!TimRestApi::account_import($user_id, '', '', $error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;

        //注册用户
        $unique_no = '18820005853';
        $device_no='273492027402342s3';
        $data = ['register_type'=>'1'];
        $geTuiId = sha1(UsualFunForStringHelper::CreateGUID());
        if(!ClientUtil::RegisterUser($unique_no,$device_no,$data,$geTuiId,$error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;

        //获取黑名单
        $page_no = 1;
        $page_size = 5;
        $user_id = 5;
        $blackList = ClientUtil::GetBlacks($user_id,$page_no,$page_size,$user_id);
        var_dump($blackList);
        exit;
        $user_id = 5;
        $passParams = [
            'device_type'=>'1',
            'cash_type'=>'2',
            'op_unique_no'=>sha1(UsualFunForStringHelper::CreateGUID()),
            'ticket_num'=>'200',
            'goods_ticket_to_cash_id'=>'1'
        ];
        if(!TicketToCashUtil::TicketToCash($passParams,$user_id,$error))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        //增加用户票数
        $param = [
            'ticket_num'=>10000
        ];
        $userBalance = BalanceUtil::GetUserBalanceByUserId(5);
        $bm = new ModifyBalanceByAddTicket($userBalance,$param);
        if(!$bm->SaveRecordForTransaction($error,$outInfo))
        {
            var_dump($error);
            exit;
        }
        echo 'ok';
        exit;
        $accessKeyId = 'ol6eFceGSKpCI7vZ';
        $accessKeySecret='ZGTjI5Z7yk3OaTYrhpCiwYWuLM1Cgb';
        $endPoint = 'oss-cn-shanghai.aliyuncs.com';
        $object = 'user-auth';
        $bucket = 'mibodemo';
        $time =60* 60;// time() + 60*2
        $oss = new OssClient($accessKeyId,$accessKeySecret,$endPoint);
        $authUrl = $oss->signUrl($bucket,$object,$time,"POST");
        var_dump($authUrl);
    }
} 