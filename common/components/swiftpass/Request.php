<?php
/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 * 
 * ================================================================
 */
namespace common\components\swiftpass;

Class Request
{
    //$url = 'http://192.168.1.185:9000/pay/gateway';

    /**
     * @var ClientResponseHandler $resHandler
     */
    private $resHandler = null;

    /**
     * @var RequestHandler $reqHandler
     */
    private $reqHandler = null;

    /**
     * @var PayHttpClient $pay
     */
    private $pay = null;

    /**
     * @var Config $cfg
     */
    private $cfg = null;
    
    public function __construct(){
        $this->Request();
    }

    public function Request(){
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = new Config();

        $this->reqHandler->setGateUrl($this->cfg->C('url'));
    }

    /**
     * @param $type
     * @param $params
     * @throws \Exception
     * @return array
     */
    private function trade($type, $params)
    {
        $this->reqHandler->setReqParams($params);
        $this->reqHandler->setKey($this->cfg->C('key'));
        $this->reqHandler->setParameter('service', $type);//接口类型：pay.weixin.native
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter("device_info", $this->cfg->getDeviceInfo()); //应用类型，Android传AND_SDK，IOS传IOS_SDK
        $this->reqHandler->setParameter("mch_app_name", $this->cfg->getMchAppName()); //应用名
        $this->reqHandler->setParameter("mch_app_id", $this->cfg->getMchAppId()); //包名
        //通知地址，必填项，接收威富通通知的URL，需给绝对路径，255字符内格式
        $this->reqHandler->setParameter('notify_url', $this->cfg->getNotifyUrl());//目前默认是空格，商户在测试支付和上线时必须改为自己的，且保证外网能访问到
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名

        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                if(!($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0)){
                    throw new \Exception('Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg'));
                }
            } else {
                throw new \Exception('Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message'));
            }
        } else {
            throw new \Exception('Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo());
        }
        // 当返回状态与业务结果都为0时才返回
        return $this->resHandler->getAllParameters();
    }

    /**
     * 提交订单信息
     * @param $params
     * @parma $type
     * @return array
     */
    public function createUnifiedTrade($params, $type)
    {
        $this->cfg->setCfgType($type);
        $resultParams = $this->trade('unified.trade.pay', $params);


        $services = explode('|', $resultParams['services']);
        if (!in_array($this->cfg->C('services'), $services)) {
            throw new \Exception('services type error');
        }

        return array(
            'token_id' => $resultParams['token_id'],
            'services' => $this->cfg->C('services')
        );
    }
    
    /**
     * 异步通知方法，demo中将参数显示在result.txt文件中
     */
    public function callback($type, $callback){
        $xml = file_get_contents('php://input');
        $this->resHandler->setContent($xml);
        $this->cfg->setCfgType($type);
        $this->resHandler->setKey($this->cfg->C('key'));
        if($this->resHandler->isTenpaySign()){
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                $params = $this->resHandler->getAllParameters();
                call_user_func($callback, $params);
				// \Yii::error('接口回调收到通知参数:' . var_export($params, true));
                // Utils::dataRecodes('接口回调收到通知参数',$this->resHandler->getAllParameters());
                echo 'success';
                exit();
            }else{
                echo 'failure1';
                exit();
            }
        }else{
            echo 'failure2';
        }
    }
}
