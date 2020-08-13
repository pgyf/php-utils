<?php

declare (strict_types = 1);

namespace phpyii\utils;

/**
 * Description of ArrayHelper
 *
 * @author lyf
 */
class ArrayHelper {
    
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
     * @return type
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
}
