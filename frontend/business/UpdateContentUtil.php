<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/14
 * Time: 11:15
 */

namespace frontend\business;

use common\components\PhpLock;
use common\models\UpdateContent;
/**
 * Class 更新内容相关功能
 * @package frontend\business
 */
class UpdateContentUtil
{
    /**
     * 根据某块id获取记录
     * @param $module_id
     */
    public static function GetUpdateItemByModuleId($module_id)
    {
        return UpdateContent::findOne(['module_id'=>$module_id]);
    }

    /**
     * 获取版本信息参数
     */
    public static function GetUpdateVersion()
    {
        $recordList = UpdateContent::find()->all();
        $rst = [];
        foreach($recordList as $oneRecord)
        {
            $rst[$oneRecord->module_id]= [
                'version'=>$oneRecord->version,
                'discribtion'=>$oneRecord->discribtion,
                'link'=> $oneRecord->link,
                'update_content'=>$oneRecord->update_content,
                'force_update'=>$oneRecord->force_update
            ];
        }
        return $rst;
    }

    /**
     * 获取更新版本最新版本
     */
    public static function GetNewestUpdateVersion($reflash = false)
    {
        if($reflash)
        {
            $rst = self::GetUpdateVersion();
            $pStr = serialize($rst);
            \Yii::$app->cache->set('update_version',$pStr);
        }
        else
        {
            $cnt = \Yii::$app->cache->get('update_version');
            if(!isset($cnt))
            {
                $lock = new PhpLock('get_update_version');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('update_version');
                if(!isset($cnt))
                {
                    $rst = self::GetUpdateVersion();
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('update_version',$pStr);
                }
                else
                {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            }
            else
            {
                $rst = unserialize($cnt);
            }

        }
        return $rst;
    }

    /**
     * 更新礼物版本
     * @ $version_type  1:礼物模块  2:轮播图模块
     */
    public static function UpdateGiftVersion(&$error,$version_type=1)
    {
        $version = 'gift_version';
        switch($version_type){
            case 1:
                $version = 'gift_version';
                break;
            case 2:
                $version = 'carousels_version';
                break;
        }
        $sql = 'insert ignore into mb_update_content (module_id,version,inner_version,update_content,force_update) values(:mid,:sion,:ision,:content,:up)';
        $result = \Yii::$app->db->createCommand($sql,[
            ':mid' => $version,
            ':sion' => '0',
            ':ision' => '0',
            ':content' => '无',
            ':up' => 0
        ])->execute();

        $update_sql = 'update mb_update_content set version=version+1,inner_version=inner_version+1 where module_id=\'gift_version\'';
        $update_result = \Yii::$app->db->createCommand($update_sql)->execute();

        if($update_result <= 0){
            $error = '礼物版本更新失败';
            return false;
        }

        return true;
    }
} 