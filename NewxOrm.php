<?php
/**
 * @author bean
 * @version 1.0
 */

define('NEWX_ORM_PATH', __DIR__); // 根目录

require_once NEWX_ORM_PATH . '/base/AutoLoader.php'; // 自动加载类

use newx\orm\base\DataBase;

class NewxOrm
{
    /**
     * 数据库
     * @var DataBase
     */
    public static $db;

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
        // 加载类关系映射
        self::$classMaps = require NEWX_ORM_PATH . '/config/class_map.php';
    }

    /**
     * 运行ORM
     * @param array $config
     */
    public static function run($config = [])
    {
        if (!$config) {
            $config = require NEWX_ORM_PATH . '/config/db.php';
        }
        self::$db = new DataBase($config);
    }

    /**
     * 获取数据库
     * @param string $key 数据库配置KEY
     * @return \newx\orm\base\Connection|null
     */
    public static function getDb($key = 'default')
    {
        if (empty(self::$db)) {
            return null;
        }
        if (!property_exists(self::$db, $key)) {
            return null;
        }
        return self::$db->{$key};
    }

}

NewxORM::init();