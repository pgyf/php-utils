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
    
    /**
     * 检查字符串中是否包含某些字符串
     * @param string       $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' != $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * 字符串转小写
     *
     * @param  string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param  string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }
    
    /**
     * 截取字符串
     *
     * @param  string   $string
     * @param  int      $start
     * @param  int|null $length
     * @return string
     */
    public static function substr(string $string, int $start, int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }
    
    /**
     * 获取指定长度的随机字母数字组合的字符串
     *
     * @param  int $length
     * @param  int $type
     * @param  string $addChars
     * @return string
     */
    public static function random(int $length = 6, int $type = null, string $addChars = ''): string
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书" . $addChars;
                break;
            default:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($length > 10) {
            $chars = $type == 1 ? str_repeat($chars, $length) : str_repeat($chars, 5);
        }
        if ($type != 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $length);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $str .= mb_substr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
            }
        }
        return $str;
    }

}
