<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 上午11:35
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByTransactions\SaveByTransaction\AddDynamicSaveByTrans;

class AddDynamic implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($data, $error)) return false;
        $type = $data['data']['type'];
        if ($type == 1) {
            if (!isset($data['data']['voice']) ||
                empty($data['data']['voice'])
            ) {
                $error = '音频url 不能为空';
                return false;
            }
        }
        $content = '';
        if (!empty($data['data']['content'])) {
            $content = $data['data']['content'];
            $content = trim($content); //TODO: 清除字符串两边的空格
            $content = str_replace('webp', 'jpg', $content);
            $content = str_replace('<section>', '', $content);
            $content = str_replace('</section>', '', $content);
        }
        $saveData = [
            'title' => $data['data']['title'],
            'pic' => $data['data']['pic'],
            'type' => $data['data']['type'],
            'content' => empty($data['data']['content']) ? '' : strval($content),
            'comment_count' => 0,
            'count' => 0,
            'create_at' => time()
        ];
        $model = DynamicUtil::getDynamicModel($saveData);
        $transActions[] = new AddDynamicSaveByTrans($model, ['type' => $type, 'voice' => $data['data']['voice']]);
        if (!SaveByTransUtil::SaveByTransaction($transActions, $error, $out)) {
            return false;
        }
        $rstData['code'] = 0;
        return true;
    }


    private function check_params($dataTotal, &$error)
    {
        $files = ['title', 'pic', 'type'];
        $filesLabel = ['标题', '图片', '动态类型'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataTotal['data'][$files[$i]]) || empty($dataTotal['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}