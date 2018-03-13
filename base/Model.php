<?php
/**
 * NewX Model
 * @author: bean
 * @version: 1.0
 */
namespace newx\orm\base;

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
    public $db = 'default';

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
     * 执行类型 - 新增
     * @var string
     */
    const EXEC_INSERT = 'insert';

    /**
     * 执行类型 - 更新
     * @var string
     */
    const EXEC_UPDATE = 'update';

    /**
     * BaseModel constructor.
     * @param Query $query
     */
    public function __construct($query = null)
    {
        if (empty($query)) {
            $this->_execute = self::EXEC_INSERT;
        } else {
            $this->_query = $query;
            $this->_execute = self::EXEC_UPDATE;
        }
    }

    /**
     * 创建数据模型
     * @return $this
     */
    public static function model()
    {
        return self::createModel(self::className());
    }

    /**
     * 查询单条记录
     * @param array|mixed $where
     * @throws \Exception
     * @return Model|null
     */
    public static function getOne($where = null)
    {
        $object = self::model();
        if (!is_array($where)) {
            // 主键查询
            $keys = $object->getPrimaryKey();
            if (!$keys) {
                throw new \Exception('Primary key not exists');
            }
            $where = $keys[0] . "='" . $where . "'";
        }
        return $object->setWhere($where)->one();
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
     * 创建模型实例
     * @return Model|mixed
     */
    private static function createModel($className)
    {
        // 获取类模型实例
        $object = Pool::getModel($className);

        if (empty($object)) {
            // 创建类实例
            $object = new $className(new Query());
            $object->_query->className = $className;
            $object->_query->tableName = $object->table;

            // 设置类模型实例
            Pool::setModel($className, $object);
        }

        return $object;
    }

    /**
     * 关联读取单条记录
     * @param string $className 关联的类名
     * @param string $relationField 关联表的字段名
     * @param string $selfField 本表的字段名
     * @return Model|mixed
     */
    public function hasOne($className, $relationField, $selfField)
    {
        return $this->createRelationModel($className, $relationField, $selfField)->one();
    }

    /**
     * 关联读取多条记录
     * @param string $className 关联的类名
     * @param string $relationField 关联表的字段名
     * @param string $selfField 本表的字段名
     * @return array
     */
    public function hasMany($className, $relationField, $selfField)
    {
        return $this->createRelationModel($className, $relationField, $selfField)->all();
    }

    /**
     * 创建关联模型实例
     * @param string $className 关联的类名
     * @param string $relationField 关联表的字段名
     * @param string $selfField 本表的字段名
     * @return $this
     */
    private function createRelationModel($className, $relationField, $selfField)
    {
        // 创建类实例
        $object = self::createModel($className);

        $where = [
            $relationField => $this->{$selfField}
        ];

        return $object->where($where);
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
        return $this->getDb()->query($sql);
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
        $join = ' ' . $query->join;
        $where = $query->where ? " WHERE " . $query->where : '';
        $order = $query->orderBy ? " ORDER BY " . $query->orderBy : '';
        $limit = $query->limit ? " LIMIT " . $query->limit : '';
        $sql = "SELECT " . $field . " FROM " . $table . $join . $where . $order . $limit;
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
     * @param array|string|int $where
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
            $this->_query->where = $where;
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
        return $this->getDb()->execute($sql);
    }

    /**
     * 获取操作执行语句
     * @return string
     */
    private function getExecSql()
    {
        // 验证操作的字段是否存在
        $this->validateAttribute();

        // 获取操作的字段
        $attributes = $this->_attributes;
        $tableName = $this->table;

        switch ($this->_execute) {
            case self::EXEC_INSERT: // 新增
                $key = implode(',', array_keys($attributes));
                $value = implode("','", array_values($attributes));
                $sql = "INSERT INTO `{$tableName}` ({$key}) VALUES ('{$value}')";
                break;
            case self::EXEC_UPDATE: // 更新
                $primaryKey = $this->getPrimaryKey(); // 主键
                $sets = [];
                foreach ($attributes as $key => $value) {
                    // 非主键的字段可更新值
                    if (!in_array($key, $primaryKey)) {
                        $sets[] = "{$key}='{$value}'";
                    }
                }
                $set = implode(',', $sets);
                $sql = "UPDATE `{$tableName}` SET {$set} WHERE {$this->_query->where}";
                break;
            default:
                $sql = null;
                break;
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
            $this->_tableDescribes = $db->query($sql);
        }
        return $this->_tableDescribes;
    }

    /**
     * 获取数据库连接实例
     * @return Connection|mixed
     */
    private function getDb()
    {
        return Pool::getDb($this->db);
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

    /**
     * 对象结果集转为数组
     * @return array
     */
    public function toArray()
    {
        $data = [];
        $attributes = $this->_attributes;
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * 关联预加载
     */
    public function relation()
    {

    }

    /**
     * 左连接
     * @param string $tableName 连接表名
     * @param string $joinFiled 连接表字段名
     * @param string $selfField 本表字段名
     * @return $this
     */
    public function leftJoin($tableName, $joinFiled, $selfField)
    {
        return $this->join($tableName, $joinFiled, $selfField, 'left join');
    }

    /**
     * 右连接
     * @param string $tableName 连接表名
     * @param string $joinFiled 连接表字段名
     * @param string $selfField 本表字段名
     * @return $this
     */
    public function rightJoin($tableName, $joinFiled, $selfField)
    {
        return $this->join($tableName, $joinFiled, $selfField, 'right join');
    }

    /**
     * 内连接
     * @param string $tableName 连接表名
     * @param string $joinFiled 连接表字段名
     * @param string $selfField 本表字段名
     * @return $this
     */
    public function innerJoin($tableName, $joinFiled, $selfField)
    {
        return $this->join($tableName, $joinFiled, $selfField, 'inner join');
    }

    /**
     * 表连接
     * @param string $tableName 连接表名
     * @param string $joinFiled 连接表字段名
     * @param string $selfField 本表字段名
     * @param string $type 连接类型
     * @return $this
     */
    private function join($tableName, $joinFiled, $selfField, $type)
    {
        $this->_query->join = "{$type} `{$tableName}` on `{$this->table}`.{$selfField} = `{$tableName}`.{$joinFiled}";
        return $this;
    }
}