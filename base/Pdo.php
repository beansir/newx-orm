<?php
/**
 * PDO
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Pdo extends Mysql
{
    /**
     * 创建数据库连接
     */
    private function connection()
    {
        $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->password);
        return $this->conn;
    }

    /**
     * 查询
     * @return array
     */
    public function query()
    {
        $data = [];
        $res = $this->connection()->query($this->sql);
        if ($res) {
            while ($row = $res->fetch()) {
                $data[] = $row;
            }
        }

        // 关闭数据库连接
        unset($this->conn);

        return $data;
    }

    /**
     * 增删改
     * @return int
     */
    public function execute()
    {
        if (!$this->conn) {
            $this->connection();
        }
        $res = $this->conn->exec($this->sql);
        if ($res) {
            return 1; // 执行成功
        } else {
            return 0; // 执行失败
        }
    }

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction()
    {
        // 创建数据库连接
        $this->connection();

        return $this->conn->beginTransaction();
    }

    /**
     * 提交事务
     * @return bool
     */
    public function commit()
    {
        return $this->conn->commit();
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function rollback()
    {
        return $this->conn->rollback();
    }
}