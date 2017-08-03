<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/31
 * Time: 下午1:45
 */

namespace backend\components;


use common\components\UsualFunForNetWorkHelper;

class TemplateComponent
{
    public $AppId;
    public $accessToken;

    public function __construct($app_id = null, $accessToken = null)
    {
        $this->AppId = $app_id;
        $this->accessToken = $accessToken;
    }

    /**
     * 发送模板消息
     */
    public function SendTemplateMessage($data){
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$this->accessToken";
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $res = json_decode(UsualFunForNetWorkHelper::HttpsPost($url,$json),true);
        return $res;
    }

    /**
     *  获取模板列表
     *  $tamplate_id
     */
    public function GetTemplateList(){
        $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=$this->accessToken";
        $res = json_decode(UsualFunForNetWorkHelper::HttpGet($url),true);
        return $res;
    }

    /**
     * 生成模板（消息模板）
     * $touser   接收者openid
     * $template_id  模板ID
     * $data    消息体内容
     *         [
     *              first => [
     *                  value => '',   内容值
     *                  color => ''    字体颜色 (不填默认黑色)
     *              ],
     *              keynote1 => [
     *                  value => '',
     *                  color => ''
     *              ]
     *              .
     *              .
     *              .
     *              remark => [
     *                  value => '',
     *                  color => ''
     *              ]
     *          ]
     */
    public function BuildTemplate($openId, $templateId, $data, $url = ''){
        return $template = [
            'touser' => $openId,
            'template_id' => $templateId,
            'url' => $url,
            'data' => $data
        ];
    }

    /**
     * 设置模板行业信息
     * $data  [
     *            industry_id1 => '1',
     *            industry_id2 => '2',
     *        ]
     */
    public function SetIndustry($data){
        $url = "https://api.weixin.qq.com/cgi-bin/template/api_set_industry?access_token=$this->accessToken";
        $json = json_encode($data);
        $res = UsualFunForNetWorkHelper::HttpsPost($url,$json);
        return $res;
    }

    /**
     * 格式化模板内容
     * @param $content
     * @return array
     *          [
     *              0 => ['text' => 'first.DATA', 'format'=>'first'],
     *              1 => ['text' => '姓名', 'format' => 'keyword1'],
     *              2 => ['text' => '手机号', 'format' => 'keyword2'],
     *              3 => ['text' => '特权身份', 'format' => 'keyword3'],
     *              4 => ['text' => 'remark.DATA', 'format' => 'remark' ]
     *          ]
     */
    public function FormatTemplate($content){
        $content = explode('}}',$content);
        $count = count($content) - 1;
        $data = [];
        for($i=0; $i < $count;$i++){
            $data[$i]['text'] = strtok(str_replace('{{','',trim($content[$i])),'：');
            $data[$i]['format'] = strtok(strtok(strstr(trim($content[$i]),'{{'),'{{'),'.');
        }
        return $data;
    }
}