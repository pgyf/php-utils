<?php

declare (strict_types = 1);

namespace phpyii\utils;

/**
 * 枚举父类
 * from https://github.com/myclabs/php-enum
 * 用法
 *    class  statusEnum extends \phpyii\utils\Enum{
 *         const VIEW = 'view';
 *         const EDIT = 'edit';
 *
 *         protected static function labels(): array {
 *             return [
 *                 self::VIEW => '视图',
 *             ];
 *         }
 *
 *   }
 * $action = statusEnum::VIEW();
 * @author 最初的梦想
 */
abstract class Enum implements \JsonSerializable
{
    /**
     * Enum value
     *
     * @var mixed
     * @psalm-var T
     */
    protected $value;
    
    protected $label;

    /**
     * Store existing constants in a static cache per object.
     *
     *
     * @var array
     * @psalm-var array<class-string, array<string, mixed>>
     */
    protected static $cache = [];
    
    /**
     * Store existing constants in a static cache per object.
     *
     *
     * @var array
     * @psalm-var array<class-string, array<string, mixed>>
     */
    protected static $cacheLabel = [];

    /**
     * Creates a new value of some type
     *
     * @psalm-pure
     * @param mixed $value
     *
     * @psalm-param static<T>|T $value
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct($value, $label = '')
    {
        if ($value instanceof static) {
           /** @psalm-var T */
            $value = $value->getValue();
        }

        if (!$this->isValid($value)) {
            /** @psalm-suppress InvalidCast */
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . static::class);
        }

        /** @psalm-var T */
        $this->value = $value;
        $this->label = $label;
    }

    /**
     * @psalm-pure
     * @return mixed
     * @psalm-return T
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @psalm-pure
     * @return mixed
     * @psalm-return T
     */
    public function getLabel()
    {
        if(empty($this->label)){
            $array = static::toArray();
            if(isset($array[$this->value])){
                $this->label = $array[$this->value];
            }
        }
        return $this->label;
    }
    
    /**
     * @psalm-pure
     * @return mixed
     * @psalm-return T
     */
    public static function getLabelByValue($value, $default = '未知')
    {
       $array = static::toArray();
        if(isset($array[$value])){
            return $array[$value];
        }
        return $default;
    }
    

    /**
     * Returns the enum key (i.e. the constant name).
     * 枚举
     * @psalm-pure
     * @return mixed
     */
    public function getKey()
    {
        return static::search($this->value);
    }

    /**
     * @psalm-pure
     * @psalm-suppress InvalidCast
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Determines if Enum should be considered equal with the variable passed as a parameter.
     * Returns false if an argument is an object of different class or not an object.
     *
     * This method is final, for more information read https://github.com/myclabs/php-enum/issues/4
     *
     * @psalm-pure
     * @psalm-param mixed $variable
     * @return bool
     */
    final public function equals($variable = null): bool
    {
        return $variable instanceof self
            && $this->getValue() === $variable->getValue()
            && static::class === \get_class($variable);
    }

    /**
     * Returns the names (keys) of all constants in the Enum class
     * 枚举
     * @psalm-pure
     * @psalm-return list<string>
     * @return array
     */
    public static function keys()
    {
        return \array_keys(static::toEnumArray());
    }

    /**
     * Returns instances of the Enum class of all Enum constants
     * 枚举值
     * @psalm-pure
     * @psalm-return array<string, static>
     * @return static[] Constant name in key, Enum instance in value
     */
    public static function values()
    {
        return \array_keys(static::toArray());
    }

    
    /**
     * Returns all possible values as an array
     *
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     *
     * @psalm-return array<string, mixed>
     * @return array Constant name in key, constant value in value
     */
    public static function toEnumArray()
    {
        $class = static::class;
        if (!isset(static::$cache[$class])) {
            $reflection            = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }
        return static::$cache[$class];
        
    }
    
    
    
    /**
     * Returns all possible values as an array
     *
     * @psalm-pure
     * @psalm-suppress ImpureStaticProperty
     *
     * @psalm-return array<string, mixed>
     * @return array Constant name in key, constant value in value
     */
    public static function toArray()
    {
        $class = static::class;
        if (isset(static::$cacheLabel[$class])) {
            return static::$cacheLabel[$class];
        }
        $enumArray = self::toEnumArray();
        $labelMap = static::labels();
        $array = [];
        foreach ($enumArray as $key => $value) {
            $label = $labelMap[$value] = $labelMap[$value] ?? $value;
            $array[$value] = $label;
        }
        static::$cacheLabel[$class] = $array;
        return $array;
    }

    /**
     * Check if is valid enum value
     *
     * @param $value
     * @psalm-param mixed $value
     * @psalm-pure
     * @return bool
     */
    public static function isValid($value)
    {
        return \in_array($value, static::toEnumArray(), true);
    }

    /**
     * Check if is valid enum key
     *
     * @param $key
     * @psalm-param string $key
     * @psalm-pure
     * @return bool
     */
    public static function isValidKey($key)
    {
        $array = static::toEnumArray();

        return isset($array[$key]) || \array_key_exists($key, $array);
    }

    /**
     * Return key for value
     *
     * @param $value
     *
     * @psalm-param mixed $value
     * @psalm-pure
     * @return mixed
     */
    public static function search($value)
    {
        return \array_search($value, static::toEnumArray(), true);
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return static
     * @psalm-pure
     * @throws \BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toEnumArray();
        if (isset($array[$name]) || \array_key_exists($name, $array)) {
            return new static($array[$name]);
        }

        throw new \BadMethodCallException("No static method or enum constant '$name' in class " . static::class);
    }

    /**
     * Specify data which should be serialized to JSON. This method returns data that can be serialized by json_encode()
     * natively.
     *
     * @return mixed
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @psalm-pure
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }
    
    /**
     * labels
     * @return array
     */
    protected static function labels(): array
    {
        return [];
    }
}
