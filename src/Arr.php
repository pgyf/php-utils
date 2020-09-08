<?php

declare (strict_types = 1);

namespace phpyii\utils;

use ArrayAccess;
use Traversable;
use InvalidArgumentException;

/**
 * 数组类
 * @author 最初的梦想
 */
class Arr {
    
    /**
     * Determine whether the given value is array accessible.
     * 判断这个对象是否可用数组访问
     * @param mixed $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
    
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     * 把数组划分成2个数组 0键 1值
     * @param array $array
     * @return array
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
    }
    
    
    /**
     * 二维数组添加索引
     * @param array $array 数组
     * @param string $key  索引字段
     * @return array
     */
    public static function index(array $array, string $key) {
        $result = [];
        foreach ($array as $item) {
            if(isset($item[$key])){
                $result[$item[$key]] = $item;
            }
        }
        return $result;
    }
    
    
    /**
     * 将2个有关联的数组通过关系组合起来
     * @param array $pkArray 主数组
     * @param array $fkArray 关联数组
     * @param string $pk   主键字段
     * @param string $fk    外键字段
     * @param string $relation  关系名
     * @return array
     */
    public static function relationSet(array $pkArray, array $fkArray, string $pk, string $fk, string $relation = 'relation') :array {
        $result = [];
        if(!empty($pkArray)){
            $fkArray = self::index($fkArray, $fk);
            foreach ($pkArray as $k => $pkItem) {
                if (isset($pkItem[$pk]) && isset($fkArray[$pkItem[$pk]])) {
                    $pkItem[$relation] = $fkArray[$pkItem[$pk]];
                }
                else{
                   $pkItem[$relation] = []; 
                }
                $result[$k] = $pkItem;
            }
        }
        return $result;
    }
    
    /**
     * 把数组2中的关联数据追加到到数组1中
     * @param array $pkArray   主数组
     * @param array $fkArray   关联数组
     * @param string $pk       主键字段
     * @param string $fk       外键字段
     * @param array $fkKeys    需要合并的键
     * @return array
     */
    public static function relationAppendKey(array $pkArray, array $fkArray, string $pk, string $fk, array $fkKeys, $isCover = false) {
        $result = [];
        if(!empty($pkArray)){
            $fkArray = self::index($fkArray, $fk);
            foreach ($pkArray as $k => $pkItem) {
                if (isset($pkItem[$pk]) && isset($fkArray[$pkItem[$pk]])) {
                    $fkItem = $fkArray[$pkItem[$pk]];
                    foreach ($fkKeys as $fkKey => $fkKeyValue) {
                        if(!isset($pkItem[$fkKey]) || $isCover){
                            if(isset($fkItem[$fkKey])){
                                $pkItem[$fkKey] = $fkItem[$fkKey]; 
                            }
                            else{
                                $pkItem[$fkKey] = $fkKeyValue; 
                            }
                        }
                    }
                }
                else{
                    foreach ($fkKeys as $fkKey => $fkKeyValue) {
                        if(!isset($pkItem[$fkKey]) || $isCover){
                            $pkItem[$fkKey] = $fkKeyValue; 
                        }
                    }
                }
                $result[$k] = $pkItem;
            }
        }
        return $result;
    }
    
    /**
     * 随机返回数组的值
     * @param array $array    数组
     * @param int $len  数量
     * @return array|bool|mixed
     */
    public static function random($array, $len = 1)
    {
        if (!is_array($array)) {
            return false;
        }
        $keys = array_rand($array, $len);
        if ($len === 1) {
            return $array[$keys];
        }
        return array_intersect_key($array, array_flip($keys));
    }
    
    /**
     * 返回两个数组中不同的元素
     * @param array $array
     * @param array $array1
     * @return array
     */
    public static function diffBoth($array, $array1)
    {
        return array_merge(array_diff($array, $array1), array_diff($array1, $array));
    }
    
