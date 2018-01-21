<?php
/**
 * 底层基类
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class BaseObject
{
    /**
     * 魔术函数 __get
     * @param string $name
     * @return object|null
     */
    public function __get($name)
    {
        $action = 'get' . ucfirst($name);

        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return null;
        }
    }

    /**
     * 实例类全名
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
}