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
     * 类型 - 查询
     * @var int
     */
    const TYPE_QUERY = 0;

    /**
     * 类型 - 增删改
     * @var int
     */
    const TYPE_EXECUTE = 1;

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
     * 查询
     * @param string $sql
     * @return array
     */
    public function query($sql)
    {
        self::validateSQL($sql, self::TYPE_QUERY);
        return $this->_db->setSQL($sql)->query();
    }

    /**
     * 增删改
     * @param string $sql
     * @return int
     */
    public function execute($sql)
    {
        self::validateSQL($sql, self::TYPE_EXECUTE);
        return $this->_db->setSQL($sql)->execute();
    }

    /**
     * 检验sql语句的合理性
     * @param string $sql
     * @param string $type SQL类型
     * @throws \Exception
     */
    public static function validateSQL($sql = null, $type)
    {
        if (!$sql) {
            throw new \Exception('sql not exists');
        }
        switch ($type) {
            case self::TYPE_QUERY:
                if (stristr($sql, 'update') || stristr($sql, 'delete')) {
                    throw new \Exception('please call the execute');
                }
                break;
            case self::TYPE_EXECUTE:
                if (stristr($sql, 'select')) {
                    throw new \Exception('please call the query');
                }
                break;
            default:
                break;
        }
    }

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->_db->beginTransaction();
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit()
    {
        return $this->_db->commit();
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function rollback()
    {
        return $this->_db->rollback();
    }
}