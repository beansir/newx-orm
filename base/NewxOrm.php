<?php
/**
 * @author bean
 * @version 2.0
 */
namespace newx\orm\base;

class NewxOrm
{
    /**
     * 运行ORM
     * @param array $config
     */
    public static function load($config = [])
    {
        // 加载数据库
        DataBase::load($config);
    }

    /**
     * 获取数据库连接
     * @param string $name 数据库配置名称
     * @return Connection|null
     */
    public static function getDb($name = 'default')
    {
        return Pool::getDb($name);
    }
}