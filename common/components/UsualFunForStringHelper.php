<?php
/**
 * 常用函数功能
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午6:55
 */

namespace common\components;


class UsualFunForStringHelper
{

    /**
     * 随机生成红包金额
     * @param $n 红包个数
     * @param $sum  总金额 整数
     * @param $index_max  最大金额在数组中索引
     * @param $error
     * @return array|false
     */
    public static function GenRandRePacketsData($n, $sum, &$index_max, &$error)
    {
        if ($sum < $n) {
            $error = '金额总数不能小于红包个数'; //$error = '金额总数必须大于红包个数';
            return false;
        }
        if ($n > 50) {
            $error = '红包数量不能大于50';
            return false;
        }
        //$sum = $sum * 100;//转为分
        $rst = [];
        $ave = intval($sum / $n);  // 金额除去红包个数 平均值 test:3/3 = 1
        $one_rst = rand(1, $ave); // 随机取1到平均值的数
        $subSum = $one_rst;  // 1
        //$rst[] = $one_rst/100;
        $rst[] = $one_rst; // $rst = ['0'=>1];
        $index_min = 0;
        $index_max = 0;
        $min = $one_rst; // 1
        $max = $one_rst; // 1

        for ($i = 2; $i <= $n; $i++) {                   //(4-1)=3 / ((3-2)+1)= 2
            //     3 / 2 = 1
            //(4-2)=2 / ((3-3)+1)= 1
            //     2 / 1 = 2
            $ave = intval(($sum - $subSum) / ($n - $i + 1)); // 1
            $one_rst = rand(1, $ave); // 1
            if ($min > $one_rst) // 1 > 1
            {
                $min = $one_rst;
                $index_min = $i - 1;
            }
            if ($max < $one_rst) // 1 < 1
            {
                $max = $one_rst;
                $index_max = $i - 1;
            }
            //$rst[] = $one_rst/100;
            $rst[] = $one_rst;
            $subSum += $one_rst; // 1
        }
        $left = $sum - $subSum;

        if ($left > 0) {
            //$rst[$index_min] = ($rst[$index_min] + $left/100);
            $rst[$index_min] = ($rst[$index_min] + $left);
            if ($rst[$index_min] > $max) {
                $max = $rst[$index_min];
                $index_max = $index_min;
            }
        }
        //检测重复的最大值处理，确保最大值唯一
        /*for($i =0; $i < $n; $i++)
        {
            if($rst[$i] === $max && $i !== $index_max)
            {
                $one_rst = $rst[$i] -1;
                $rst[$i] = $one_rst;
                $rst[$index_max] = $max + 1;
                break;
            }
        }*/

        //重新乱序
        shuffle($rst);
        //查找最大值
        $index_max = 0;
        $max = $rst[0];
        for ($i = 1; $i < $n; $i++) {
            if ($rst[$i] > $max) {
                $index_max = $i;
                $max = $rst[$i];
            }
        }
        return $rst;
    }

    /**
     * 生成guid
     * @return string
     */
    public static function CreateGUID()
    {
        if (function_exists('com_create_guid')) {
            $guid = com_create_guid();
            $guid = str_replace('{', '', $guid);
            $guid = str_replace('}', '', $guid);
            return $guid;
        } else {
            mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = //chr(123)// "{"
                substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
            //.chr(125);// "}"
            return $uuid;
        }
    }


    /**
     * 从规定字符中生成固定位数随即串
     * @param int $l 位数
     * @param string $c 数据来源字符串
     * @return string 返回随即串
     */
    public static function mt_rand_str($l, $c = 'abcdefghijklmnopqrstuvwxyz1234567890')
    {
        $lenC = strlen($c);
        for ($s = '', $cl = $lenC - 1, $i = 0; $i < $l; $s .= $c[mt_rand(0, $cl)], ++$i) ;
        return $s;
    }


    /***
     * 将/uXXXX中文转义字符转为中文字符
     */
    public static function jsonRemoveUnicodeSequences($msg)
    {
        return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V', hexdec('U$1')))", $msg);
    }


    /**
     * 生成16位小数随机数和js中的Math.random一致
     * @param int $len
     * @return string
     */
    public static function get_rand_point_data($len = 16)
    {
        $rst = '0.';
        for ($i = 0; $i < $len; $i++) {
            $rst .= mt_rand(0, 9);
        }
        return $rst;
    }

    /**
     * 验证身份证号
     * @param $id_card_no
     * @return bool
     */
    public static function is_identity_card($id_card_no)
    {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91'
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $id_card_no)) return false;

        if (!in_array(substr($id_card_no, 0, 2), $vCity)) return false;

        $id_card_no = preg_replace('/[xX]$/i', 'a', $id_card_no);
        $vLength = strlen($id_card_no);

        if ($vLength == 18) {
            $vBirthday = substr($id_card_no, 6, 4) . '-' . substr($id_card_no, 10, 2) . '-' . substr($id_card_no, 12, 2);
        } else {
            $vBirthday = '19' . substr($id_card_no, 6, 2) . '-' . substr($id_card_no, 8, 2) . '-' . substr($id_card_no, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18) {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i--) {
                $vSubStr = substr($id_card_no, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1) return false;
        }

        return true;
    }

    /**
     * 判断是否都是中文
     * @param $str
     * @return int
     */
    public static function IsAllChinese($str)
    {
        $len = preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $str);
        if ($len) {
            return true;
        }
        return false;
    }

    /**
     * 验证是否为正确手机号
     * @param $phone
     * @return bool
     */
    public static function IsPhoneNum($phone)
    {
        $patten = '/^1[34578]\d{9}$/';
        if (!preg_match($patten, $phone)) {
            return false;
        }
        return true;
    }

    /**
     * 判断时间格式是否为 YYYY-mm-dd HH:ii:ss
     * @param $date
     * @return bool
     */
    public static function IsDateTime($date)
    {
        $patten = '/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/';
        if (!preg_match($patten, $date)) {
            return false;
        }
        return true;
    }

    /**
     * 将秒数转为HH:MM:SS格式的时间
     * @param $seconds
     * @return string
     */
    public static function GetHHMMSSBySeconds($seconds)
    {
        if ($seconds > 3600 * 24) {
            $hours = intval($seconds / 3600);
            $leftSeconds = $seconds - $hours * 3600;
            $time = sprintf('%02s', $hours) . ":" . gmstrftime('%M:%S', $leftSeconds);
        } else {
            $time = gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }

    /**
     * 将秒数转为HH:MM格式的散列数组
     * @param $seconds
     * @return mixed
     */
    public static function GetHHMMBySeconds($seconds)
    {
        if ($seconds > 3600 * 24) {
            $hours = intval($seconds / 3600);
            $leftSeconds = $seconds - $hours * 3600;
            $time['H'] = sprintf('%02s', $hours);
            $time['I'] = gmstrftime('%M', $leftSeconds);
        } else {
            $time['H'] = gmstrftime('%H', $seconds);
            $time['I'] = gmstrftime('%M', $seconds);
        }
        return $time;
    }
} 