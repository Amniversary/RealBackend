<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 上午11:46
 */

namespace console\controllers\BackendActions\BackStatistic;


use backend\business\AuthorizerUtil;
use backend\business\DataCubeUtil;
use backend\business\SaveByTransUtil;
use backend\business\SaveRecordByTransactions\SaveByTransaction\SaveArticleTotalByTrans;
use yii\db\Query;
use yii\helpers\Console;

class ArticleDataCubeAction implements IBackStatistic
{
    function ExecuteStatistic($params, &$outInfo, &$error)
    {
        set_time_limit(0);
        $authList = $this->getAuthList();
        if (empty($authList)) {
            $error = '没有找到公众号列表'."\n";
            return false;
        }
        $count = count($authList);
        $sum = 0 ;
        foreach ($authList as $list) {
            if (!AuthorizerUtil::isVerify($list['verify_type_info'])) {
                continue;
            }
            $auth = AuthorizerUtil::getAuthByOne($list['record_id']);
            $accessToken = $auth->authorizer_access_token;
            $num = 0;
            for ($i = 7; $i >= 1; $i--) {
                $transActions = [];
                $rst = DataCubeUtil::getArticleTotal($accessToken, $i);
                if( empty($rst)  || isset($rst['errmsg'] )) {
                    continue;
                }
                foreach ($rst['list'] as $item) {
                    $transActions[] = new SaveArticleTotalByTrans($item, ['app_id' => $auth->record_id]);
                }
                if (!SaveByTransUtil::RewardSaveByTransaction($transActions, $error, $out)) {
                    return false;
                }
                unset($transActions);
                $num++;
            }
            $time = date('Y-m-d H:i:s');
            $sum ++;
            fwrite(STDOUT, Console::ansiFormat("$auth->nick_name 执行成功 : 更新 $num 条记录  date $time "."\n", [Console::FG_GREEN]));
        }

        echo "共 $count 条记录, 成功执行 $sum 条,  date: ". date('Y-m-d H:i:s');
        return true;
    }


    private function getAuthList()
    {
        $query = (new Query())
            ->select(['record_id', 'verify_type_info', 'nick_name'])
            ->from('wc_authorization_list')
            ->all();

        return $query;
    }
}