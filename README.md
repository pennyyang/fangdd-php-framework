fangdd-php-framework
====================

为积分赛所做的小框架。

首先，我到网上找了一些小框架来研究了一番。

1. LazyPHP
2. Klein
3. Idiorm
4. web.py

迪哥要求要用 MVC 模式，并且要使用单例模式和工厂模式。

为了满足他的要求，结合一些小框架的特点。我觉的下面这些是可行的：

1. 整个框架 $app = App::app(); 使用 **单例模式**
1. MVC（简单的模型、控制器、和渲染引擎（可以布局））
2. 简单的路由分发
3. 自带简单的ORM，使用 **工厂模式**（我已经写好了）
4. 使用 memcache 的简单缓存
