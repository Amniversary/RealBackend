<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/7/15
 * Time: 10:31
 */

namespace common\components;


class XmlUtil
{
    /**
     * 是否是xml字符串
     * @param $xmlStr
     * @param string $encoding
     * @return bool
     */
    public static function IsXml($xmlStr,$encoding='utf-8')
    {
        $xml_parser = xml_parser_create($encoding);
        $rst_xml = xml_parse($xml_parser,$xmlStr,true);
        return $rst_xml === 1;
    }
} 