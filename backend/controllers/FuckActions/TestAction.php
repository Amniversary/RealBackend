<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午3:09
 */

namespace backend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\business\WeChatUserUtil;
use backend\components\TemplateComponent;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Action;
use yii\db\Query;

class TestAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $auth = AuthorizerUtil::getAuthByOne(85);
        $app_id = $auth->record_id;
        $accessToken = $auth->authorizer_access_token;
        $url = 'http://novel.duobb.cn/novel/vipremind?app=4';

        $data = [
            [
                'open_id'=>'oG5UpwKsAdoEb1iGmlgPcTI9jvQg',
                'nick_name'=>"Gavean"
            ]
        ];
        $template = new TemplateComponent($app_id, $accessToken);
        $temp = 'uK2SkIUqJpL5jy_lAauMIvHMp9DuOM9Np0ez3eHm8bE';

        foreach($data as $item) {
            $data = [
                'first'=>[
                    'value'=>"您好，您的免费开通『VIP会员』特权即将到期。",
                    'color'=>'#173177'
                ],
                'keyword1'=>[
                    'value'=>$item['nick_name'],
                    'color'=>'#135EFB',
                ],
                'keyword2'=>[
                    'value'=>'未知',
                    'color'=>'#173177',
                ],
                'keyword3'=>[
                    'value'=>'邀您免费开通【VIP会员】',
                    'color'=>'#FF0000',
                ],
                'keyword4'=>[
                    'value'=>date('Y-m-d', strtotime('+3 day')),
                    'color'=>'#135EFB',
                ],
                'remark'=>[
                    'value'=>"限时会员到期之前免费开通永久【VIP会员】，您将永久免费阅读『我的书库』所有书籍。\n点击查看 >>> 开通方法",
                    'color'=>'#173177',
                ]
            ];
            $msg = $template->BuildTemplate($item['open_id'], $temp,$data,$url);
            print_r($template->SendTemplateMessage($msg));
        }
        exit;

        $query = (new Query())
            ->from('wc_client')
            ->select(['client_id','open_id','nick_name','app_id'])
            ->where(['app_id'=>89,'is_vip'=>0,'subscribe'=>1])
            ->limit(100)
            ->all();
        print_r($query);
        exit;
        $content = '{{first.DATA}}
            姓名：{{keyword1.DATA}}
            手机号：{{keyword2.DATA}}
            特权身份：{{keyword3.DATA}}
            到期时间：{{keyword4.DATA}}
            {{remark.DATA}}';
        $content = explode('}}',$content);
        print_r($content);
        $count = count($content) - 1;
        $data = [];
        for($i=0; $i < $count;$i++){
            $data[$i]['text'] = strtok(str_replace('{{','',trim($content[$i])),'：');
            $data[$i]['format'] = strtok(strtok(strstr(trim($content[$i]),'{{'),'{{'),'.');
        }
        print_r($data);
        exit;
        //TODO: 切换模板格式
        $content = '{{first.DATA}}
            姓名：{{keyword1.DATA}}
            手机号：{{keyword2.DATA}}
            特权身份：{{keyword3.DATA}}
            到期时间：{{keyword4.DATA}}
            {{remark.DATA}}';
        $content = explode('}}',$content);
        print_r($content);
        $count = count($content) - 1;
        $data = [];
        for($i=0; $i < $count;$i++){
            $str = strstr(trim($content[$i]),'{{');
            $str = strtok($str,'{{');
            $key = strtok($str,'.');
            $data[] = $key;
        }
        print_r($data);
        exit;
        $auth = AuthorizerUtil::getAuthByOne(84);
        $open_id ='oJXYOwRxeD5L-w6tHu1LsD2oPgTw';
        $data = [
            'msg_type'=> 2,
            'media_id'=> 'KgnT5hzBUX_uFT3D0h7pJNkNuY36wDGl6mjMTrjBNNq_keSPvazpI93Q3VNC8T2-',
        ];
        $msg = WeChatUserUtil::getMsgTemplate($data,$open_id);
         $rst = WeChatUserUtil::sendCustomerMsg($auth->authorizer_access_token,$msg);
        print_r($rst);




        exit;
        $app_token = 'XWca1x9_q1tf9bDY8pI4tbkVVTy1SYMSE7liximiIo4RmmjinXnfCC-mQleejiLIryvCiYhl7kUU3apGgUdUlK8yp75733PinrtgK-7-Rhu-SEC2rs2tphCGaGBTFxFTWXThADAEFB';
        $data = [
            'component_appid'=>'wx25d7fec30752314f',
            'authorizer_appid'=>'wx253ac96ed8cfb4ab',
            'authorizer_refresh_token'=>'refreshtoken@@@Vw8m3230uyrtCRIZUfqudeVVkxXKWJbfV_EjsVt8Nw0'
        ];
        $url = sprintf('https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s',
            $app_token);
        $json = json_encode($data);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);

        print_r($rst);


        exit;




    }

/*[template_list] =>
            [
            [0] => Array
            (
            [template_id] => wMhMFPBJXcb6Ilyt6U8bFQ_0thXdyJM6v6YhnRYBQMI
            [title] => 订阅模板消息
            [primary_industry] =>
            [deputy_industry] =>
            [content] => {{content.DATA}}
            [example] =>
                            )

                        [1] => Array
            (
                [template_id] => iecBsyRQxB25021l4nRpvSkPdPnedWSVZVKTnse4-hI
                    [title] => 特权到期提醒
            [primary_industry] => IT科技
            [deputy_industry] => 互联网|电子商务
            [content] => {{first.DATA}}
            姓名：{{keyword1.DATA}}
            手机号：{{keyword2.DATA}}
            特权身份：{{keyword3.DATA}}
            到期时间：{{keyword4.DATA}}
            {{remark.DATA}}
                                [example] => 您好，您在本店的会员身份已到期。
            姓名 ：张三
            手机号：13711111111
            特权身份：1级会员
            到期时间：2015-07-27 15：00
            您的会员身份已于2015-07-25日15：00到期，如有问题，请直接联系客服电话（400-123456）
                )

        )*/
}