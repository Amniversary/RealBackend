<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午3:09
 */

namespace backend\controllers\FuckActions;


use backend\business\AuthorizerUtil;
use backend\components\TemplateComponent;
use yii\base\Action;
use yii\db\Query;

class TestAction extends Action
{
    public function run()
    {
        echo "<pre>";
        $query = (new Query())
            ->from('wc_client')
            ->select(['client_id','open_id','nick_name','app_id'])
            ->where(['app_id'=>89,'is_vip'=>0,'subscribe'=>1])
            ->limit(100)
            ->all();
        print_r($query);
        exit;
        $auth = AuthorizerUtil::getAuthByOne(89);
        $app_id = $auth->record_id;
        $accessToken = $auth->authorizer_access_token;
        $url = 'http://novel.duobb.cn/novel/vipremind?app=4';

        $data = [
            [
                'open_id'=>'oWrMewqeUq0sdbzlTW-XZm0rPvlg',
                'nick_name' => 'Mr.REE',
            ],
            [
                'open_id'=>'oWrMewvkT27ilO5Nst-lSZFaYkf4',
                'nick_name'=>"Gavean"
            ]
        ];
        $template = new TemplateComponent($app_id, $accessToken);
        $temp = 'DIWFZf7uyxBWl8cC5PldnN4QjY_Iihw6x5Ho6OlIi5Y';

        foreach($data as $item) {
            $data = [
                'first'=>[
                    'value'=>'您好，您的『免费开通会员』特权即将到期。',
                    'color'=>'#173177'
                ],
                'keyword1'=>[
                    'value'=>$item['nick_name'],
                    'color'=>'#173177',
                ],
                'keyword2'=>[
                    'value'=>'未知',
                    'color'=>'#173177',
                ],
                'keyword3'=>[
                    'value'=>'邀您免费开通会员',
                    'color'=>'#173177',
                ],
                'keyword4'=>[
                    'value'=>date('Y-m-d', strtotime('+3 day')),
                    'color'=>'#173177',
                ],
                'remark'=>[
                    'value'=>"会员到期之前免费开通会员，您将永久免费阅读『我的书库』所有书籍，更新量超过一万册。\n点击查看 >>> 开通方法",
                    'color'=>'#173177',
                ]
            ];
            $msg = $template->BuildTemplate($item['open_id'], $temp,$data,$url);
            print_r($template->SendTemplateMessage($msg));
        }

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