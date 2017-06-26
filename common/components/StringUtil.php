<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午5:56
 */

namespace common\components;


class StringUtil
{

    /**
     * 16进制转字节数组
     * @param $hexString
     * @return array
     * @throws Exception
     */
    public static function  hexStrToByte($hexString)
    {
        $hexString = str_replace(' ', '', $hexString);
        if ((strlen($hexString) % 2) != 0)
        {
            throw new Exception("字符数组格式不正确");
        }
        $returnBytes = array();
        for ($i = 0; $i < strlen($hexString); $i+=2)
        {
            $h = substr($hexString, $i, 2);
            $returnBytes[] = hexdec($h);
        }
        return $returnBytes;
    }

    /**
     * 字节转16进制字符串
     * @param $bytes
     */
    public static function byteToHexStr($bytes)
    {
        $rst = '';
        if(!is_array($bytes))
        {
            return $rst;
        }
        foreach($bytes as $b)
        {
            $tmp = dechex($b & 0xff);
            if(strlen($tmp) === 1)
            {
                $tmp = '0'.$tmp;
            }
            $rst .= $tmp;
        }
        return $rst;
    }

    /**
     * 转换一个String字符串为byte数组
     * @param $str 需要转换的字符串
     * @param $bytes 目标byte数组
     * @author Zikie
     */
    public static function getBytesFromStr($string)
    {
        $bytes = array();
        for($i = 0; $i < strlen($string); $i++){
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }


    /**
     * 将字节数组转化为String类型的数据
     * @param $bytes 字节数组
     * @param $str 目标字符串
     * @return 一个String类型的数据
     */
    public static function bytesToStr($bytes)
    {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }

        return $str;
    }


} 