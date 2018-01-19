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
    private $db;

    /**
     * 执行语句
     * @var string
     */
    protected $sql;

    /**
     * 执行类型
     * @var string
     */
    protected $type;

    /**
     * Connection constructor.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->db = DataBaseContainer::get($config['type'], $config);

        if (empty($this->db)) {
            throw new \Exception('database config error: type not exists');
        }
    }

    /**
     * 创建查询语句
     * @param string $sql
     * @return $this
     */
    public function setSql($sql = null)
    {
        if (!empty($sql)) {
            $this->sql = $sql;
        }
        return $this;
    }

    /**
     * 查询
     */
    public function query()
    {
        $this->type = 'query';
        $this->validateSQL();
        return $this->db->setSQL($this->sql)->query();
    }

    /**
     * 增删改
     */
    public function execute()
    {
        $this->type = 'execute';
        $this->validateSQL();
        return $this->db->setSQL($this->sql)->execute();
    }

    /**
     * 验证sql语句的合理性
     * @throws \Exception
     */
    private function validateSQL()
    {
        if ($this->type == 'query') {
            if (stristr($this->sql, 'update') || stristr($this->sql, 'delete')) {
                throw new \Exception('unavailable query()');
            }
        } elseif ($this->type == 'execute') {
            if (stristr($this->sql, 'select')) {
                throw new \Exception('unavailable execute()');
            }
        }
    }
}