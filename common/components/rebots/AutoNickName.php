<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/10/14
 * Time: 13:33
 */
namespace common\components\rebots;

class AutoNickName
{
    private $xingList = [];
    private $nameList = [];
    private $nameLen = 0;
    private $xingLen = 0;

    function __construct()
    {
        $tmpAry = require(__DIR__.'/rebot_config.php');
        if(!is_array($tmpAry) || !isset($tmpAry['Xing']) || !isset($tmpAry['Name']))
        {
            throw new \yii\base\Exception("config file miss");
        }
        $this->xingList = $tmpAry['Xing'];
        $this->nameList = $tmpAry['Name'];
        $this->nameLen = count($this->nameList);
        $this->xingLen = count($this->xingList);
    }

    /**
     * 获取姓
     * @return string
     */
    private function GetXing()
    {
        return $this->xingList[rand(0,$this->xingLen -1)];
    }

    /**
     * 获取名字
     * @return string
     */
    private function GetMing()
    {
        return $this->nameList[rand(0,$this->nameLen -1)];
    }

    /**
     * 获取昵称
     * @param int $type
     * @return string
     */
    public function GetName($type=0)
    {
        $name = '' ;
        switch($type)
        {
            case 1://2字
                $name = $this->GetXing().$this->GetMing();
                break;
            case 2://随机2、3个字
                $name = $this->GetXing().$this->GetMing();
                if(mt_rand (0,100)>50)
                    $name .= $this->GetMing();
                break;
            case 3://只取姓
                $name = $this->GetXing();
                break;
            case 4://只取名
                $name = $this->GetMing();
                break;
            case 0:
            default://默认情况 1姓+2名
                $name = $this->GetXing().$this->GetMing().$this->GetMing();
        }
        return $name;
    }
} 