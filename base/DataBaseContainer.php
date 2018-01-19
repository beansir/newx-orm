<?php
/**
 * 数据库容器
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class DataBaseContainer
{
    /**
     * 容器数据
     * @var array
     */
    private static $_objects = [];

    /**
     * 数据注入
     * @param string $name
     * @param $object
     */
    public static function set($name, MysqlInterface $object)
    {
        self::$_objects[$name] = $object;
    }

    /**
     * 获取数据
     * @param string $name
     * @param array $config
     * @return MysqlInterface|null
     */
    public static function get($name, $config)
    {
        if (array_key_exists($name, self::$_objects)) {
            $object = self::$_objects[$name];
            self::configure($object, $config);
            return $object;
        } else {
            return null;
        }
    }

    /**
     * 数据库基础信息配置
     * @param $object
     * @param array $config
     */
    public static function configure(MysqlInterface $object, $config = [])
    {
        $object->configure($config);
    }
}