# php-utils
php积累的一些公用类方法

# 环境支持

php版本 >= PHP 7.1

安装
------------

```
composer require phpyii/php-utils

//开发版本
composer require phpyii/php-utils:dev-master

```

# 枚举类使用方法

   ```php
    class  statusEnum extends \phpyii\utils\Enum{
         const VIEW = 'view';
         const EDIT = 'edit';

         protected static function labels(): array {
             return [
                 self::VIEW => '视图',
             ];
         }

    }

    
    
    //枚举值
    $viewAction = statusEnum::VIEW();
    $viewAction->getValue();
    //或者
    statusEnum::VIEW;

    //获取枚举label
    statusEnum::getLabelByValue(statusEnum::VIEW);

    //获取枚举 值=>label数组
    statusEnum::toArray();

   ```

