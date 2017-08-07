<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午3:57
 */

namespace console\controllers;


use backend\business\AuthorizerUtil;
use backend\business\JobUtil;
use backend\components\TemplateComponent;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByTransactions\TemplateFlagSaveByTrans;
use yii\console\Controller;
use yii\db\Query;

/**
 * 模板二次关注消息脚本
 * Class TemplateController
 * @package console\controllers
 */
class TemplateController extends Controller
{
    public function actionSendtemplatemsg()
    {
        set_time_limit(0);
        $data = \Yii::$app->params['WxAuthParams'];
        $str = implode(',', $data);
        $authList = $this->GetAuthList($str);
        $OneGroupSql = $this->GetOneGroupUserList($str);
        $TowGroupSql = $this->GetTowGroupUserList($str);
        $File = \Yii::$app->basePath .'/../common/config/WxAuthParams.php';
        if(!file_exists($File)) {
            echo '找不到公众号模板配置文件.'."\n";exit;
        }
        $paramsFile = require($File);
        $num = '';
        if(!empty($OneGroupSql)) {
            foreach($OneGroupSql as $one) {
                if($one['temp_num'] >= 1) {
                    continue;
                }
                $accessToken = $authList[$one['app_id']]['access_token'];
                $template_id = $paramsFile[$one['app_id']];
                $template = new TemplateComponent(null, $accessToken);
                $templateMsg = [
                        'first'=>['value'=>"您的免费【VIP会员】已经到期。\n积分换会员活动还剩3天，今日参与只需5个积分可获得【永久VIP资格】，免费阅读所有书籍。", 'color'=>'#173177'],
                        'keyword1'=>['value'=>$one['nick_name'], 'color'=>'#135EFB'],
                        'keyword2'=>['value'=>'未知', 'color'=>'#173177'],
                        'keyword3'=>['value'=>'邀您免费开通【VIP会员】', 'color'=>'#FF0000'],
                        'keyword4'=>['value'=>'今日VIP会员名额还剩 '.rand(3,15).' 个', 'color'=>'#135EFB'],
                        'remark'=>['value'=>"限时会员到期之前免费开通永久【VIP会员】，您将永久免费阅读『我的书库』所有书籍。\n点击查看 >>> 开通方法", 'color'=>'#173177']
                ];
                switch($one['app_id']) {
                    case 76: $num = 0;break;
                    case 84: $num = 2;break;
                    case 85: $num = 1;break;
                    case 86: $num = 3;break;
                    case 89: $num = 4;break;
                }
                $URL = 'http://novel.duobb.cn/novel/vipremind?app='.$num;
                $msg = $template->BuildTemplate($one['open_id'],$template_id,$templateMsg,$URL);
                $res = $template->SendTemplateMessage($msg);
                if($res['errcode'] != 0) {
                    echo "发送模板消息失败 1 :  Nick_name".$one['nick_name'].  '  open_id : '.$one['open_id'] . ' app_id :'. $one['app_id']."\n";
                    continue;
                }
                //TODO : 处理用户标记
                $data = ['client_id'=>$one['client_id'], 'app_id'=> $one['app_id'], 'flag'=> 1];
                $transAction[] = new TemplateFlagSaveByTrans($data);
                if(!SaveByTransUtil::SaveByTransaction($transAction, $error, $out)) {
                    echo $error; continue;
                }
            }
            echo '发送第一批模板消息成功 :  '. date('Y-m-d H:i:s')."\n";
        }

        if(!empty($TowGroupSql)) {
            foreach($TowGroupSql as $tow) {
                if($tow['temp_num'] >= 2){
                    continue;
                }
                $accessToken = $authList[$tow['app_id']]['access_token'];
                $template_id = $paramsFile[$tow['app_id']];
                $template = new TemplateComponent(null, $accessToken);
                $templateMsg = [
                    'first'=>['value'=>'您好，您的免费开通『VIP会员』特权即将到期。', 'color'=>'#173177'],
                    'keyword1'=>['value'=>$tow['nick_name'], 'color'=>'#135EFB'],
                    'keyword2'=>['value'=>'未知', 'color'=>'#173177'],
                    'keyword3'=>['value'=>'邀您免费开通【VIP会员】', 'color'=>'#FF0000'],
                    'keyword4'=>['value'=>date('Y-m-d', strtotime('+3 day')), 'color'=>'#135EFB'],
                    'remark'=>['value'=>"会员到期之前免费开通会员，您将永久免费阅读『我的书库』所有书籍，更新量超过一万册。\n点击查看 >>> 开通方法", 'color'=>'#173177']
                ];
                $msg = $template->BuildTemplate($tow['open_id'],$template_id,$templateMsg,'');
                $res = $template->SendTemplateMessage($msg);
                if($res['errcode'] != 0) {
                    echo "发送模板消息失败 2 :  Nick_name".$tow['nick_name'].  '  open_id : '.$tow['open_id'] . ' app_id :'. $tow['app_id']."\n";
                    continue;
                }
                //TODO : 处理用户标记
                $data = ['client_id'=>$tow['client_id'], 'app_id'=> $tow['app_id'], 'flag'=> 2];
                $transAction[] = new TemplateFlagSaveByTrans($data);
                if(!SaveByTransUtil::SaveByTransaction($transAction, $error, $out)) {
                    echo $error; continue;
                }
            }
            echo '发送第二批模板消息成功 :  '. date('Y-m-d H:i:s')."\n";
        }
    }


    /**
     * 获取第一批关注用户
     */
    private function GetOneGroupUserList($str) {
        $sql = 'select client_id,nick_name,open_id,c.app_id, ifnull(temp_num,0) as temp_num
                from wc_client c left join wc_template_flag f ON  c.client_id = f.user_id and c.app_id = f.app_id
                where client_id = 114567 and is_vip = 0 and c.app_id in ('.$str.') and (create_time BETWEEN :star and :end) and subscribe = 1';
        $OneGroupData =  \Yii::$app->db->createCommand($sql,[
            ':star' => date('Y-m-d H:i:s', time() - 60 * 60),
            ':end' => date('Y-m-d H:i:s', time() - 60 * 30),
        ])->queryAll();
        return $OneGroupData;
    }

    /**
     * 获取第二批关注用户
     */
    private function GetTowGroupUserList($str) {
        $sql ='select client_id,nick_name,open_id,c.app_id,ifnull(temp_num,0) as temp_num
                        from wc_client c left join wc_template_flag f on c.client_id = f.user_id and c.app_id = f.app_id
                        where client_id = 114567 and is_vip = 0 and c.app_id in ('.$str.') and (create_time BETWEEN :star and :end) and subscribe = 1';
        $TowGroupData = \Yii::$app->db->createCommand($sql,[
            ':star'=> date('Y-m-d H:i:s', time() - (60 * (60 * 2 + 30))),
            ':end' => date('Y-m-d H:i:s', time() - 60 * 60 * 2),
        ])->queryAll();
        return $TowGroupData;
    }

    private function GetAuthList($str) {
        $query = ( new Query() )
            ->select(['record_id','nick_name', 'authorizer_access_token as access_token'])
            ->from('wc_authorization_list')
            ->where('record_id in ('.$str.')')
            ->all();
        $data = [];
        foreach($query as $item) {
            $data[$item['record_id']] = $item;
        }
        return $data;

    }
}