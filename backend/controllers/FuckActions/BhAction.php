<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/6
 * Time: 下午5:27
 */

namespace backend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\business\DailyStatisticUsersUtil;
use backend\business\DataCubeUtil;
use backend\business\ImageUtil;
use backend\business\JobUtil;
use backend\business\KeywordUtil;
use backend\business\ResourceUtil;
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\SaveArticleTotalByTrans;
use backend\business\SignParamsUtil;
use backend\business\UserMenuUtil;
use backend\business\WeChatUserUtil;
use backend\business\WeChatUtil;
use backend\components\MessageComponent;
use backend\components\ReceiveType;
use backend\components\WeChatClass\EventClass;
use backend\components\WeChatComponent;
use callmez\wechat\sdk\MpWechat;
use callmez\wechat\sdk\Wechat;
use Codeception\Module\Cli;
use common\components\OssUtil;
use common\components\SystemParamsUtil;
use common\components\UsualFunForNetWorkHelper;
use common\components\UsualFunForStringHelper;
use common\models\ArticleTotal;
use common\models\AttentionEvent;
use common\models\AuthorizationList;
use common\models\AuthorizationMenu;
use common\models\Client;
use common\models\QrcodeImg;
use common\models\Resource;
use common\models\SignImage;
use common\models\SignParams;
use common\models\TemplateTiming;
use common\models\User;
use Qiniu\Auth;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;

class BhAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $arr = [1,3,5,6,6];
        $rst = implode(',', $arr);

        print_r($rst);


        exit;
        $rst = DailyStatisticUsersUtil::getDailyFansNum(15);
        if(empty($rst)) {
            $rst[] = ['name'=> 'empty'];
        }
        for($i = 0; $i< 24 ; $i ++) {
            foreach($rst as $item) {
                if(!isset($item['statistics_date']) || $item['statistics_date'] != $i) {
                    $net_user[$i] = 0;
                    //                $new_user[$i] = 0;
                    //                $cancel_user[$i] = 0;
                }else{
                    $net_user[$i] = intval($item['net_user']);
                    break;
                    //                $new_user[$i] = $rst[$i]['new_user'];
                    //                $cancel_user[$i] = $rst[$i]['cancel_user'];
                }
            }
            $date[] = $i;
        }
        $rstData = [
            'net_user' => $net_user,
//            'new_user' => $new_user,
//            'cancel_user' => $cancel_user,
            'date' => $date
        ];
        print_r($rstData);
        exit;
        $auth = AuthorizerUtil::getAuthByOne(15);
        $USer = WeChatUserUtil::getUserInfo($auth->authorizer_access_token, 'o3WvUw7VgX1N6dMFHLxaY7D0JszI');
//        $rst = WeChatUserUtil::getUserListForOpenId($auth->authorizer_access_token, 'o3GvUw8Q60ND-Hsa2UMlA7BUSJQU');
        var_dump($USer);

        exit;

        exit;
        $time = date('H',1504454400);
        $i = date('H');
        var_dump(intval($i));
        var_dump(intval($time));

        exit;
        $se = [
            'name'=>'2121',
            'data'=>[1,2,4,51,3],
        ];
        $rst  =  DailyStatisticUsersUtil::getWeekFansNum(0);
        print_r($rst['net_user']);
