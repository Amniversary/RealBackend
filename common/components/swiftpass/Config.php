<?php
namespace common\components\swiftpass;

class Config
{
    private $cfg = array(
        'url'     => 'https://pay.swiftpass.cn/pay/gateway',//接口请求地址，固定不变，无需修改
        'mchId'   => '101520000465',//测试商户号，商户需改为自己的 101570038502
        'key'     => '58bb7db599afc86ea7f7b262c32ff42f',//测试密钥，商户需改为自己的 dd5fbc8972c7c171c1401cbe8ca8e26d
        'version' => '2.0',
        'services' => 'pay.weixin.wappay',
       );

    const ALIPAY_SERVICE_TYPE = 20;
    const WEIXIN_SERVICE_TYPE = 21;
    const QQPAY_SERVICE_TYPE  = 22;
    /**
     * @var array
     * services
     * pay.alipay.jspay|pay.alipay.micropay|pay.alipay.native|pay.weixin.micropay
     */
    private $testCfg = [
        self::ALIPAY_SERVICE_TYPE => [
            'mchId' => '101520000465',
            'key'   =>  '58bb7db599afc86ea7f7b262c32ff42f',
            'services' => 'pay.alipay.jspay',
            'notifyUrl' => 'http://fronttest.mblive.cn/swiftpass/alipay_notify'
        ],
        self::WEIXIN_SERVICE_TYPE => [
            'mchId' => '7552900037',
            'key'   => '11f4aca52cf400263fdd8faf7a69e007',
            'services' => 'pay.weixin.wappay',
            'notifyUrl' => 'http://fronttest.mblive.cn/swiftpass/weixin_notify'
        ],
        self::QQPAY_SERVICE_TYPE => [
            'mchId' => '755110002853',
            'key'   => '385abd5f2a3a101c125bae7b',
            'services' => 'pay.tenpay.wappay',
            'notifyUrl' => 'http://fronttest.mblive.cn/swiftpass/tenpay_notify'
        ],
    ];

    public $notifyUrl = 'http://fronttest.mblive.cn/swiftpass/notify';

    public $mchAppName = '蜜播';

    public $mchAppId = 'mibo';

    public $deviceInfo = 'AND_SDK';

    public $deviceType;

    
    public function C($cfgName){
        return $this->cfg[$cfgName];
    }

    /**
     * 设置支付参数
     * @param $type
     * @return $this
     */
    public function setCfgType($type)
    {
        if (!isset($this->testCfg[$type])) {
            return $this;
        }
        $cfg = $this->testCfg[$type];
        $this->cfg = array_merge($this->cfg, $cfg);
        $this->notifyUrl = $cfg['notifyUrl'];
        return $this;
    }

    /**
     * 设置设备类型
     * @param $type
     * @return $this
     */
    public function setDeviceType($type)
    {
        $this->deviceType = $type;
        return $this;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function getMchAppName()
    {
        return $this->mchAppName;
    }

    public function getMchAppId()
    {
        return $this->mchAppId;
    }

    public function getDeviceInfo()
    {
        return $this->deviceInfo;
    }
}