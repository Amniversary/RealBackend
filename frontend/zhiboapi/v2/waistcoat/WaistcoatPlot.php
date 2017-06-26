<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/23
 * Time: 16:15
 */

namespace frontend\zhiboapi\v2\waistcoat;
use yii\base\Exception;
use yii\log\Logger;

class WaistcoatPlot
{
    private $dataParams;

    public function __construct( $dataProtocal )
    {
        $this->dataParams = $dataProtocal;
    }

    public function DoAction(){
        $rstOut = [
            "errno"=>"0",
            "errmsg"=>"提示信息",
            "has_data"=>"0",
            "data_type"=>"string",
            "data"=>""
        ];
        $error  = '';
        $configFile = \Yii::$app->getBasePath().'/zhiboapi/v2/waistcoat/WaistcoatConfig.php';
        if(!file_exists($configFile))
        {
            $rst['errmsg'] = '找不到配置文件:WaistcoatConfig';
            \Yii::getLogger()->log('WaistcoatConfig文件找不到'.var_export($rst['errmsg'],true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $className = $this->dataParams['app_id']."_".$this->dataParams['action_name'];
        $funData = require($configFile);
        if(!isset($funData[$className]))
        {
            $rst['errmsg'] = '找不到对应的处理类';
            \Yii::getLogger()->log('v2找不到对应的处理类:'.$className,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $actionClass = 'frontend\zhiboapi\v2\waistcoat\\'.$funData[$className];
        if(!class_exists($actionClass))
        {
            $rst['errno'] = '14';
            $rst['errmsg'] = '对应的功能不存在';
            \Yii::getLogger()->log($rst['errmsg'].' class not exists,action:'.$actionClass.'; file:'.$actionClass,Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        //写入协议访问日志
        $errorMsg = '';
        $fun = new $actionClass;
        try
        {
            if(!$fun->action($this->dataParams, $rstOut,$errorMsg))
            {
                if(is_array($errorMsg))
                {
                    $rst = $errorMsg;
                }
                else
                {
                    $rst['errno'] = '15';
                    $rst['errmsg'] = $errorMsg;
                }
                \Yii::getLogger()->log($rst['errmsg'].' 功能执行有异常，actionClass:'.$actionClass,Logger::LEVEL_ERROR);
                echo json_encode($rst);
                exit;
            }
        }
        catch(Exception $e)
        {
            \Yii::getLogger()->log('调用相应的马甲号程序处理时发生了错误:'.$e->getMessage(),Logger::LEVEL_ERROR);
            exit;
        }

        return $rstOut;
    }
}