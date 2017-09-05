<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午5:57
 */

namespace common\components;

use yii\base\Exception;
use \yii\db\Connection;
use yii\log\Logger;

/**
 *流水号生成类
 * @package common\components
 */
class WaterNumUtil
{
    /**
     * 从已有数据库中获取id
     * @param $error
     * @return bool
     */
    public static function GetUniqueIdFromTable(&$error)
    {
        $phpLock = new PhpLock('system_waternum_generate_from_exist_table');
        $phpLock->lock();
        $trans =\Yii::$app->db->beginTransaction();
        try
        {
            $sql='select client_no, @rid :=record_id from mb_client_no_list where status = 1 and is_use = 0 limit 1 for update;
                  update mb_client_no_list set is_use = 1 where record_id = @rid and status = 1 and is_use = 0;';
            $data = \Yii::$app->db->createCommand($sql)->queryOne();
            if($data === false)
            {
                $phpLock->unlock();
                $error = '未获取到数据请从新获取';
                $trans->rollBack();
                return false;
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $phpLock->unlock();
            $error = $e->getMessage();
            $trans->rollBack();
            $error = 'sql执行错误';
            return false;
        }
        $phpLock->unlock();
        return $data['client_no'];
    }
    /**
     * 生成流水号
     * @param type $preName 流水号前缀
     *  @param type $isIncludePre bool 流水号是否包含前缀
     *  @param type $isIncludeDate bool  流水号是否包含日期部分
     * @return string 返回流水号
     */
    public  static function GenWaterNum($preName = 'NO.',$isIncludePre = true, $isIncludeDate = true,$date ='' ,$length = 4)
    {
        $connection =new Connection(['dsn'=>\Yii::$app->db->dsn,
            'username'=>\Yii::$app->db->username,
            'password'=>\Yii::$app->db->password]);
        if(empty($date))
        {
            $sql = "select date_format(current_date(),'%Y%m%d')";
            $command = $connection->createCommand($sql);
            $date = $command->queryScalar();
            //$connection->setActive(FALSE);
        }
        $preName = str_replace('\'', '', $preName);
        $date = str_replace('\'', '', $date);


        //$connection->setAutoCommit(FALSE);
        //$sql= "set global transaction ISOLATION LEVEL SERIALIZABLE;";
        //设置了上面语句依然会死锁，写覆盖死锁，使用下面锁定方式确保没问题
        $sql= "SELECT GET_LOCK('waternum_unique_lock',10);";
        $connection->createCommand($sql)->execute();
        $trans = $connection->beginTransaction();
        $sql=  sprintf("insert ignore  into waternumgenerate(prename,createdate,waternum,remark)
(select '%s' as prename,'%s' as createdate,1 as waternum, '' as remark)", $preName, $date);
        $connection->createCommand($sql)->execute();

        $sql =  sprintf('select waternum from waternumgenerate
where prename = \'%s\' and createdate=\'%s\'  limit 0,1 for update;
', $preName, $date);
        $waternum = $connection->createCommand($sql)->queryScalar();

        $sql=sprintf('
update waternumgenerate set waternum = waternum + 1
where prename = \'%s\' and createdate=\'%s\';
',$preName, $date);
        $connection->createCommand($sql)->execute();

        $trans->commit();

        $sql = 'SELECT RELEASE_LOCK(\'waternum_unique_lock\')';
        $connection->createCommand($sql)->execute();
        $connection->close();

        $waternum = sprintf('%0'.$length.'s' ,$waternum);

        if($isIncludeDate)
        {
            $waternum =  substr($date,2,8).$waternum;
        }
        if($isIncludePre)
        {
            $waternum = $preName.$waternum;
        }
        //$waternum = $preName . substr($date,2,7) . $waternum;
        return $waternum;
    }

    /**
     * 生成随机唯一数字，位数5,7,9,11方式递增
     * @param int $u_len
     * @return string
     */
    public static function GetRandUniqueNo($u_len = 2)
    {
        $no = WaterNumUtil::GenWaterNum('client_user_no',false,false,'2016-05-13',$u_len);
        $len = strlen($no);
        $randLen = $len + 1;
        $randStr = UsualFunForStringHelper::mt_rand_str($randLen,'0123456789');
        $rstStr = '';
        for($i =0; $i <$len; $i ++)
        {
            $rstStr .= $randStr[$i].$no[$i];
        }
        $rstStr .= $randStr[$i];
        return $rstStr;
    }

    public static function GenWaterNumUseLock($preName = 'NO.',$isIncludePre = true, $isIncludeDate = true,$date ='' ,$length = 4)
    {
        $connection = new Connection(['dsn'=>\Yii::$app->db->dsn,
            'username'=>\Yii::$app->db->username,
            'password'=>\Yii::$app->db->password]);
        if(empty($date))
        {
            $sql = "select date_format(current_date(),'%Y%m%d')";
            $command = $connection->createCommand($sql);
            $date = $command->queryScalar();
            //$connection->setActive(FALSE);
        }
        $preName = str_replace('\'', '', $preName);
        $date = str_replace('\'', '', $date);

        $path = $_SERVER['DOCUMENT_ROOT'] . '/lock';
        $lock = new PhpLock('waternumberlock', $path);
        $lock->lock();

        //$connection->setAutoCommit(FALSE);
        //$sql= "set global transaction ISOLATION LEVEL SERIALIZABLE;";
        //设置了上面语句依然会死锁，写覆盖死锁，使用下面锁定方式确保没问题
//        $sql= "SELECT GET_LOCK('lock1',10);";
//        $connection->createCommand($sql)->execute();
        $trans = $connection->beginTransaction();
        $sql=  sprintf("insert ignore  into waternumgenerate(prename,createdate,waternum,remark)
(select '%s' as prename,'%s' as createdate,1 as waternum, '' as remark)", $preName, $date);
        $connection->createCommand($sql)->execute();

        $sql =  sprintf('select waternum from waternumgenerate
where prename = \'%s\' and createdate=\'%s\'  limit 0,1 for update;
', $preName, $date);
        $waternum = $connection->createCommand($sql)->queryScalar();

        $sql=sprintf('
update waternumgenerate set waternum = waternum + 1
where prename = \'%s\' and createdate=\'%s\';
',$preName, $date);
        $connection->createCommand($sql)->execute();


        $trans->commit();

        //$connection->setActive(FALSE);
        $lock->unlock();
        $waternum = sprintf('%0'.$length.'s' ,$waternum);

        if($isIncludeDate)
        {
            $waternum =  substr($date,2,7).$waternum;
        }
        if($isIncludePre)
        {
            $waternum = $preName.$waternum;
        }
        //$waternum = $preName . substr($date,2,7) . $waternum;
        return $waternum;
    }

} 