print_r($se);
        exit;
        $sql_net = 'select sum(net_user) as num from wc_fans_statistics where app_id = :apd and statistics_date < :date';
        $net_count = \Yii::$app->db->createCommand($sql_net,[':apd'=>14, ':date'=>date('Y-m-d')])->queryScalar();
        print_r($net_count);
        exit;
        $rst = DailyStatisticUsersUtil::getDailyFansNum(0);
        print_r($rst);




        exit;
        $rst = AuthorizerUtil::getAuthListName();
        $data = [];
        foreach($rst as $item)
        {
            $data[$item['record_id']] = $item['nick_name'];
        }
        //$rst = User::findByBackendUsername(1,5);
        print_r($data);exit;
        print_r(\Yii::$app->params['backend_list']);exit;
        /*$rst = sprintf('%.4f',  round(0 / 21221,4)) * 100;
        print_r($rst);
        exit;*/

        $auth = AuthorizerUtil::getAuthByOne(27);
        $accessToken = $auth->authorizer_access_token;
        $transActions = [];
        $e = 0;
        for ($i = 7; $i >= 1; $i --) {
            $rst = DataCubeUtil::getArticleTotal($accessToken, $i);
            if(empty($rst) || isset($rst['errmsg'])) {

            }
            foreach( $rst['list'] as $item) {
                $transActions[] = new SaveArticleTotalByTrans($item, ['app_id'=> 27]);
            }
            if(!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $out)) {
                print_r($error);exit;
            }
            unset($transActions);
            $e ++;
        }
        print_r($e);
        exit;

        //$data = DataCubeUtil::getUserRead($accessToken);
        //$data = DataCubeUtil::getArticleSummary($accessToken);
        var_dump($data);

        exit;
        JobUtil::AddCustomJob('beanstalk', 'tube', 1, $error, 999);


        exit;
        $bg_image = SignImage::findAll(['sign_id' => 26]);
        $count = count($bg_image);
        print_r($count);
        print_r("<br />");
        $bg = $bg_image[rand(0, ($count - 1))]['pic_url'];
        print_r($bg);

        exit;
        $bgPath = \Yii::$app->basePath . '/runtime/signimg/bg2.jpg';
        $picPath = \Yii::$app->basePath . '/runtime/signimg/0.jpeg';
        $text = ['name' => '点撒接口定时爱的世界阿坤嗒嗒嗒的撒', 'num' => 1000000];
        if (!ImageUtil::imageSign($bgPath, $picPath, '111', $text, $filename, $error)) {
            print_r($error);
            exit;
        }

        exit;
        $data = [
            'appid' => 'wxfb4431191609bd1e',  //wxd396e6246bd24673  90  wxfb4431191609bd1e 1
            'EventKey' => '121',
            'FromUserName' => 'oH24O0m_KZp0XcQSb74qGNuFZDsw',
            'Content' => '晚安',
        ];
        /*$data  = array (
            'ToUserName' => 'gh_bdba6dfdc510',
            'FromUserName' => 'ol_EGvw_V3rXYILgc7QEOVVBrxwg',
            'CreateTime' => '1503042880',
            'MsgType' => 'text',
            'Content' => '11',
            'MsgId' => '6455520014503388140',
            'appid' => 'wxeb6eae49977722b5',
        );*/
        $msgObj = new MessageComponent($data, 1);
        $rst = $msgObj->VerifySendMessage();
        print_r($rst);
        exit;
        $day = 1;
        $query = (new Query())
            ->select(['key_id', 'sign_id', 'day_id', 'type'])
            ->from('wc_sign_keyword sk')
            ->innerJoin('wc_sign_params sp', 'sk.sign_id = sp.id')
            ->where('key_id = :key and day_id = :day', [':key' => 3, ':day' => $day])
            ->one();

        $sign_id = $query[0]['sign_id'];

        print_r($query);
        exit;
        $ary = ['日', '一', '二', '三', '四', '五', '六'];
        $time = time();
        $i = date('w', $time);
        print_r('星期' . $ary[$i]);
        echo "<br>";
        exit;


        exit;

        exit;
        $sign = SignParamsUtil::GetSignDayParams(1);
        print_r($sign);
        exit;


        $data = '2017-08-17 11:50:00';
        print_r(strtotime($data));
        exit;

        $json = '{"first":{"value":"\u6765\u4e00\u6761\u6d4b\u8bd5\u6a21\u677f\u63a8\u9001\u6d88\u606f","color":"#FF0000"},"keyword1":{"value":"{{NICKNAME}} \u8fd9\u91cc\u5e26\u4e00\u4e2a\u7528\u6237\u540d","color":"#135EFB"},"keyword2":{"value":"\u597d\u50cf\u8fd8\u53ef\u4ee5","color":"#173177"},"keyword3":{"value":"999\u81f3\u5c0aVIP","color":"#135EFB"},"keyword4":{"value":"2222-09-09 23:59:59","color":""},"remark":{"value":"adaa","color":"#FF0000"},"url":"http:\/\/www.baidu.com","openid":"ol_EGvw_V3rXYILgc7QEOVVBrxwg"}';
        print_r(json_decode($json, true));

        exit;
        $time = time();
        $condition = 'status = 1 and create_time <= :tm';
        $query = TemplateTiming::find()
            ->select(['app_id', 'template_id', 'template_data', 'status', 'type', 'create_time'])
            ->where($condition, [':tm' => $time])
            ->all();
        foreach ($query as $item) {
            $item->id;
        }


        exit;
        $time = date('Y-W');
        print_r(strtotime($time));
        echo "<br />";
        $date = date('Y-m-d', strtotime($time));
        print_r($date);
        exit;
        //匹配时间格式为2016-02-16或2016-02-16 23:59:59前面为0时可以不写
        $time = "2016-12-14 23:59:59";
        $patten = '/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/';
        if (preg_match($patten, $time)) {
            echo $timestro = strtotime($time);
        } else {
            echo "error";
        }


        exit;
        $data = [
            'appid' => 'wxd396e6246bd24673',
            'EventKey' => '121',
            'FromUserName' => 'oH24O0m_KZp0XcQSb74qGNuFZDsw'
        ];
        $event = new EventClass($data);
        $event->Click();
        echo "ok";

        exit;
        $time = time();
        $rst = date('YW', $time);
        print_r($rst);
        exit;
        $msg = AttentionEvent::findAll(['app_id' => 90, 'menu_id' => 72, 'flag' => 2]);
        print_r($msg);
        exit;
        $a = 89;
        $auth = AuthorizerUtil::getAuthByOne($a);
        $rst_ticket = WeChatUserUtil::getQrcodeTickt($auth->authorizer_access_token, '1', $error);
        if (!$rst_ticket) {
            print_r($error);
            exit;
        }
        $ticket = $rst_ticket['ticket'];
        if (!WeChatUserUtil::getQrcodeImg($ticket, $a, $qrcode_file)) {
            $error = '保存二维码到本地失败';
            print_r($error);
            exit;
        }
        print_r($qrcode_file);

        exit;
        $rst = UsualFunForStringHelper::mt_rand_str(20);
        print_r($rst);
        exit;
        $time = strtotime(date('Y-m-d H:i:s'));
        $end = strtotime('-2 day'); //date('Y-m-d H:i:s',strtotime('-2 day'));
        $rst = ($time - $end) / 86400;
        print_r($rst);
        echo "<br/>";
        print_r($time);
        print_r("<br/>");
        print_r($end);
        //$rst = Client::find()->where(['and',['app_id'=>85],['between','crete_time',date('Y-m-d H:i:s',time() - 60), date('Y-m-d H:i:s')],['subscribe'=>1]])->one();
        //$rst = date('Y-m-d H:i:s', strtotime('-2 day'));
        //print_r($rst);
        exit;
        $query = (new Query())
            ->select(['al.record_id', 'ifnull(new_user,0) as new_user', 'ifnull(net_user,0) as net_user', 'ifnull(count_user,0) as count_user'])
            ->from('wc_authorization_list al')
            ->innerJoin('wc_statistics_count sc', 'al.record_id = sc.app_id and al.record_id = 76')
            ->leftJoin('wc_fans_statistics fs', 'al.record_id = fs.app_id and fs.statistics_date =:date', [':date' => date('Y-m-d')])->one();
        print_r($query);
        exit;
        $data = \Yii::$app->params['WxAuthParams'];
        $str = implode(',', $data);

        $sql = 'select client_id,nick_name,open_id,c.app_id, ifnull(temp_num,0) as temp_num
                from wc_client c left join wc_template_flag f ON  c.client_id = f.user_id and c.app_id = f.app_id
                where client_id in (9617,114567,215226,216218) and is_vip = 0 and c.app_id in (' . $str . ') and (create_time BETWEEN :star and :end) and subscribe = 1';
        $OneGroupData = \Yii::$app->db->createCommand($sql, [
            ':star' => '2017-08-07 17:00:00', //date('Y-m-d H:i:s', time() - 60 * 60),
            ':end' => '2017-08-07 17:30:00'//date('Y-m-d H:i:s', time() - 60 * 30),
        ])->queryAll();
        print_r($OneGroupData);
        exit;
        $sql = 'select client_id,nick_name,open_id,c.app_id,ifnull(temp_num,0) as temp_num
                        from wc_client c left join wc_template_flag f on c.client_id = f.user_id and c.app_id = f.app_id
                        where client_id = 114567 and is_vip = 0 and c.app_id in (' . $str . ') and (create_time BETWEEN :star and :end) and subscribe = 1';
        $TowGroupData = \Yii::$app->db->createCommand($sql, [
            ':star' => date('Y-m-d H:i:s', time() - (60 * (60 * 2 + 30))),
            ':end' => date('Y-m-d H:i:s', time() - 60 * 60 * 2),
        ])->queryAll();
        print_r(\Yii::$app->db->createCommand($sql, [
            ':star' => date('Y-m-d H:i:s', time() - (60 * (60 * 2 + 30))),
            ':end' => date('Y-m-d H:i:s', time() - 60 * 60 * 2),
        ])->rawSql);
        print_r($TowGroupData);
        exit;
        exit;
        phpinfo();
        exit;
        $stat = microtime(true);
        print_r('程序开始:' . memory_get_usage());
        echo "<br/>";
        $data = [
            'nick_name' => 'Gavean',
            'pic' => 'http://wx.qlogo.cn/mmopen/UVzXBswyibFh7ib0qClxDP6Y5EFUGSgrw7FIUNcB7K60LAIpKHpqHxJa7ta10HKYYIVSCPSQy0IBzGib9zgn9NE00vaHbVydjpY/0',
        ];
        $qrcode = 'http://mmbiz.qpic.cn/mmbiz_jpg/6SPlDzxhRsQZgoUE4507ibia0hcWdicibxPLU2JvGjreoJMA9JDzyQK1IFNQb7OrZDx0HsIjgfuL2pJQe4PXrzIUdg/0';
        $rst = UsualFunForNetWorkHelper::HttpGetImg($data['pic'], $content_type, $error);
        print_r('执行获取:' . memory_get_usage());
        echo "<br/>";
        unset($data);
        if (!$rst) {
            echo "rst :" . "<br />";
            print_r($error);
            exit;
        }
        $res = UsualFunForNetWorkHelper::HttpGet($qrcode);
        unset($qrcode);
        if (!$res) {
            echo "res:";
            print_r($error);
            exit;
        }
        $text = 'Mr.REE';
        $time = time();
        $filename = \Yii::$app->basePath . '/web/wswh/img/pic_' . $time . '.png';
        $qrcodename = \Yii::$app->basePath . '/web/wswh/img/qrcode_' . $time . '.png';
        file_put_contents($filename, $rst);
        file_put_contents($qrcodename, $res);

        if (!ImageUtil::imagemaking($qrcodename, $filename, 'bbbbb', $text, $faaa, $error)) {
            print_r($error);
            exit;
        }
        print_r('处理完图片:' . memory_get_usage());
        echo "<br/>";
        @unlink($filename);
        @unlink($qrcodename);
        $end = microtime(true);
        $return = $end - $stat;
        echo "<br/>";
        print_r('最终结束:' . memory_get_usage());
        echo "<br/>";
        print_r($return);
        exit;
        phpinfo();
        exit;
        $data = [];
        JobUtil::AddCustomJob('beanstalk', 'tube', $data, $error);

        exit;
        $query = (new Query())
            ->select(['authorizer_appid', 'authorizer_refresh_token', 'nick_name', 'record_id', 'update_time'])
            ->from('wc_authorization_list')
            ->all();
        if (empty($query)) exit('没有数据');
        $date = date('Y-m-d H:i:s');
        $time = '2017-08-02 12:00:00';
        //print_r($time);
        echo "<br/>";

        $rst = floor((strtotime($time) - strtotime($query[1]['update_time'])) % 86400 / 60);
        print_r($rst);
        exit;
        $max = count($query);
        $k = 0;
        print_r($max);
        $error_data = [];
        $timeout = 0;
        for ($i = 0; $i < $max; $i++) {
            $data = [
                'component_appid' => '2121',
                'authorizer_appid' => $query[$i]['authorizer_appid'],
                'authorizer_refresh_token' => $query[$i]['authorizer_refresh_token']
            ];

            $url = "https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=" . $this->AppInfo->access_token;
            $json = json_encode($data);
            $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
            if (!isset($rst['authorizer_access_token']) || !isset($rst['authorizer_refresh_token'])) {
                if ($timeout < 3) {
                    $i--;
                    $timeout++;
                    continue;
                }
                echo '刷新授权方令牌失败: AppId : ' . $query[$i]['authorizer_appid'] . "  time : $date\n";
                continue;
            }
            $AuthAppid = AuthorizationList::findOne(['authorizer_appid' => $item['authorizer_appid']]);
            $AuthAppid->authorizer_access_token = $rst['authorizer_access_token'];
            $AuthAppid->authorizer_refresh_token = $rst['authorizer_refresh_token'];
            $AuthAppid->update_time = $date;
            if (!$AuthAppid->save()) {
                if ($timeout < 3) {
                    $i--;
                    $timeout++;
                    continue;
                }
                \Yii::error('保存授权数据失败: rst:' . var_export($rst, true) . ' Nick_name :' . $item['nick_name'] . '  ' . var_export($AuthAppid->getErrors(), true));
                \Yii::getLogger()->flush(true);
                echo '保存授权新数据失败: ' . "time: $date\n";
                continue;
            }
            $timeout = 0;
        }
        echo "刷新授权公众号AppId完成，共 $max 条记录 , 成功刷新记录数: " . ($i + 1) . " 条,  time:  $date \n";
        exit;

        $model = Client::findOne(['client_id' => 2]);
        return $this->controller->redirect('demo');

        /*$query = (new Query())
            ->select(['menu_id'])->from('wc_authorization_menu')->where(['global'=>3])->all();
        print_r($query);

        exit;*/

        $rst = User::findAll(['pic' => '']);
        foreach ($rst as $item) {
            $num = rand(1, 6);
            $item->pic = 'http://7xld1x.com1.z0.glb.clouddn.com/person-' . $num . '.png';
            $item->save();
            print_r($item->attributes);
        }

        exit;
        $openid = 'olAb6wbBGooyONepGqXDrda2ADsA';
        $auth = AuthorizerUtil::getAuthByOne(69);
        $getData = WeChatUserUtil::getUserInfo($auth->authorizer_access_token, $openid);
        print_r($getData);
        exit;
        $stat = microtime(true);

        $data = [
            'nick_name' => 'Gavean',
            'pic' => 'http://wx.qlogo.cn/mmopen/UVzXBswyibFh7ib0qClxDP6Y5EFUGSgrw7FIUNcB7K60LAIpKHpqHxJa7ta10HKYYIVSCPSQy0IBzGib9zgn9NE00vaHbVydjpY/0',
        ];
        $qrcode = 'http://mmbiz.qpic.cn/mmbiz_jpg/6SPlDzxhRsQZgoUE4507ibia0hcWdicibxPLU2JvGjreoJMA9JDzyQK1IFNQb7OrZDx0HsIjgfuL2pJQe4PXrzIUdg/0';
        $rst = UsualFunForNetWorkHelper::HttpGetImg($data['pic'], $content_type, $error);
        if (!$rst) {
            echo "rst :" . "<br />";
            print_r($error);
            exit;
        }
        $res = UsualFunForNetWorkHelper::HttpGet($qrcode);
        if (!$res) {
            echo "res:";
            print_r($error);
            exit;
        }
        $text = $data['nick_name'];
        $time = time();
        $filename = \Yii::$app->basePath . '/web/wswh/img/pic_' . $time . '.png';
        $qrcodename = \Yii::$app->basePath . '/web/wswh/img/qrcode_' . $time . '.png';
        file_put_contents($filename, $rst);
        file_put_contents($qrcodename, $res);

        if (!ImageUtil::imagemaking($qrcodename, $filename, 'bbbbb', $text, $faaa, $error)) {
            print_r($error);
            exit;
        }

        @unlink($filename);
        @unlink($qrcodename);
        $end = microtime(true);
        $return = $end - $stat;
        echo "<br/>";
        print_r($return);
        exit;

        $msg = SystemParamsUtil::GetSystemParam('qrcode_msg', true, '');

        print_r(sprintf(htmlspecialchars($msg['value1']), 1, 1, 1));
        exit;
        $params = 'select msg_id from wc_batch_attention where app_id = %s';
        $condition = sprintf('app_id=%s and flag=%s or record_id in (' . $params . ')', 43, 0, 43);
        if (null !== null)
            $condition .= sprintf(' or key_id=%s', null);
        $query = (new Query())->from('wc_attention_event')
            ->select(['record_id', 'app_d', 'event_id', 'content', 'msg_type', 'title', 'description', 'url',
                'picurl', 'update_time', 'video'])
            ->where($condition)
            ->orderBy('order_no asc,create_time asc')
            ->all();
        if (empty($query))
            return false;
        print_r($query);
        exit;
        //$msg = SystemParamsUtil::GetSystemParam('qrcode_msg',true,'value3');
        \Yii::getLogger()->log('1', Logger::LEVEL_ERROR);
        //print_r($msg);exit;
        $rst = AuthorizerUtil::getEventMsg(7);
        //$pos = strpos("\r\n",$rst->attributes['content']);
        $html = str_replace("\r\n", PHP_EOL, $rst->attributes['content']);
        $html = json_encode($html, JSON_UNESCAPED_UNICODE);
        print_r($html);
        exit;

        exit;
        $url = 'http://7xld1x.com1.z0.glb.clouddn.com/getvoice.mp3';
        $basename = basename($url);
        $dirname = \Yii::$app->basePath . '/web/wswh/source/';
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        $fileDir = $dirname . $basename;
        file_put_contents($fileDir, $rst);
        if (!file_exists($fileDir)) {
            print_r('保存七牛图片到本地失败');
            exit;
        }
        print_r('ok');
        exit;
        $qrcodename = \Yii::$app->basePath . '/web/wswh/qrcode.png';
        $filename = \Yii::$app->basePath . '/web/wswh/0.jpeg';
        $text = 'Gavean';
        $openid = time();
        if (!ImageUtil::imagemaking($qrcodename, $filename, $openid, $text, $faaa, $error)) {
            print_r($error);
            exit;
        }
        //print_r($faaa);

        exit;

        $auth = AuthorizerUtil::getAuthByOne(76);
        $data['appid'] = $auth->authorizer_appid;
        $msg = new MessageComponent($data);
        $msgData = $msg->getMessageModel();
        $msgData = [['msg_type' => '3', 'media_id' => 'bftMsd8OQJiYWsRLs0TJTJvYbrNXNmdx5fmZvegeo-fX6_mCBK31OiU3vJmuKdxQ']];
        $msg->sendMessageCustom($msgData, 'ol_EGvw_V3rXYILgc7QEOVVBrxwg');

        exit;
        $pic = \Yii::$app->basePath . '/web/wswh/getvoice.mp3';
        print_r($pic);
        $Wechat = new WeChatUtil();
        $re = $Wechat->Upload($pic, $auth->authorizer_access_token, $rst, $error, 'voice');
        if (!$re) {
            print_r($error);
            exit;
        }
        print_r($rst);
        /* [type] => voice
     [media_id] => bftMsd8OQJiYWsRLs0TJTJvYbrNXNmdx5fmZvegeo-fX6_mCBK31OiU3vJmuKdxQ
     [created_at] => 1500894333*/

        exit;

        $query = AuthorizerUtil::getMenuList(76);
        print_r($query);
        exit;
        phpinfo();
        exit;
        $sta = microtime(true);
        $data = [
            'ToUserName' => 'gh_7318204277d9',
            'FromUserName' => 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA',
            'CreateTime' => '1500542620',
            'MsgType' => 'event',
            'Event' => 'CLICK',
            'EventKey' => 'get_qrcode',
            'appid' => 'wx1024c6215af20360',
        ];
        $openid = $data['FromUserName'];
        $auth = AuthorizerUtil::getAuthOne($data['appid']);
        $access_token = $auth->authorizer_access_token;
        $client = AuthorizerUtil::getUserForOpenId($openid, $auth->record_id);
        $img = ImageUtil::GetQrcodeImg($client->client_id);

        if (!isset($img)) {  //TODO: 如果图片不存在  重新生成并上传
            $q = microtime(true);
            $userData = WeChatUserUtil::getUserInfo($access_token, $openid);
            if (!WeChatUserUtil::getQrcodeSendImg($access_token, $openid, $userData['headimgurl'], $qrcode_file, $pic_file)) {
                print_r('不行');
                exit;
            }
            echo "获取2张图片";
            print_r(microtime(true) - $q);
            echo "<br/>";
            print_r(microtime(true) - $sta);
            echo "<br/>";
            $text = $userData['nickname'];
            $w = microtime(true);
            if (!ImageUtil::imagemaking($qrcode_file, $pic_file, $text, $bg_img, $error)) {
                \Yii::error('generate Qrcode Img Error: ', var_export($error, true));
                print_r($error);
                exit;
            }
            echo "组装图片";
            print_r(microtime(true) - $w);
            echo "<br />";
            print_r(microtime(true) - $sta);
            echo "<br />";
            $name = basename($bg_img);
            $e = microtime(true);
            if (!OssUtil::UploadQiniuFile($name, $bg_img, $bg_url, $error)) {  //TODO: 背景图上传七牛
                print_r($error);
                exit;
            }
            echo "上传七牛 ";
            print_r(microtime(true) - $e);
            echo "<br />";
            print_r(microtime(true) - $sta);
            echo "<br />";
            $wechat = new WeChatUtil();
            $r = microtime(true);
            if (!$wechat->Upload($bg_img, $access_token, $rst, $error)) { //TODO: 背景图上传微信素材
                print_r($error);
                exit;
            }
            echo "上传微信";
            print_r(microtime(true) - $r);
            echo "<br />";
            print_r(microtime(true) - $e);
            echo "<br />";
            /*$model = new QrcodeImg();
            $model->client_id = $client->client_id;
            $model->media_id = $rst['media_id'];
            $model->pic_url = $bg_url;
            $model->update_time = $rst['created_at'];
            $model->save();*/
            $media_id = '';//$model->media_id;
            @unlink($qrcode_file);
            @unlink($pic_file);
        } else {
            $time = time();
            $outTime = intval(($time - $img->update_time) / 84600);
            if ($outTime >= 3) {
                $rst = (new WeChatUtil())->UploadWeChatImg($img->pic_url, $access_token);
                $img->media_id = $rst['media_id'];
                $img->update_time = $rst['created_at'];
                $img->save();
            }
            $media_id = $img->media_id;
        }

        print_r($media_id);
        $end = microtime(true);
        $rst = $end - $sta;
        echo "<br />";
        print_r($rst);
        exit;
        $msgObj = new MessageComponent($this->data);
        $msgData[] = [
            'msg_type' => '2',
            'media_id' => $media_id,
        ];
        $msgObj->sendMessageCustom($msgData, $openid);


        exit;
        phpinfo();
        exit;
        $stat = microtime(true);

        $data = [
            'nick_name' => 'Gavean',
            'pic' => 'http://wx.qlogo.cn/mmopen/UVzXBswyibFh7ib0qClxDP6Y5EFUGSgrw7FIUNcB7K60LAIpKHpqHxJa7ta10HKYYIVSCPSQy0IBzGib9zgn9NE00vaHbVydjpY/0',
        ];
        $qrcode = 'http://mmbiz.qpic.cn/mmbiz_jpg/6SPlDzxhRsQZgoUE4507ibia0hcWdicibxPLU2JvGjreoJMA9JDzyQK1IFNQb7OrZDx0HsIjgfuL2pJQe4PXrzIUdg/0';
        $rst = UsualFunForNetWorkHelper::HttpGetImg($data['pic'], $content_type, $error);
        if (!$rst) {
            echo "rst :" . "<br />";
            print_r($error);
            exit;
        }
        $res = UsualFunForNetWorkHelper::HttpGet($qrcode);
        if (!$res) {
            echo "res:";
            print_r($error);
            exit;
        }
        $text = $data['nick_name'];
        $time = time();
        $filename = \Yii::$app->basePath . '/web/wswh/img/pic_' . $time . '.png';
        $qrcodename = \Yii::$app->basePath . '/web/wswh/img/qrcode_' . $time . '.png';
        file_put_contents($filename, $rst);
        file_put_contents($qrcodename, $res);

        if (!ImageUtil::imagemaking($qrcodename, $filename, $text, $faaa, $error)) {
            print_r($error);
            exit;
        }

        @unlink($filename);
        @unlink($qrcodename);
        $end = microtime(true);
        $return = $end - $stat;
        echo "<br/>";
        print_r($return);
        exit;

        if (!function_exists('imagecreatefromjpeg')) {
            echo "no";
            exit;
        }
        echo "ok";

        exit;
        $rst = \Yii::$app->basePath . '/web/wswh/img/121e21.jpg';
        $sss = basename($rst);
        print_r($sss);
        exit;
        $userData = WeChatUserUtil::getUserInfo('DRT1eWCeFYoCIn9rm6PvCogwtq0N_clZGYO0di145z-t720_8Lyv2FqyTTsH5bIMEWl8GHLvEMc2GV7pGFUdc-QWkoUcIysViE6KS2_aIL2QbYmeuIliON_mlT4Iv4RyCXQbADDWRK', 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA');
        print_r($userData);
        exit;
        $msg = SystemParamsUtil::GetSystemParam('qrcode_msg', true, 'value1');
        $rst = sprintf($msg, (5 - 3));
        print_r($rst);
        exit;
        $num = 1;
        ++$num;
        print_r($num);
        exit;
        if (!WeChatUserUtil::getWxFansAccumulate('OHa-A3h6P8WswEjgTAPt3YST5q-wqgBwCyYAcEU_JQ752xyO5WHxdKkF5wybPgHD_Of2Fhkdj5Hn0B4TOfrHOyAqfFUVJ-HkhNh0x3T39gZDKyMkM21loCVFre1BJV1pTETfAGDLWL', $rst, $error)) {
            echo "$error \n";
            exit;
        }
        print_r($rst);
        exit;
        $num = AuthorizerUtil::getAttention(3);
        print_r($num);
        exit;
        $data = ['EventKey' => 'qrscene_oB4Z-wf0FYMlI7fW4ZvD90Y06RxA'];
        if (strpos($data['EventKey'], 'qrscene_') !== false) {
            $str = str_replace('qrscene_', '', $data['EventKey']);
            $str = trim($str);
        }

        exit;
        $srt = microtime(true);
        /*
        Array
        (
            [ticket] => gQGO7zwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTUh6THc3NzZkLTExbnJWSnhwY00AAgSveG1ZAwQsAQAA
            [expire_seconds] => 300
            [url] => http://weixin.qq.com/q/02MHzLw776d-11nrVJxpcM
        )*/
        $auth = AuthorizerUtil::getAuthByOne(3);
        $rst = WeChatUserUtil::getQrcodeTickt($auth->authorizer_access_token, $error);
        if (!$rst) {
            print_r($error);
            exit;
        }
        if (!WeChatUserUtil::getQrcodeImg($rst['ticket'], $file)) {
            exit('no');
        }
        $end = microtime(true);
        $time = $end - $srt;
        print_r($time);
        print_r($file);
        exit;


        exit;
        imagejpeg($source_image);
        //imagedestroy($source_image);
        exit;
        if (!WeChatUserUtil::getWxFansAccumulate('yrP4t2AdRJ6qrDdfi4Xrg80OLYqlAQF6O3CHC1zFnZnyZy5ctYRDensz_w24hbPKZfCjVYpIBHCFi46scFABRt8eJtNlxMmzdeHwYFu440_Vq6Xn1wO-7-Cf9xgdi7X1ELUfAIDVCL', $rst, $error)) {
            print_r($error);
            exit;
        }
        print_r($rst);
        exit;
        $data = ['appid' => 'wx1024c6215af20360'];
        $msgData = new MessageComponent($data, 0);
        $sr = $msgData->getMessageModel();
        print_r($sr);
        exit;
        $AuthInfo = AuthorizerUtil::getAuthOne('wxfb4431191609bd1e');
        print_r($AuthInfo);
        exit;
        //TODO: 处理回复消息逻辑 走客服消息接口 回复多条消息
        $auth = AuthorizationList::findOne(['record_id' => 3]);
        $item = [
            'msg_type' => '2',
            'media_id' => 'sRc34mo9KOo4XiPGhZ33oivTle80mXAM2cWDn96XqU7krMdSmkDM8Kg1_uMBADBb',
        ];
        $paramData = [
            'key_word' => 'wechat',
            'open_id' => 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA',
            'authorizer_access_token' => $auth->authorizer_access_token,
            'item' => $item
        ];
        if (!JobUtil::AddCustomJob('wechatBeanstalk', 'wechat', $paramData, $error)) {
            print_r($error);
            exit;
        }
        echo "ok";
        exit;
        //$msgData = AuthorizerUtil::getAttentionMsg(3,1,11);
        $auth = AuthorizationList::findOne(['record_id' => 3]);
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=%s',
            $auth->authorizer_access_token);
        $openid = 'oB4Z-wf0FYMlI7fW4ZvD90Y06RxA';
        $data = [
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content' => 'eeee'
            ]
        ];
        $data = [
            'touser' => $openid,
            'msgtype' => 'news',
            'news' => [
                'articles' => [[
                    'title' => 'qqq',
                    'description' => 'sasaaaa',
                    'url' => 'www.aaa.com',
                    'picurl' => 'qwqewqewq',
                ]
                ]
            ],
        ];
        $data = [
            'touser' => $openid,
            'msgtype' => 'image',
            'image' => [
                'media_id' => 'sRc34mo9KOo4XiPGhZ33oivTle80mXAM2cWDn96XqU7krMdSmkDM8Kg1_uMBADBb'
            ]
        ];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        print_r($rst);
        exit;
        //\Yii::error('curlData::'.var_export($data,true));


        $wx = new WeChatUtil();
        $rst = $wx->UploadWeChatImg('http://7xld1x.com1.z0.glb.clouddn.com/headbg1.jpg', 'E1APCI4AD4kk4kgjQybt6wwEQ9IM0Qy5wPa0hUFJVFRHO6Jok12MUwvcljKOI9BkElnXWXPQMbc1v8Y3qWCK10axeNf7n9nmSVvhG_uHmCRRapAWnWKbHUQxO8K80IBTYGSfAKDCKW');
        print_r($rst);
        exit;
        $wx = new WeChatUtil();
        $rst = $wx->UploadWeChatImg('http://7xld1x.com1.z0.glb.clouddn.com/headbg1.jpg');
        print_r($rst);
        exit;
        //      增加消息队列
        if (!JobUtil::AddCustomJob('beanstalk', 'tube', '', $error)) {
            print_r($error);
            exit;
        }
        echo "ok";
        exit;
        $params = \Yii::$app->params['QiNiuOss'];
        $auth = new Auth($params['ak'], $params['sk']);
        $bucket = $params['bucket'];
        print_r($bucket);
        exit;
        $token = $auth->uploadToken($bucket);
        $cache = WeChatUserUtil::getCacheInfo();
        $rst = WeChatUserUtil::getWxFansAccumulate($cache['authorizer_access_token']);
        $res = WeChatUserUtil::getWxFansSummary($cache['authorizer_access_token']);
        print_r($rst);
        print_r($res);

        exit;

        $rst = date('Y-m-d H:i:s', 1499676884);
        print_r($rst);
        exit;
        $query = (new Query())->select(['backend_user_id'])->from('wc_user')->all();
        print_r($query);
        exit;
        $rst = AuthorizerUtil::getMenuCount(3);
        print_r($rst);
        exit;
        $time = intval((time() - '') / 84600);
        print_r($time);
        exit;
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())->select(['menu_id'])->from('wc_authorization_menu')->where(['is_list' => 1])->all();
        AuthorizationMenu::deleteAll(['app_id' => $cacheInfo['record_id']]);
        foreach ($query as $v) {
            AuthorizationMenu::deleteAll(['parent_id' => $v['menu_id']]);
        }
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $access_token = $cacheInfo['authorizer_access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token=$access_token";
        $rst = UsualFunForNetWorkHelper::HttpGet($url);
        $rst = json_decode($rst, true);
        if ($rst['errcode'] != 0) {
            print_r($rst);
            exit;
        }
        $data = $rst['selfmenu_info']['button'];
        foreach ($data as $item) {
            $model = new AuthorizationMenu();
            $model->app_id = $cacheInfo['record_id'];
            if (!isset($item['sub_button'])) {
                $model->type = $item['type'];
                $model->name = $item['name'];
                $model->key_type = isset($item['key']) ? $item['key'] : '';
                $model->is_list = 0;
                $model->save();
            } else {
                $model->is_list = 1;
                $model->name = $item['name'];
                $model->save();
                foreach ($item['sub_button']['list'] as $v) {
                    $list = new AuthorizationMenu();
                    $list->parent_id = $model->menu_id;
                    $list->name = $v['name'];
                    $list->key_type = isset($v['key']) ? $v['key'] : '';
                    $list->url = isset($v['url']) ? $v['url'] : '';
                    $list->type = $v['type'];
                    $list->save();
                }
            }
        }
        print_r($rst);
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = (new Query())
            ->select(['menu_id', 'app_id', 'name', 'ifnull(type,\'\') as type', 'ifnull(key_type,\'\') as key_type', 'is_list'])
            ->from('wc_authorization_menu')
            ->where('app_id = 3')->all();
        //print_r($query);

        $data = [];
        foreach ($query as $key => $v) {
            if (!$v['is_list']) {
                $data['button'][$key] = $v['type'] == 'click' ? ['key' => $v['key_type']] : ['url' => $v['key_type']];
                $data['button'][$key]['name'] = $v['name'];
                $data['button'][$key]['type'] = $v['type'];
            } else {
                $sql = (new Query())
                    ->select(['name', 'type', 'url', 'key_type'])
                    ->from('wc_authorization_menu')
                    ->where('parent_id = :md', [':md' => $v['menu_id']])->all();
                $data['button'][$key] = [
                    'name' => $v['name'],
                ];
                $info = [];
                foreach ($sql as $q => $value) {
                    $info[$q] = $value['type'] == 'click' ? ['key' => $value['key_type']] : ['url' => $value['url']];
                    $info[$q]['name'] = $value['name'];
                    $info[$q]['type'] = $value['type'];
                }
                $data['button'][$key]['sub_button'] = $info;
            }
        }
        print_r($data);
        $access_token = $cacheInfo['authorizer_access_token'];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        print_r($rst);
        exit;

        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $data = [
            'button' => [
                [
                    'type' => 'click',
                    'name' => '点击打开',
                    'key' => 'open',
                ],
                [
                    'name' => '菜单',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '百度',
                            'url' => 'http://www.baidu.com',
                        ],
                        [
                            'type' => 'click',
                            'name' => '点一下赞',
                            'key' => 'click',
                        ]
                    ]
                ],
                [
                    'name' => '其他',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '帮助中心',
                            'url' => 'http://mibo.matewish.cn/mibo/help.html',
                        ]
                    ]
                ]
            ]
        ];
        $access_token = $cacheInfo['authorizer_access_token'];
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        print_r($rst);

        exit;


        exit;
        $url = 'http://7xld1x.com1.z0.glb.clouddn.com/858390311.jpg';
        $WeChat = new WeChatUtil();
        $rst = $WeChat->UploadWeChatImg($url);

    }
}