<?php
/**
 * 数据库连接类
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Connection extends BaseObject
{
    /**
     * 数据库
     * @var MysqlInterface
     */
    private $_db;

    /**
     * 执行语句
     * @var string
     */
    private $_sql;

    /**
     * Connection constructor.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        // 数据库连接类型，默认mysqli
        if (isset($config['type']) && !empty($config['type'])) {
            $type = $config['type'];
        } else {
            $type = DB_LINK_MYSQLI;
        }

        // 创建数据库配置实例
        switch ($type) {
            case DB_LINK_MYSQLI:
                $this->_db = new Mysqli();
                break;
            case DB_LINK_PDO:
                $this->_db = new Pdo();
                break;
            default:
                throw new \Exception('database config error: type not found');
                break;
        }

        // 数据配置
        $this->_db->configure($config);
    }

    /**
     * 创建查询语句
     * @param string $sql
     * @return $this
     */
    public function setSql($sql = null)
    {
        if (!empty($sql)) {
            $this->_sql = $sql;
        }
        return $this;
    }

    /**
     * 查询
     */
    public function query()
    {
        $this->validateSQL('query');
        return $this->_db->setSQL($this->_sql)->query();
    }

    /**
     * 增删改
     */
    public function execute()
    {
        $this->validateSQL('execute');
        return $this->_db->setSQL($this->_sql)->execute();
    }

    /**
     * 验证sql语句的合理性
     * @param string $type SQL类型
     * @throws \Exception
     */
    private function validateSQL($type)
    {
        if ($type == 'query') {
            if (stristr($this->_sql, 'update') || stristr($this->_sql, 'delete')) {
                throw new \Exception('unavailable query()');
            }
        } elseif ($type == 'execute') {
            if (stristr($this->_sql, 'select')) {
                throw new \Exception('unavailable execute()');
            }
        }
    }
}