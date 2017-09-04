<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/29
 * Time: 下午4:18
 */

namespace frontend\api\version\BooksBackend;


use common\models\ArticleSystemMenu;
use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;
use yii\db\Exception;

class UpdateArticleParams implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        \Yii::error('update_article:'.var_export($data,true));
        if (!$this->Check_Params($data, $error)) {
            return false;
        }
        $qrcode_url = '';
        if (!empty($data['data']['qrcode_url'])) {
            $qrcode_url = $data['data']['qrcode_url'];
        }
        $id = $data['data']['id'];
        $model = ArticlesUtil::GetArticleParamsById($id);
        if (empty($model)) {
            $error = '更新失败, 配置信息不存在';
            return false;
        }
        $model->title = $data['data']['title'];
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

            (new ArticleSystemMenu())->deleteAll(['system_id' => $model->id]);
            $sql = '';
            foreach ($data['data']['carousel'] as $item) {
                $sql .= sprintf('insert into %s_article_system_menu(system_id, carousel_id) VALUES (%s,%s);', \Yii::$app->db->tablePrefix, $model->id, $item);
            }
            $rst = \Yii::$app->db->createCommand($sql)->execute();
            if ($rst <= 0) {
                $error = '保存章节轮播图配置信息失败';
                return false;
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

    private function Check_Params($dataProtocal, &$error)
    {
        $files = ['id', 'title', 'carousel'];
        $fileList = ['配置id', '配置标题', '轮播图'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocal['data'][$files[$i]]) || empty($dataProtocal['data'][$files[$i]])) {
                $error = $fileList[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}