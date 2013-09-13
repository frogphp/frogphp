Frogphp - php学习框架!  
---------
本框架根据MVC模式编写，完全实现面向对象!  
 作者：silenceper  
 email:silenceper@gmail.com  
 website:http://silenceper.com  

#快速入门

###1、入口文件配置：
```php
	define('APP_PATH','./web/');
	define('APP_NAME','web');
	define('APP_DEBUG',true);
	require './frogphp/Frogphp.php';
```

手动创建项目目录:  

./web/common  : 存放 config.php 配置文件，functions.php 公用函数  
./web/controllers  : 所有控制器都在里，例如定义index控制器:IndexController.class.php  
./web/models : 存放项目model 例如UserModel.class.php  
./web/runtime  : smarty 缓存  
./web/views :视图  
###2、输出hello world
创建控制器IndexController.class.php

```php
class IndexController extends Controller{
		public function index(){
			echo 'hello world';
		}
	}
```

这样就可以通过 http://localhost/index.php?c=index&amp;a=index

**所有控制器都必须继承Controller基类!  
默认controller 为index，默认的action为index**

###3、使用数据库
数据库配置：

在./web/common/config.php 中配置:

```php
<?php return array(   
	'db'=-->array(
	'connectionString'=>'mysql:host=localhost;dbname=demodb',
	'dbType'=>'pdo',
	'username'=>'root',
	'password'=>'123',
	'tablePrefix'=>'frog_',
	'charset'=>'utf8'
),
)
?>
```
在models目录下创建 UserModel.class.php 文件

```php
class UserModel extends Model{
	public function getAllUser(){
		$sql="select * from `{{user}}`";
		return $this->query($sql);
	}
}
```

**在controller中调用getAllUser 方法:**

```php

$userData=M('user')->getAllUser();

```

实例化UserModel类应该使用M(‘user’) ，M方法可以帮你实现实例化model并防止在controller重复调用model而重复实例化造成的性能损失！

**所有的model都必须继承Model基类。**

###4、使用视图
Frogphp 框架视图层使用smarty模板引擎，重写了display方法方便使用. 

例如渲染某个视图:  

```php
$this->display();  
```

表示渲染  

./web/views/default/index/index.htm 文件  

其中default 为默认模板名可在配置文件中更改.  

index目录为控制器名.   

index.html 对应的就是默认控制器index.  

想要渲染./web/views/default/site/index.htm    

就可以使用  
```php
$this->display('site/index');  
```
