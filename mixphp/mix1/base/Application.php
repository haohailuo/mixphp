<?php

namespace mix\base;

/**
 * App类
 * @author 刘健 <coder.liu@qq.com>
 *
 * @property \mix\base\Route $route
 * @property \mix\base\Log $log
 * @property \mix\web\Request|\mix\swoole\Request|\mix\console\Request $request
 * @property \mix\web\Response|\mix\swoole\Response|\mix\console\Response $response
 * @property \mix\web\Error $error
 * @property \mix\web\Session $session
 * @property \mix\web\Cookie $cookie
 * @property \mix\web\Token $token
 * @property \mix\client\Pdo $rdb
 * @property \mix\client\Redis $redis
 * @property \mix\swoole\Request $wsRequest
 * @property \mix\swoole\Response $wsResponse
 * @property \mix\websocket\Token $wsToken
 * @property \mix\websocket\Session $wsSession
 */
class Application
{

    // 基础路径
    public $basePath = '';

    // 控制器命名空间
    public $controllerNamespace = 'index\controller';

    // 组件配置
    public $components = [];

    // 对象配置
    public $objects = [];

    // 组件容器
    protected $_components;

    // NotFound错误消息
    protected $_notFoundMessage = 'Not Found';

    // 构造
    public function __construct($attributes)
    {
        // 导入属性
        foreach ($attributes as $name => $attribute) {
            $this->$name = $attribute;
        }
        // 快捷引用
        \Mix::setApp($this);
    }

    // 创建对象
    public function createObject($name)
    {
        return \Mix::createObject($this->objects[$name]);
    }

    // 装载组件
    public function loadComponent($name)
    {
        // 未注册
        if (!isset($this->components[$name])) {
            throw new \mix\exception\ComponentException("组件不存在：{$name}");
        }
        // 使用配置创建新对象
        $object = \Mix::createObject($this->components[$name]);
        // 组件效验
        if (!($object instanceof Component)) {
            throw new \mix\exception\ComponentException("不是组件类型：{$this->components[$name]['class']}");
        }
        // 装入容器
        $this->_components[$name] = $object;
    }

    // 执行功能并返回
    public function runAction($method, $action, $controllerAttributes = [])
    {
        $action = "{$method} {$action}";
        // 路由匹配
        list($action, $queryParams) = \Mix::app()->route->match($action);
        // 执行功能
        if ($action) {
            // 路由参数导入请求类
            \Mix::app()->request->setRoute($queryParams);
            // index处理
            if (isset($queryParams['controller']) && strpos($action, ':action') !== false) {
                $action = str_replace(':action', 'index', $action);
            }
            // 实例化控制器
            $action    = "{$this->controllerNamespace}\\{$action}";
            $classFull = \mix\base\Route::dirname($action);
            $classPath = \mix\base\Route::dirname($classFull);
            $className = \mix\base\Route::snakeToCamel(\mix\base\Route::basename($classFull), true);
            $method    = \mix\base\Route::snakeToCamel(\mix\base\Route::basename($action), true);
            $class     = "{$classPath}\\{$className}Controller";
            $method    = "action{$method}";
            try {
                $reflect = new \ReflectionClass($class);
            } catch (\ReflectionException $e) {
                throw new \mix\exception\NotFoundException($this->_notFoundMessage);
            }
            $controller = $reflect->newInstanceArgs([$controllerAttributes]);
            // 判断方法是否存在
            if (method_exists($controller, $method)) {
                // 执行控制器的方法
                return $controller->$method();
            }
        }
        throw new \mix\exception\NotFoundException($this->_notFoundMessage);
    }

    // 获取配置目录路径
    public function getConfigPath()
    {
        return $this->basePath . 'config' . DIRECTORY_SEPARATOR;
    }

    // 获取运行目录路径
    public function getRuntimePath()
    {
        return $this->basePath . 'runtime' . DIRECTORY_SEPARATOR;
    }

}
