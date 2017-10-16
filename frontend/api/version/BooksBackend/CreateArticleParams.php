<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/29
 * Time: 下午3:51
 */

namespace frontend\api\version\BooksBackend;


use common\models\ArticleSystemMenu;
use common\models\ArticleSystemParams;
use frontend\api\IApiExecute;
use yii\db\Exception;

class CreateArticleParams implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->Check_Params($dataProtocol, $error)) {
            return false;
        }
        $qrcode_url = '';
        if (!empty($dataProtocol['data']['qrcode_url'])) {
            $qrcode_url = $dataProtocol['data']['qrcode_url'];
        }
        $carousel = $dataProtocol['data']['carousel'];
        if (!is_array($carousel)) {
            $error = '数据错误 , 轮播图不是数组对象';
            return false;
        }
        $model = new ArticleSystemParams();
        $model->title = $dataProtocol['data']['title'];
        $model->qrcode_url = $qrcode_url;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        try {
            $trans = \Yii::$app->db->beginTransaction();
            if (!$model->save()) {
                $error = '保存章节配置记录信息失败';
                \Yii::error($error . ' :' . var_export($model->getErrors(), true));
                return false;
            }
            if (!empty($carousel)) {
                (new ArticleSystemMenu())->deleteAll(['system_id' => $model->id]);
                $sql = '';
                foreach ($dataProtocol['data']['carousel'] as $item) {
                    $sql .= sprintf('insert into %s_article_system_menu(system_id, carousel_id) VALUES (%s,%s);', \Yii::$app->db->tablePrefix, $model->id, $item);
                }
                $rst = \Yii::$app->db->createCommand($sql)->execute();
                if ($rst <= 0) {
                    $error = '保存章节轮播图配置信息失败';
                    return false;
                }
            }
            $trans->commit();
        } catch (Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function Check_Params($dataProtocol, &$error)
    {
        $files = ['title', 'carousel'];
        $fileList = ['配置标题', '轮播图'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error = $fileList[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}