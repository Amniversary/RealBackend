<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午2:37
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;

class GetDynamic implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if(!$this->check_params($dataProtocol, $error)) return false;
        $dynamic_id = $dataProtocol['data']['dynamic_id'];
        $Dynamic = DynamicUtil::getDynamicById( $dynamic_id);
        if(empty($Dynamic)) {
            $error = '动态记录信息不存在或已删除';
            return false;
        }
        $voice = DynamicUtil::getVoiceById($Dynamic->dynamic_id);
        if($dataProtocol['data']['type'] == 1) {
            $sql = 'update wc_studying_dynamic set count = count + 1 where dynamic_id = :dy';
            $res = \Yii::$app->db->createCommand($sql,[':dy'=>$Dynamic->dynamic_id])->execute();
            if($res <= 0) {
                $error = '更新点击次数失败';
                \Yii::error($error.' ' . \Yii::$app->db->createCommand($sql,[':dy'=>$Dynamic->dynamic_id])->rawSql);
                return false;
            }
        }


        $callback = [
            'dynamic_id'=>$Dynamic->dynamic_id,
            'title' => $Dynamic->title,
            'pic'=> $Dynamic->pic,
            'content'=> htmlspecialchars_decode($Dynamic->content),
            'voice'=> empty($voice->voice) ? '': $voice->voice,
            'count' => intval($Dynamic->count),
            'comment_count'=> intval($Dynamic->comment_count),
            'create_at' => $Dynamic->create_at
        ];

        $rstData['code'] = 0;
        $rstData['data'] = $callback;
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['dynamic_id', 'type'];
        $filesLabel = ['动态id', '动态类型'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}