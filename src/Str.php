<?php

declare (strict_types = 1);

namespace phpyii\utils;

/**
 * Description of Str
 *
 * @author lyf
 */
class Str {

    /**
     * 返回字节数
     * @param string $string 字符串
     * @return int
     */
    public static function byteLength($string) {
        return mb_strlen($string, '8bit');
    }

    /**
     * 字符串长度
     * @param  string  $value
     * @param  string|null  $encoding
     * @return int
     */
    public static function length($value, $encoding = null) {
        if ($encoding) {
            return mb_strlen($value, $encoding);
        }
        return mb_strlen($value);
    }

    /**
     * 以什么开始
     * @param string $string      字符串
     * @param string $with        开始字符串
     * @param bool $caseSensitive 是否区分大小写
     * @param string $encoding    编码
     * @return boolean
     */
    public static function startsWith($string, $with, $caseSensitive = true, $encoding = 'UTF-8') {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            return strncmp($string, $with, $bytes) === 0;
        }
        return mb_strtolower(mb_substr($string, 0, $bytes, '8bit'), $encoding) === mb_strtolower($with, $encoding);
    }

    /**
     * 以什么结束
     * @param type $string         字符串
     * @param type $with           结束字符串
     * @param type $caseSensitive  是否区分大小写
     * @param type $encoding       编码
     * @return boolean
     */
    public static function endsWith($string, $with, $caseSensitive = true, $encoding = 'UTF-8') {
        if (!$bytes = static::byteLength($with)) {
            return true;
        }
        if ($caseSensitive) {
            // Warning check, see https://secure.php.net/manual/en/function.substr-compare.php#refsect1-function.substr-compare-returnvalues
            if (static::byteLength($string) < $bytes) {
                return false;
            }
            return substr_compare($string, $with, -$bytes, $bytes) === 0;
        }
        return mb_strtolower(mb_substr($string, -$bytes, mb_strlen($string, '8bit'), '8bit'), $encoding) === mb_strtolower($with, $encoding);
    }

    /**
     * url和文件名base64编码
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input 字符串
     * @return string encoded string.
     */
    public static function base64UrlEncode($input) {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * url和文件名base64解码
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input 编码字符串
     * @return string decoded string.
     */
    public static function base64UrlDecode($input) {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * 截取字符串
     * @param string $string    字符串
     * @param int $length       长度
     * @param string $suffix    后缀
     * @param string $encoding  编码
     * @return string the truncated string.
     */
    public static function limit($string, $length, $suffix = '...', $encoding = null) {
        if ($encoding === null) {
            $encoding = 'UTF-8';
        }
        if (mb_strlen($string, $encoding) > $length) {
            return rtrim(mb_substr($string, 0, $length, $encoding)) . $suffix;
        }
        return $string;
    }

    /**
     * 单词数截取 只支持英文
     * @param  string  $value
     * @param  int  $words
     * @param  string  $end
     * @return string
     */
    public static function wordsLimit($value, $words = 100, $end = '...') {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || static::length($value) === static::length($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * 替换首次出现的
     * @param  string  $search   要替换的字符串
     * @param  string  $replace  新的字符串
     * @param  string  $subject  原字符串
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ($search == '') {
            return $subject;
        }

        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }
    
    /**
     * 替换最后一次出现的
     * @param  string  $search   要替换的字符串
     * @param  string  $replace  新的字符串
     * @param  string  $subject  原字符串
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * 转为title格式 只支持英文
     * @param  string  $value
     * @return string
     */
    public static function title($value)
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
    

}
