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
    private function createConnection()
    {
        $this->conn = @mysqli_connect($this->host, $this->user, $this->password, $this->db);
        if (empty($this->conn)) {
            throw new \Exception('database connection error: access denied for user');
        }
        return $this->conn;
    }

    /**
     * 查询
     * @return array
     */
    public function query()
    {
        $data = [];
        $this->createConnection();
        $res = mysqli_query($this->conn, $this->sql);
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = $row;
            }
        }
        $this->closeConnection();
        return $data;
    }

    /**
     * 关闭数据库连接
     */
    private function closeConnection()
    {
        if (isset($this->conn)) {
            mysqli_close($this->conn);
        }
    }

    /**
     * 增删改
     * @return int
     */
    public function execute()
    {
        $this->createConnection();
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
}