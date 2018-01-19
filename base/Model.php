<?php
/**
 * NewX Model
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

use NewxOrm;

class Model extends BaseObject
{
    /**
     * 数据表
     * @var string
     */
    public $table = 'default';

    /**
     * 数据库
     * @var string
     */
    public $database = 'default';

    /**
     * 操作属性
     * @var array
     */
    private $_attributes = [];

    /**
     * 查询命令
     * @var Query
     */
    private $_query;

    /**
     * 操作类型
     * @var string
     */
    private $_execute;

    /**
     * 数据表信息
     * @var array
     */
    private $_tableDescribes;

    /**
     * 数据表主键
     * @var array
     */
    private $_primaryKeys = [];

    /**
     * 类实例
     * @var array
     */
    private static $classObjects = [];

    /**
     * BaseModel constructor.
     * @param Query $query
     */
    public function __construct($query = null)
    {
        if (empty($query)) {
            $this->_execute = 'insert';
        } else {
            $this->_query = $query;
            $this->_execute = 'update';
        }
    }

    /**
     * 创建数据模型
     * @return $this
     */
    public static function model()
    {
        return self::createObject();
    }

    /**
     * 查询单条记录
     * @param array|mixed $where
     * @return $this|array|null
     */
    public static function getOne($where = null)
    {
        $object = self::model();
        if ($where) {
            $object->setWhere($where);
        }
        return $object->one();
    }

    /**
     * 查询多条记录
     * @param array|mixed $where
     * @return array
     */
    public static function getAll($where = null)
    {
        $object = self::model();
        if ($where) {
            $object->setWhere($where);
        }
        return $object->all();
    }

    /**
     * 创建当前model实例
     * @return $this
     */
    private static function createObject()
    {
        // 获取类实例
        $className = self::className();
        $object = self::getClassObject($className);

        if (empty($object)) {
            $object = new $className(new Query());
            self::setClassObject($className, $object); // 存储类实例
        }

        $object->_query->className = $className;
        $object->_query->tableName = $object->table;

        return $object;
    }

    /**
     * 获取类实例
     * @param string $className
     * @return mixed
     */
    private static function getClassObject($className)
    {
        if (array_key_exists($className, self::$classObjects)) {
            return self::$classObjects[$className];
        } else {
            return null;
        }
    }

    /**
     * 配置类实例
     * @param string $className
     * @param object $object
     */
    private static function setClassObject($className, $object)
    {
        self::$classObjects[$className] = $object;
    }

    /**
     * 关联读取单条记录
     * @param string $className 关联的类名
     * @param string $relation_field 关联表的字段名
     * @param string $self_field 本表的字段名
     * @return object
     */
    public function hasOne($className, $relation_field, $self_field)
    {
        return $this->createWithObject($className, $relation_field, $self_field)->one();
    }

    /**
     * 关联读取多条记录
     * @param string $className 关联的类名
     * @param string $relation_field 关联表的字段名
     * @param string $self_field 本表的字段名
     * @return array
     */
    public function hasMany($className, $relation_field, $self_field)
    {
        return $this->createWithObject($className, $relation_field, $self_field)->all();
    }

    /**
     * 创建关联的实例
     * @param string $className 关联的类名
     * @param string $relation_field 关联表的字段名
     * @param string $self_field 本表的字段名
     * @return $this
     */
    private function createWithObject($className, $relation_field, $self_field)
    {
        /**
         * @var Model $object
         */
        $object = new $className();
        $tableName = $object->table;

        $this->_query = new Query();
        $this->_query->className = $className;
        $this->_query->tableName = $tableName;

        $where = [
            $relation_field => $this->{$self_field}
        ];

        return $this->where($where);
    }

    /**
     * 查询单条记录
     * @return $this|array|null
     */
    public function one()
    {
        $this->_query->limit = 1;
        $data = $this->all();
        return $data ? $data[0] : null;
    }

    /**
     * 查询多条记录
     * @return array
     */
    public function all()
    {
        $data = $this->buildQuery();
        return $this->process($data);
    }

    /**
     * 返回查询的数量
     * @return int
     */
    public function count()
    {
        $data = $this->buildQuery();
        return count($data);
    }

    /**
     * 创建查询
     */
    private function buildQuery()
    {
        $sql = $this->getQuerySql();
        return $this->getDb()->setSql($sql)->query();
    }

    /**
     * 获取执行语句
     */
    private function getQuerySql()
    {
        $query = $this->_query;
        $field = $query->field ? $query->field : '*';
        $table = $query->tableName ? $query->tableName : $this->table;
        $table = "`" . $table . "`";
        $where = $query->where ? " WHERE " . $query->where : '';
        $order = $query->orderBy ? " ORDER BY " . $query->orderBy : '';
        $limit = $query->limit ? " LIMIT " . $query->limit : '';
        $sql = "SELECT " . $field . " FROM " . $table . $where . $order . $limit;
        return $sql;
    }

    /**
     * 设置查询字段
     * @param $field
     * @return $this
     */
    public function field($field = null)
    {
        if ($field) {
            if (is_array($field)) {
                $this->_query->field = implode(',', $field);
            } else {
                $this->_query->field = $field;
            }
        }
        return $this;
    }

    /**
     * 设置查询条件
     * @param array $where
     * @return $this
     */
    public function where($where = null)
    {
        if ($where) {
            $this->setWhere($where);
        }
        return $this;
    }

    /**
     * 设置排序规则
     * @param string $orderBy
     * @return $this
     */
    public function orderBy($orderBy = null)
    {
        if ($orderBy) {
            $this->_query->orderBy = $orderBy;
        }
        return $this;
    }

    /**
     * 设置查询数量
     * @param int $limit
     * @return $this
     */
    public function limit($limit = null)
    {
        if ($limit) {
            $this->_query->limit = $limit;
        }
        return $this;
    }

    /**
     * 获取数据表主键
     * @return array
     */
    private function getPrimaryKey()
    {
        if (empty($this->_primaryKeys)) {
            $tableDescribes = $this->getTableDescribe();
            foreach ($tableDescribes as $describe) {
                if ($describe['Key'] == 'PRI') {
                    $this->_primaryKeys[] = $describe['Field'];
                }
            }
        }
        return $this->_primaryKeys;
    }

    /**
     * 配置查询条件
     * @param array $where
     * @throws \Exception
     * @return $this
     */
    private function setWhere($where)
    {
        if (is_array($where)) {
            $condition = [];
            foreach ($where as $key => $value) {
                $condition[] = $key . "='" . self::sqlSafe($value) . "'";
            }
            $this->_query->where = implode(' and ', $condition);
        } else {
            $keys = $this->getPrimaryKey();
            if (!$keys) {
                throw new \Exception('Primary key not exists');
            }
            $this->_query->where = $keys[0] . "='" . $where . "'";
        }
        return $this;
    }

    /**
     * 设置数据返回格式为数组
     */
    public function asArray()
    {
        $this->_query->asArray = true;

        return $this;
    }

    /**
     * 数据加工
     * @param array $data
     * @return array
     */
    private function process($data = [])
    {
        $newData = [];
        $query = $this->_query;

        if ($query->asArray) { // 返回数组
            $newData = $data;
        } else { // 返回对象
            foreach ($data as $value) {
                $object = self::instantiate($query); // 创建对象实例
                foreach ($value as $key => $value2) {
                    // 将每组字段数据加入新建的对象中
                    $object->{$key} = $value2;
                }
                $newData[] = $object;
            }
        }
        return $newData;
    }

    /**
     * 移除属性
     * @param string $property
     */
    private function unsetProperty($property)
    {
        unset($this->{$property});
    }

    /**
     * 用于数据加工使用的对象实例
     * 不注册该对象实例
     * @param object $query
     * @return $this
     */
    private static function instantiate($query = null)
    {
        $className = self::className();

        if ($query->className) {
            $className = $query->className;
        }

        return new $className($query);
    }

    /**
     * 新增、修改
     */
    public function save()
    {
        $sql = $this->getExecSql();
        return $this->getDb()->setSql($sql)->execute();
    }

    /**
     * 获取操作执行语句
     * @return string
     */
    private function getExecSql()
    {
        $execute = $this->_execute;
        $tableName = $this->table;

        // 验证操作的字段是否存在
        $this->validateAttribute();

        // 获取操作的字段
        $attributes = $this->_attributes;

        if ($execute == 'insert') {
            $key = implode(',', array_keys($attributes));
            $value = implode(',', array_values($attributes));
            $sql = "INSERT INTO `" . $tableName . "` (" . $key . ") VALUES (" . $value . ")";
        } else {
            $set = '';
            foreach ($attributes as $key => $value) {
                if (in_array($key, $this->getPrimaryKey())) {
                    continue;
                }
                if (!empty($set)) {
                    $set .= ",";
                }
                $set .= $key . "='" . $value . "'";
            }
            $sql = "UPDATE `" . $tableName . "` SET " . $set . " WHERE " . $this->_query->where;
        }
        return $sql;
    }

    /**
     * 验证数据库字段是否存在
     */
    private function validateAttribute()
    {
        $tableFields = [];
        $tableDescribe = $this->getTableDescribe();
        if ($tableDescribe) {
            foreach ($tableDescribe as $array) {
                if (array_key_exists('Field', $array)) {
                    $tableFields[] = $array['Field'];
                }
            }
        }

        $attributes = $this->_attributes;
        foreach ($attributes as $key => $value) {
            if (!in_array($key, $tableFields)) {
                throw new \Exception('unknown table field: ' . $key);
            }
        }
    }

    /**
     * 获取数据表字段信息
     * @return array
     */
    private function getTableDescribe()
    {
        if (empty($this->_tableDescribes)) {
            $table = $this->table;
            $sql = "DESCRIBE " . $table;
            $db = $this->getDb();
            $this->_tableDescribes = $db->setSql($sql)->query();
        }
        return $this->_tableDescribes;
    }

    /**
     * 获取数据库连接配置
     * @return Connection|mixed
     */
    private function getDb()
    {
        $db = NewxOrm::$db;
        $database = $this->database;

        if (!property_exists($db, $database)) {
            throw new \Exception("database config not exists: '{$database}'");
        }

        return $db->{$database};
    }

    /**
     * GET魔术函数
     * @param string $name
     * @return string|null|mixed
     */
    public function __get($name)
    {
        $res = parent::__get($name);
        if (empty($res)) {
            $res = $this->getAttribute($name);
        }
        return $res;
    }

    /**
     * SET魔术函数
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * 配置数据库属性
     * @param string $name
     * @param string $value
     * @return $this
     */
    private function setAttribute($name, $value)
    {
        $this->_attributes[$name] = $value;
        return $this;
    }

    /**
     * 获取数据库属性
     * @param string $name
     * @return string|null
     */
    private function getAttribute($name)
    {
        if (array_key_exists($name, $this->_attributes)) {
            return $this->_attributes[$name];
        } else {
            return null;
        }
    }

    /**
     * 防SQL注入
     * @param string $sql
     * @return string|mixed
     */
    private static function sqlSafe($sql = null)
    {
        if (is_string($sql)) {
            return str_replace(['"', "'", ';', '_', '%'], ['\"', "\'", '\;', '\_', '\%'], $sql);
        } else {
            return $sql;
        }
    }
}