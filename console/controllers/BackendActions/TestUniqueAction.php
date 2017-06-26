<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/11
 * Time: 9:39
 */

namespace console\controllers\BackendActions;


use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class TestUniqueAction extends Action
{

    public function run()
    {
        $this->GenUniqueNo();
    }

    private function GenUniqueNo()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2G');
        //测试服务器
        $begin = 10000001;
        $end = 15000000;
        //正式服务器
        $begin = 20000001;
        $end = 25000000;
        //本次测试
        $begin = 30000001;
        $end = 35000000;
        //生成房间号
        $begin = 20000001;
        $end = 20000301;
        //生成1到99999999的所有整数
        $codes = [];
        for ($i = $begin; $i <= $end; $i++)
        {
            $codes[] = sprintf('%08s',$i);
        }
        shuffle($codes);
        $len = count($codes);
        for ($i = 0; $i <= $len; $i++)
        {
            //加入异步任务处理
            $data=[
                'client_no'=>$codes[$i]
            ];
            if(!JobUtil::AddJob('unique_no',$data,$error))
            {
                \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
            }
            echo $i."\n";
        }

        //随机交换数据
/*        for($j = 1; $j <= 10; $j ++)
        {
            for ($i = $begin; $i <= $end; $i++)
            {
                $index = rand($begin,$end);
                $tempCode = $codes[$index];
                $codes[$index] = $codes[$i];
                $codes[$i] = $tempCode;
            }
        }*/

/*        $sqlTemplate = 'insert into mb_client_no_list(client_no,status,is_use)values';
        $sql = $sqlTemplate;
        $count = 0;
        for($i =0; $i < $end; $i ++)
        {
            if($i > 0 && $i % 10000 === 0)
            {
                $sql = substr($sql,0,strlen($sql) -1);
                $rst = \Yii::$app->db->createCommand($sql)->execute();
                $count ++;
                echo strval($count).' '. date('Y-m-d H:i:s').' '.$rst."\n";
                $sql = $sqlTemplate;
            }
            $sql .= sprintf('(%s,1,0),',$codes[$i]);
        }
        if($i % 10000 !== 0)
        {
            $sql = substr($sql,0,strlen($sql) -1);
            $rst = \Yii::$app->db->createCommand($sql)->execute();
            $count ++;
            echo strval($count).' '. date('Y-m-d H:i:s').' '.$rst."\n";
        }*/
        echo 'ok';
    }
} 