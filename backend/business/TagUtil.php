<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/6
 * Time: 上午10:08
 */

namespace backend\business;


use common\models\SystemTagMenu;
use yii\db\Exception;
use yii\db\Query;

class TagUtil
{
    /**
     * 获取标签配置
     * @return array
     */
    public static function GetTagListName()
    {
        $name = [];
        $articleList = (new Query())
            ->select(['id', 'tag_name'])
            ->from('wc_system_tag')
            ->all();

        foreach ($articleList as $list) {
            $name[$list['id']] = $list['tag_name'];
        }

        $rights = array_chunk($name, 20, true);
        return $rights;
    }

    /**
     * 获取公众号已有标签配置
     * @param $tag_id
     * @return array
     */
    public static function getAuthByTagId($tag_id)
    {
        $query = (new Query())
            ->select(['auth_id'])
            ->from('wc_system_tag_menu')
            ->where(['tag_id' => $tag_id])
            ->all();
        $rst = [];
        foreach ($query as $v) {
            $rst[] = $v['auth_id'];
        }
        return $rst;
    }

    /**
     * 保存标签配置信息
     * @param $params
     * @param $tag_id
     * @param $error
     * @return bool
     * @throws Exception
     */
    public static function SaveTagParams($params, $tag_id, &$error)
    {
        try {
            $trans = \Yii::$app->db->beginTransaction();
            (new SystemTagMenu())->deleteAll(['tag_id'=>$tag_id]);//TODO: 删除用户原有权限数据
            $sql = '';
            $table = \Yii::$app->db;
            foreach ($params as $parList) {
                $sql .= sprintf('insert into %s_system_tag_menu (tag_id, auth_id) values(%s,%s);',$table->tablePrefix,$tag_id,$parList);
            }
            $rst = $table->createCommand($sql)->execute();
            if( $rst <= 0 ){
                throw new Exception('保存权限数据异常');
            }
            $trans->commit();
        } catch(Exception $e) {
            $trans->rollBack();
            $error = $e->getMessage();
            return false;
        }
        return true;
    }
}