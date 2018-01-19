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
    private function createConnection()
    {
        if (!isset($this->conn)) {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db . "", $this->user, $this->password);
        }
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
        $res = $this->createConnection()->query($this->sql);
        if ($res) {
            while ($row = $res->fetch()) {
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
            unset($this->conn);
        }
    }

    /**
     * 增删改
     * @return int
     */
    public function execute()
    {
        $res = $this->createConnection()->exec($this->sql);
        if ($res) {
            return 1; // 执行成功
        } else {
            return 0; // 执行失败
        }
    }
}