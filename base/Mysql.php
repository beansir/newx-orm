<?php
/**
 * 数据库基类
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Mysql implements MysqlInterface
{
    /**
     * 数据库连接
     */
    protected $conn;

    /**
     * 主机地址
     * @var string
     */
    protected $host;

    /**
     * 数据库用户
     * @var string
     */
    protected $user;

    /**
     * 数据库密码
     * @var string
     */
    protected $password;

    /**
     * 数据库名称
     * @var string
     */
    protected $db;

    /**
     * 执行语句
     * @var string
     */
    protected $sql;

    /**
     * 设置sql语句
     * @param string $sql
     * @return $this
     */
    public function setSQL($sql = null)
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * 配置
     * @param array $config
     */
    public function configure($config = [])
    {
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->password = $config['password'];
        $this->db = $config['db'];
    }

    /**
     * 查询
     */
    public function query() {}

    /**
     * 增删改
     */
    public function execute() {}

    /**
     * 开启事务
     */
    public function beginTransaction() {}

    /**
     * 提交事务
     */
    public function commit() {}

    /**
     * 事务回滚
     */
    public function rollback() {}
}