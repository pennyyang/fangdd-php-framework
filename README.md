fangdd-php-framework
====================

为 **迪哥杯** 积分赛所做的小框架。

**特点**

1. MVC（简单的模型、控制器、和渲染引擎（可以布局））
1. 实现路由（ Router , 杨萍）和数据库（ ORM , 王霄池），以及表单验证（ Validator , 谢康旺）
1. 一切的表现形式都学迪哥的这个框架
1. 但是底层的实现都 **自己写**
1. 太复杂的不写
1. 有自己的微创新

成员
------

- 王霄池
- 谢康旺
- 杨萍

此框架参考了以下项目：

1. 房云框架
1. LazyPHP
2. Klein
3. Idiorm

盘点我们已经使用的设计模式

- 单例模式(Singleton) `Model::db()`
- 连贯接口(FluentInterface) `$model->where->find()`
- 工厂方法(Factory Method) `$model->forTabel('user')`

特征展示
-----------

**结构**

1. 模仿房云框架结构(文件夹名称全部小写)
3. 自动载入 library 中的类

**模型**

定义

```php
// table name: user
// primary key: user_id
// default method: add, edit, get, delete
class UserModel extends Model {}
```

查找

```php
$this->model('user')
    ->where('gender', 'female')
    ->where('birth_year', '>=', '1990')
    ->select();
```

more about search

```php
$this->model
    ->from(array('b' => 'blog'))
    ->from('blog')
    ->alias('b')
    ->colunm(array('name' => 'u.username'))
    ->join(array('u' => 'user'), array('u.id', 'blog.user_id'))
    ->where(new Expression('(`user_id`=? OR `username`=?)', array('3', 'Jack')))
    ->orderBy('user_id DESC')
    ->groupBy('city_id')
    ->having('sum', '>', 33)
    ->distinct()
    ->query('select * frorm user where user_id=?', array('3'))
    ->getLastSql()
    ->update($args)
    ->insert($args)
    ->delete()
```

write

```php
$this->model
    ->query('select * frorm user where user_id=?', array('3'))
    ->getLastSql()
    ->update($args)
    ->insert($args)
    ->delete()
```

user condition for search

```php
$this->model
    ->rules(array(
        'gt_age' => array('birth_year', '>');
    ))
    ->conditions(get())
    ->where(...)
```

**路由**

路由可以设置规则

```php
$router = new Router();
$router->rule('/latest', array('article', 'latest'));
$router->rule('/article/[:id]', array('article', 'view'));
$router->rule('GET', '/article/[:id]/edit', array('article', 'edit'));
$router->rule('POST', '/article/[:id]/edit', array('article', 'editSave'));
$router->rule('*', array('page404', 'index'));
$router->dispatch();
```

如果不设置规则，则路由会应用默认的规则。

```php
$router = new Router();
// $router->rule('/[:controller]/[:action]', array('{$controller}', '{$action}'));
// $router->rule('*', array('page404', 'index'));
$router->dispatch();
```

**验证**
    
