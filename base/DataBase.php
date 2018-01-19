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
     * CONSTRUCT
     * @param array $configs 数据库配置
     */
    public function __construct($configs = array())
    {
        $this->loadContainer();

        if (!empty($configs)) {
            foreach ($configs as $property => $config) {
                $this->{$property} = new Connection($config);
            }
        }
    }

    /**
     * 加载数据库容器
     */
    private function loadContainer()
    {
        DataBaseContainer::set('mysqli', new Mysqli());
        DataBaseContainer::set('pdo', new Pdo());
    }
}