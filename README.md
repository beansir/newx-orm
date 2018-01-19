<h2 align="center">NewX ORM</h2>

NewX ORM是一个简洁的数据库对象关系映射。（NewX ORM is a concise database object relational mapping.）

创建一个数据库配置文件，存放于你自己的项目中，格式如下
```php
<?php
return [
    'default' => [
        'host' => '127.0.0.1', // 地址 host
        'user' => 'user', // 用户名 user
        'password' => 'password', // 密码 password
        'db' => 'db', // 数据库名 database name
        'type' => 'mysqli' // 连接方式 connect type
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
    
    public $database = 'default'; // 数据库配置，默认default
}
```

####使用指南

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
$user = Model_User::model()->all();
```

查询单条记录
```php
<?php
// @return Model_User
$user = Model_User::model()->one();
```

直接返回数组结果集
```php
<?php
// @return array 结果集数组
$user = Model_User::model()->asArray()->one();
```

条件查询
```php
<?php
// 三种方式结果相同
$user = Model_User::model()->where(['id' => 1])->one();
$user = Model_User::getOne(['id' => 1]);
$user = Model_User::getOne(1); // 主键查询
```

表关联
```php
<?php
// 模型中关联用法
class Model_User extends \newx\orm\base\Model
{
    // 关联Model_Log
    public function getLog()
    {
        return $this->hasOne(
            Model_Log::className(), // 关联表的类名
            'user_id', // 关联表的字段名
            'id' // 本表的字段名
        );
    }
}
// 获取关联结果
// @return 关联的模型对象或对象数组，取决于hasOne还是hasMany
$log = Model_User::getOne(1)->getLog(); // 方法一
$log = Model_User::getOne(1)->log; // 方法二
```