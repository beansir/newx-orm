<?php
/**
 * @author bean
 * @version 1.0
 */

define('NEWX_ORM_PATH', __DIR__); // 根目录

require_once NEWX_ORM_PATH . '/base/AutoLoader.php'; // 自动加载类

use newx\orm\base\Pool;
use newx\orm\base\DataBase;
use newx\orm\base\Connection;

class NewxOrm
{
    /**
     * 类关系映射
     * @var array
     */
    public static $classMaps;

    /**
     * 初始化
     */
    public static function init()
    {
        // 加载配置文件
        require NEWX_ORM_PATH . '/config/config.php';

        // 加载类关系映射
        self::$classMaps = require NEWX_ORM_PATH . '/config/class_map.php';
    }

    /**
     * 运行ORM
     * @param array $config
     */
    public static function run($config = [])
    {
        // 数据库配置文件
        if (!$config) {
            $config = require NEWX_ORM_PATH . '/config/db.php';
        }

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

NewxORM::init();