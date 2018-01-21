<?php
/**
 * 数据池
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Pool
{
    /**
     * 数据库配置池
     * @var array
     */
    private static $_dbs = [];

    /**
     * 模型池
     * @var array
     */
    private static $_models = [];

    /**
     * GET
     * @param array $target
     * @param string $key
     * @return mixed
     */
    private static function get($target, $key)
    {
        if (array_key_exists($key, $target)) {
            return unserialize(serialize($target[$key]));
        } else {
            return null;
        }
    }

    /**
     * 数据库配置出池
     * @param string $name
     * @return Connection|null
     */
    public static function getDb($name)
    {
        return self::get(self::$_dbs, $name);
    }

    /**
     * 数据库配置入池
     * @param string $name
     * @param Connection $object
     */
    public static function setDb($name, $object)
    {
        self::$_dbs[$name] = unserialize(serialize($object));
    }

    /**
     * 模型出池
     * @param $name
     * @return Model|null
     */
    public static function getModel($name)
    {
        return self::get(self::$_models, $name);
    }

    /**
     * 模型入池
     * @param string $name
     * @param Model $object
     */
    public static function setModel($name, $object)
    {
        self::$_models[$name] = unserialize(serialize($object));
    }
}