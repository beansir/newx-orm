<?php
/**
 * mysql接口
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

interface MysqlInterface
{
    /**
     * 查询
     * @return array
     */
    public function query();

    /**
     * 增删改
     * @return int|string|mixed
     */
    public function execute();

    /**
     * 配置
     * @param array $config
     */
    public function configure($config = []);

    /**
     * 设置执行语句
     * @param string $sql
     * @return $this
     */
    public function setSQL($sql = null);

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction();

    /**
     * 提交事务
     * @return bool
     */
    public function commit();

    /**
     * 事务回滚
     * @return bool
     */
    public function rollback();
}