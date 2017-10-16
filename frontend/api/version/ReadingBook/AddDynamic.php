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
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) return false;
        $type = $dataProtocol['data']['type'];
        if ($type == 1) {
            if (!isset($dataProtocol['data']['voice']) ||
                empty($dataProtocol['data']['voice'])
            ) {
                $error = '音频url 不能为空';
                return false;
            }
        }
        $content = '';
        if (!empty($dataProtocol['data']['content'])) {
            $content = $dataProtocol['data']['content'];
            $content = trim($content); //TODO: 清除字符串两边的空格
            $content = str_replace('webp', 'jpg', $content);
            $content = str_replace('<section>', '', $content);
            $content = str_replace('</section>', '', $content);
        }
        $saveData = [
            'title' => $dataProtocol['data']['title'],
            'pic' => $dataProtocol['data']['pic'],
            'type' => $dataProtocol['data']['type'],
            'content' => empty($dataProtocol['data']['content']) ? '' : strval($content),
            'comment_count' => 0,
            'count' => 0,
            'create_at' => time()
        ];
        $model = DynamicUtil::getDynamicModel($saveData);
        $transActions[] = new AddDynamicSaveByTrans($model, ['type' => $type, 'voice' => $dataProtocol['data']['voice']]);
        if (!SaveByTransUtil::SaveByTransaction($transActions, $error, $out)) {
            return false;
        }
        $rstData['code'] = 0;
        return true;
    }


    private function check_params($dataProtocol, &$error)
    {
        $files = ['title', 'pic', 'type'];
        $filesLabel = ['标题', '图片', '动态类型'];
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