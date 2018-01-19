<?php
/**
 * 自动加载类
 * @author: bean
 * @version 1.0
 */
namespace newx\orm\base;

class AutoLoader
{
    /**
     * 自动加载
     * @param string $class className
     */
    public static function autoload($class)
    {
        // 获取类关系映射
        $classMaps = \NewxORM::$classMaps;
        if (array_key_exists($class, $classMaps)) {
            // 加载类文件
            require_once $classMaps[$class];
        }
    }
}

spl_autoload_register("\\newx\\orm\\base\\AutoLoader::autoload");