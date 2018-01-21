<?php
/**
 * 数据库类
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class DataBase extends BaseObject
{
    /**
     * 加载数据库连接
     * @param array $configs 数据库配置
     */
    public static function load($configs = array())
    {
        // 创建数据库连接实例
        if (!empty($configs)) {
            foreach ($configs as $key => $config) {
                $db = new Connection($config);
                Pool::setDb($key, $db);
            }
        }
    }
}