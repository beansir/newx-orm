<h2 align="center">NewX ORM</h2>

NewX ORM是一个简洁的数据库对象关系映射。（NewX ORM is a concise database object relational mapping.）

创建一个数据库配置文件，存放于你自己的项目中，格式如下
```php
<?php
return [
    'default' => [
        'host' => '127.0.0.1', // 地址
        'user' => 'user', // 用户名
        'password' => 'password', // 密码
        'db' => 'db', // 数据库名
        'type' => 'mysqli' // 连接方式
    ],
];
```

加载NewX ORM（请务必在应用运行之前加载）
```php
<?php
require 'newx-orm/NewxOrm.php';
$db = require 'xxx/db.php'; // 上一步配置的数据库文件
NewxOrm::run($db);
```

继承NewX ORM Model
```php
<?php
class Model_User extends \newx\orm\base\Model
{
    public $table = 'user'; // 数据表名，默认default
    
    public $db = 'default'; // 数据库配置，默认default
}
```

## 使用指南

创建数据模型
```php
<?php
// @return Model_User
$user = Model_User::model();
```

查询所有记录
```php
<?php
// @return array 模型对象数组
// 方式一
$user = Model_User::model()->all();
 
// 方式二
$user = Model_User::getAll();
```

查询单条记录
```php
<?php
// @return Model_User
// 方式一
$user = Model_User::model()->one();
 
// 方式二
$user = Model_User::getOne();
```

直接返回数组结果集
```php
<?php
// @return array 结果集数组
// 方式一
$user = Model_User::model()->asArray()->one();
 
// 方式二
$user = Model_User::model()->one();
$user = $user->toArray();
```

条件查询
```php
<?php
// 方式一
$user = Model_User::model()->where(['id' => 1])->one();
  
// 方式二
 
$user = Model_User::getOne(['id' => 1]);
 
// 方式三
$user = Model_User::getOne(1); // 主键查询
```

表关联
```php
<?php
// @return 关联的模型对象或对象数组，取决于hasOne还是hasMany
// 方式一
$log = Model_User::getOne(1)->getLog();
 
// 方式二
$log = Model_User::getOne(1)->log;
 
// 模型中关联写法
class Model_User extends \newx\orm\base\Model
{
    // 关联Model_Log
    public function getLog()
    {
        return $this->hasOne(
            Model_Log::className(), // 关联表类名
            'user_id', // 关联表字段名
            'id' // 本表字段名
        );
    }
}
```

表连接
```php
<?php
// 左连接
$user = Model_User::model()
    ->leftJoin(
        'table name', // 连接表名
        'join field', // 连接表字段名
        'self field' // 本表字段名
    )
    ->all();
 
// 右连接
$user = Model_User::model()->rightJoin('table name', 'join field', 'self field')->all();
 
// 内连接
$user = Model_User::model()->innerJoin('table name', 'join field', 'self field')->all();
```