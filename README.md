<h2 align="center">NewX ORM</h2>

NewX ORM是一个简洁的数据库对象关系映射。

## 安装说明
使用composer一键安装
```
composer require beansir/newx-orm
```

#### 数据库配置文件
创建一个数据库配置文件，存放于你自己的项目中，格式如下
```php
<?php
return [
    'default' => [
        'host'      => '127.0.0.1', // 地址
        'user'      => 'user', // 用户名
        'password'  => 'password', // 密码
        'db'        => 'db', // 数据库名
        'type'      => 'mysqli' // 连接方式
    ],
];
```

#### 加载ORM
请务必在应用运行之前加载
```php
<?php
// 引入主体文件
require './vendor/beansir/newx-orm/NewxOrm.php';
 
// 上一步配置的数据库文件
$db = require 'xxx/db.php'; 
 
// 运行ORM
NewxOrm::run($db);
```

#### 继承Model
```php
<?php
class UserModel extends \newx\orm\base\Model
{
    public $table = 'user'; // 数据表名，默认default
    
    public $db = 'default'; // 数据库配置，默认default
}
```

## 使用指南

#### 创建数据模型
```php
<?php
// @return Model_User
$user = UserModel::model();
```

#### 查询所有记录
```php
<?php
// @return array 模型对象数组
// 方式一
$user = UserModel::model()->all();
 
// 方式二
$user = UserModel::getAll();
```

#### 查询单条记录
```php
<?php
// @return Model_User
// 方式一
$user = UserModel::model()->one();
 
// 方式二
$user = UserModel::getOne();
```

#### 定义数组结果集
```php
<?php
// @return array 结果集数组
// 方式一
$user = UserModel::model()->asArray()->one();
 
// 方式二
$user = UserModel::model()->one();
$user = $user->toArray();
```

#### 条件查询
```php
<?php
// 方式一
$user = UserModel::model()->where(['id' => 1])->one();
  
// 方式二
$user = UserModel::getOne(['id' => 1]);
 
// 方式三
$user = UserModel::getOne(1); // 主键查询
```

#### 表关联
```php
<?php
// @return 关联的模型对象或对象数组，取决于hasOne还是hasMany
// 方式一
$log = UserModel::getOne(1)->getLog();
 
// 方式二
$log = UserModel::getOne(1)->log;
 
// 模型中关联写法
class UserModel extends \newx\orm\base\Model
{
    // 关联Model_Log
    public function getLog()
    {
        return $this->hasOne(
            LogModel::className(), // 关联表类名
            'user_id', // 关联表字段名
            'id' // 本表字段名
        );
    }
}
```

#### 表连接
```php
<?php
// 左连接
$user = UserModel::model()
    ->leftJoin(
        'table name', // 连接表名
        'join field', // 连接表字段名
        'self field' // 本表字段名
    )
    ->all();
 
// 右连接
$user = UserModel::model()->rightJoin('table name', 'join field', 'self field')->all();
 
// 内连接
$user = UserModel::model()->innerJoin('table name', 'join field', 'self field')->all();
```

#### 执行SQL语句
```php
<?php
$db = NewxOrm::getDb();
 
// 查询 @return array
$db->query($sql);
 
// 增删改 @return int
$db->execute($sql);
```

#### 事务管理
```php
<?php
$db = NewxOrm::getDb();
 
// 开启事务 @return bool
$db->beginTransaction();
 
// 提交事务 @return bool
$db->commit();
 
// 事务回滚 @return bool
$db->rollback();
```