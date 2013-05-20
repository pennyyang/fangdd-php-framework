fangdd-php-framework
====================

为积分赛所做的小框架。

关于这个小框架，我觉得可以这么来：

1. 实现路由（controller）和数据库（model），以及表单验证（valid）
1. 一切的表现形式都学迪哥的这个框架
1. 但是底层的实现都自己写
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
- 单例模式(Singleton) `Model::db()`
- 连贯接口(FluentInterface) `$model->where->find()`
- 工厂方法(Factory Method) `$model->forTabel('user')`
