<?php

namespace Src;

use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher\MarkBased as Dispatcher;
use Src\Traits\SingletonTrait;

class Middleware
{
    //использ трейт
    use SingletonTrait;

    private RouteCollector $middlewareCollector;

    public function add($httpMethod, string $route, array $action): void
    {
        $this->middlewareCollector->addRoute($httpMethod, $route, $action);
    }

    public function group(string $prefix, callable $callback): void
    {
        $this->middlewareCollector->addGroup($prefix, $callback);
    }

    //конструктор скрыт. вызывается только один раз
    private function __construct()
    {
        $this->middlewareCollector = new RouteCollector(new Std(), new MarkBased());
    }

    //запуск всех middlewares
    public function go(string $httpMethod, string $uri, Request $request): Request
    {
        return $this->runMiddlewares($httpMethod, $uri, $this->runAppMiddlewares($request));
    }

    //запуск всех middlewares для текущего маршрута
    private function runMiddlewares(string $httpMethod, string $uri, Request $request): Request
    {
        //список всех разрешенных классов middlewares из настроек приложения
        $routeMiddleware = app()->settings->app['routeMiddleware'];

        //перебор всех middlewares для текущего адреса
        foreach ($this->getMiddlewaresForRoute($httpMethod, $uri) as $middleware) {
            $args = explode(':', $middleware);
            //создание объекта и вызов метода handle
            $request = (new $routeMiddleware[$args[0]])->handle($request, $args[1] ?? null) ?? $request;
        }
        //итоговый request
        return $request;
    }

    //запуск всех глобальных middlewares
    private function runAppMiddlewares(Request $request): Request
    {
        //список всех разрешенных классов middlewares из настроек приложения
        $routeMiddleware = app()->settings->app['routeAppMiddleware'];

        //перебор и запуск их
        foreach ($routeMiddleware as $name => $class) {
            $args = explode(':', $name);
            $request = (new $class)->handle($request, $args[1] ?? null) ?? $request;
        }
        return $request;
    }

    //поиск middlewares по адресу
    private function getMiddlewaresForRoute(string $httpMethod, string $uri): array
    {
        $dispatcherMiddleware = new Dispatcher($this->middlewareCollector->getData());
        return $dispatcherMiddleware->dispatch($httpMethod, $uri)[1] ?? [];
    }
}
