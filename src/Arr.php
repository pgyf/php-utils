<?php

declare (strict_types = 1);

namespace phpyii\utils;

/**
 * 数组类
 * @author lyf
 */
class Arr {
    
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
     * 把数组2中的关联数据合并到数组1中
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
    public static function rand($array, $len = 1)
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
     * Convert the array into a query string.
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
    
}
