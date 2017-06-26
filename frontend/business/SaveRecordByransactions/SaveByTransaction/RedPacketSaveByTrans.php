<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:52
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;



use common\models\RedPacketMain;
use common\models\RedPacketSon;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\log\Logger;


/**
 * 生成红包主次表
 * Class RedPacketSaveByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class RedPacketSaveByTrans implements ISaveForTransaction
{
    private $redPacketInfo = null;
    private $extend_params = [];

    public function  __construct($record, $extend_params=[])
    {
        $this->redPacketInfo = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        if(!($this->redPacketInfo instanceof RedPacketMain))
        {
            $error = '不是红包主表对象';
            return false;
        }

        if(!$this->redPacketInfo->save())
        {
            $error = '红包主表数据保存失败';
            \Yii::getLogger()->log($error. ' :'.var_export($this->redPacketInfo->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $sql_params = [];
        $sql = 'insert into mb_red_packet_son(gu_id,red_packet_no,red_packet_money,status,luck,create_time)VALUES';

        if($this->redPacketInfo->red_packet_type == 1)
        {
            $indexMax = $this->extend_params['index_max'];
            $i = 1;
            $sumMax = count($this->extend_params['red_packet']);
            foreach($this->extend_params['red_packet'] as $k => $v)
            {
                $luck = 1;
                if($k == $indexMax)
                {
                    $luck = 2;
                }
                $sql_params[':gd'.$i] = $this->redPacketInfo->gu_id;
                $sql_params[':rpn'.$i] = $k + 1;
                $sql_params[':rpm'.$i] = $v;
                $sql_params[':s'.$i] = 0;
                $sql_params[':lk'.$i] = $luck;
                $sql_params[':ct'.$i] = date('Y-m-d H:i:s');
                $sql .=sprintf('(:gd%d,:rpn%d,:rpm%d,:s%d,:lk%d,:ct%d)',$i,$i,$i,$i,$i,$i);
                if($i === $sumMax)
                {
                    $sql .=';';
                }
                else
                {
                    $sql .=',';
                }
                $i++;
            }
        }
        else
        {
            for($i = 1;$i <= $this->redPacketInfo->red_packet_num;$i++)
            {
                $sql_params[':gd'.$i] = $this->redPacketInfo->gu_id;
                $sql_params[':rpn'.$i] = $i ;
                $sql_params[':rpm'.$i] = $this->extend_params['red_money'];
                $sql_params[':s'.$i] = 0;
                $sql_params[':lk'.$i] = 1;
                $sql_params[':ct'.$i] = date('Y-m-d H:i:s');
                $sql .= sprintf('(:gd%d,:rpn%d,:rpm%d,:s%d,:lk%d,:ct%d)',$i,$i,$i,$i,$i,$i);
                if($i == $this->redPacketInfo->red_packet_num)
                {
                    $sql .=';';
                }
                else
                {
                    $sql .=',';
                }
            }

        }

        $rst = \Yii::$app->db->createCommand($sql,$sql_params)->execute();

        if($rst <= 0)
        {
            $error = '红包子表数据保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($rst->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        $outInfo = [
            'gu_id'=>$this->redPacketInfo->gu_id,
        ];

        return true;
    }
} 