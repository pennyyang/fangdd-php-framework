fangdd-php-framework
====================

为积分赛所做的小框架。
hh

关于这个小框架，我觉得可以这么来：

1. 实现路由（controller, 杨萍）和数据库（model, 王霄池），以及表单验证（valid, 谢康旺）
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
4. web.py

迪哥要求要用 MVC 模式，并且要使用单例模式和工厂模式。

为了满足他的要求，结合一些小框架的特点。我觉的下面这些是可行的：

2. 简单的路由分发
1. MVC（简单的模型、控制器、和渲染引擎（可以布局））

盘点我们已经使用的设计模式
- 单例模式(Singleton) `Model::db()` `Router::dispatch()`
- 连贯接口(FluentInterface) `$model->where->find()`
- 工厂方法(Factory Method) `$model->forTabel('user')`

特征
-----------

**结构**

1. 模仿房云框架结构
2. 文件夹名称全部小写
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
    ))
    ->conditions(get())
    ->where(...)
```