    /**
     * 统计一维数组元素出现的次数
     * @return array|bool
     */
    public static function count(...$args)
    {
        $data = $args;
        $num = count($args);
        $result = [];
        if ($num > 0) {
            for ($i = 0; $i < $num; $i ++) {
                foreach ($data[$i] as $v) {
                    if (isset($result[$v])) {
                        $result[$v] ++;
                    } else {
                        $result[$v] = 1;
                    }
                }
            }
            return $result;
        }
        return $result;
    }
    
    
   /**
     * 数组转为查询字符串
     * @param  array  $array
     * @return string
     */
    public static function query($array)
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }
    
    
    /**
     * If the given value is not an array and not null, wrap it in one.
     * 把一个不是数组的变量用数组包装
     * @param  mixed  $value
     * @return array
     */
    public static function wrap($value)
    {
        if (is_null($value)) {
            return [];
        }
        return is_array($value) ? $value : [$value];
    }
    
    /**
     * 把值插入数组开始位置
     * @param array $array  数组
     * @param mixed $value  值
     * @param mixed $key    键
     * @return array 新数组
     */
    public static function prepend($array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }
        return $array;
    }
    
    /**
     * 判断是否不是为0开始的下标索引的数组
     * @param array $array
     * @return bool
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
    
    /**
     * 数组排序，多维递归排序
     * @param array $array
     * @return array
     */
    public static function sortRecursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }
        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }
        return $array;
    }
    
    
    /**
     * 打乱数组顺序
     * @param array    $array
     * @param int|null $seed
     * @return array
     */
    public static function shuffle($array, $seed = null)
    {
        if (is_null($seed)) {
            shuffle($array);
        } else {
            srand($seed);
            usort($array, function () {
                return rand(-1, 1);
            });
        }
        return $array;
    }
    
    
    
    /**
     * 递归合并数组  如果键有相同 后覆盖前
     * @param array $args
     * @return array
     */
    public static function merge(...$args)
    {
        $res = array_shift($args);
        while (!empty($args)) {
            foreach (array_shift($args) as $k => $v) {
                if (is_int($k)) {
                    if (array_key_exists($k, $res)) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }
    
    /**
     * 移除数组中的元素 返回移除的元素
     * @param array $array
     * @param string $key
     * @param mixed  $default
     * @return mixed|null
     */
    public static function remove(&$array, $key, $default = null)
    {
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
            $value = $array[$key];
            unset($array[$key]);

            return $value;
        }

        return $default;
    }
    
    
    /**
     * 移除数组中的值
     * @param array $array
     * @param string $value
     * @return array
     */
    public static function removeValue(&$array, $value)
    {
        $result = [];
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if ($val === $value) {
                    $result[$key] = $val;
                    unset($array[$key]);
                }
            }
        }
        return $result;
    }
    
    
    /**
     * 检查数组中键是否存在
     * @param string $key
     * @param array|ArrayAccess $array
     * @param bool $caseSensitive   是否区分大小写
     * @return bool
     */
    public static function keyExists($key, $array, $caseSensitive = true)
    {
        if ($caseSensitive) {
            // Function `isset` checks key faster but skips `null`, `array_key_exists` handles this case
            // https://secure.php.net/manual/en/function.array-key-exists.php#107786
            if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array))) {
                return true;
            }
            // Cannot use `array_has_key` on Objects for PHP 7.4+, therefore we need to check using [[ArrayAccess::offsetExists()]]
            return $array instanceof ArrayAccess && $array->offsetExists($key);
        }

        if ($array instanceof ArrayAccess) {
            throw new InvalidArgumentException('Second parameter($array) cannot be ArrayAccess in case insensitive mode');
        }

        foreach (array_keys($array) as $k) {
            if (strcasecmp($key, $k) === 0) {
                return true;
            }
        }

        return false;
    }
    
    
    /**
     * 通过键获取数组的值
     * @param array|object $array
     * @param string|\Closure|array $key
     * @param mixed $default
     * @return mixed
     * @throws \Exception
     */
    public static function getValue($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }

        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = static::getValue($array, $keyPart);
            }
            $key = $lastKey;
        }

        if (static::keyExists($key, $array)) {
            return $array[$key];
        }

        if (($pos = strrpos($key, '.')) !== false) {
            $array = static::getValue($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }

        if (static::keyExists($key, $array)) {
            return $array[$key];
        }
        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            try {
                return $array->$key;
            } catch (\Exception $e) {
                if ($array instanceof ArrayAccess) {
                    return $default;
                }
                throw $e;
            }
        }

        return $default;
    }
    
    
    /**
     * 获取数组中1列
     * @param array $array
     * @param int|string|\Closure $name
     * @param bool $keepKeys  是否保持原来的key
     * will be re-indexed with integers.
     * @return array the list of column values
     */
    public static function getColumn($array, $name, $keepKeys = true)
    {
        $result = [];
        if ($keepKeys) {
            foreach ($array as $k => $element) {
                $result[$k] = static::getValue($element, $name);
            }
        } else {
            foreach ($array as $element) {
                $result[] = static::getValue($element, $name);
            }
        }

        return $result;
    }

    
    /**
     * 把数组返回一个键值对形式的数组
     * @param array $array
     * @param string|\Closure $from
     * @param string|\Closure $to
     * @param string|\Closure $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $value = static::getValue($element, $to);
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
    
    /**
     * 按照一个键排序
     * @param array $array
     * @param string|\Closure|array $key 
     * @param int|array $direction the sorting direction. It can be either `SORT_ASC` or `SORT_DESC`.
     * @param int|array $sortFlag the PHP sort flag. Valid values include
     * `SORT_REGULAR`, `SORT_NUMERIC`, `SORT_STRING`, `SORT_LOCALE_STRING`, `SORT_NATURAL` and `SORT_FLAG_CASE`.
     * Please refer to [PHP manual](https://secure.php.net/manual/en/function.sort.php)
     * for more details. When sorting by multiple keys with different sort flags, use an array of sort flags.
     * @throws InvalidArgumentException if the $direction or $sortFlag parameters do not have
     * correct number of elements as that of $key.
     */
    public static function multisort(&$array, $key, $direction = SORT_ASC, $sortFlag = SORT_REGULAR)
    {
        $keys = is_array($key) ? $key : [$key];
        if (empty($keys) || empty($array)) {
            return;
        }
        $n = count($keys);
        if (is_scalar($direction)) {
            $direction = array_fill(0, $n, $direction);
        } elseif (count($direction) !== $n) {
            throw new InvalidArgumentException('The length of $direction parameter must be the same as that of $keys.');
        }
        if (is_scalar($sortFlag)) {
            $sortFlag = array_fill(0, $n, $sortFlag);
        } elseif (count($sortFlag) !== $n) {
            throw new InvalidArgumentException('The length of $sortFlag parameter must be the same as that of $keys.');
        }
        $args = [];
        foreach ($keys as $i => $k) {
            $flag = $sortFlag[$i];
            $args[] = static::getColumn($array, $k);
            $args[] = $direction[$i];
            $args[] = $flag;
        }

        // This fix is used for cases when main sorting specified by columns has equal values
        // Without it it will lead to Fatal Error: Nesting level too deep - recursive dependency?
        $args[] = range(1, count($array));
        $args[] = SORT_ASC;
        $args[] = SORT_NUMERIC;

        $args[] = &$array;
        call_user_func_array('array_multisort', $args);
    }

    
    /**
     * 对数组进行递归编码
     * @param array $data data to be encoded
     * @param bool $valuesOnly 是否只编码值
     * @param string $charset 编码格式
     * @return array the encoded data
     * @see https://secure.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function htmlEncode($data, $valuesOnly = true, $charset = null)
    {
        if ($charset === null) {
            $charset = 'UTF-8';
        }
        $d = [];
        foreach ($data as $key => $value) {
            if (!$valuesOnly && is_string($key)) {
                $key = htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
            }
            if (is_string($value)) {
                $d[$key] = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
            } elseif (is_array($value)) {
                $d[$key] = static::htmlEncode($value, $valuesOnly, $charset);
            } else {
                $d[$key] = $value;
            }
        }

        return $d;
    }

    
    /**
     * 对数组进行解码
     * @param array $data data to be decoded
     * @param bool $valuesOnly 是否只解码值
     * @return array the decoded data
     * @see https://secure.php.net/manual/en/function.htmlspecialchars-decode.php
     */
    public static function htmlDecode($data, $valuesOnly = true)
    {
        $d = [];
        foreach ($data as $key => $value) {
            if (!$valuesOnly && is_string($key)) {
                $key = htmlspecialchars_decode($key, ENT_QUOTES);
            }
            if (is_string($value)) {
                $d[$key] = htmlspecialchars_decode($value, ENT_QUOTES);
            } elseif (is_array($value)) {
                $d[$key] = static::htmlDecode($value);
            } else {
                $d[$key] = $value;
            }
        }

        return $d;
    }
    
    /**
     * 判断数组在数组中
     * @param mixed $needle 值
     * @param array|Traversable $haystack 数组或者可循环的对象
     * @param bool $strict 是否严格模式
     * @return bool `true` if `$needle` was found in `$haystack`, `false` otherwise.
     * @throws InvalidArgumentException if `$haystack` is neither traversable nor an array.
     * @see https://secure.php.net/manual/en/function.in-array.php
     */
    public static function isIn($needle, $haystack, $strict = false)
    {
        if ($haystack instanceof Traversable) {
            foreach ($haystack as $value) {
                if ($needle == $value && (!$strict || $needle === $value)) {
                    return true;
                }
            }
        } elseif (is_array($haystack)) {
            return in_array($needle, $haystack, $strict);
        } else {
            throw new InvalidArgumentException('Argument $haystack must be an array or implement Traversable');
        }

        return false;
    }
    
}
