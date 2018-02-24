<?php
/**
 * Mysqli
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

class Mysqli extends Mysql
{
    /**
     * 创建数据库连接
     */
    private function connection()
    {
        $this->conn = new \mysqli($this->host, $this->user, $this->password, $this->db);
        return $this->conn;
    }

    /**
     * 查询
     * @return array
     */
    public function query()
    {
        $data = [];

        // 创建数据库连接
        $this->connection();

        $res = mysqli_query($this->conn, $this->sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = $row;
            }
        }

        // 关闭数据库连接
        mysqli_close($this->conn);

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
        $res = mysqli_query($this->conn, $this->sql);
        if ($res) {
            if (mysqli_affected_rows($this->conn) > 0) {
                return 1; // 执行成功
            } else {
                return 2; // 无行数影响
            }
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

        return $this->conn->begin_transaction();
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