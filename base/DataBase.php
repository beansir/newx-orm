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
    public static function load($configs = [])
    {
        if (!$configs) {
            // 获取数据库默认配置
            $configs = self::getDefaultDbConfig();
        }

        // 创建数据库连接实例
        foreach ($configs as $key => $config) {
            $db = new Connection($config);
            Pool::setDb($key, $db);
        }
    }

    /**
     * 获取数据库默认配置
     * @return array
     */
    private static function getDefaultDbConfig()
    {
        return [
            'default' => [
                'host' => '127.0.0.1',
                'user' => 'user',
                'password' => 'password',
                'db' => 'db',
                'type' => 'mysqli'
            ],
        ];
    }
}