<?php

namespace Src;

use Error;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use Src\Auth\Auth;

class Application
{
    //список провайдеров
    private array $providers = [];
    //данные приложения
    private array $binds = [];

    public function __construct(array $settings = [])
    {
//        //привязка класса со всеми настройками приложения
//        $this->settings = $settings;
//        //привязка классса маршрутизации с установкой префикса
//        $this->route = Route::single()->setPrefix($this->settings->getRootPath());
//        //создание класса менеджера для бд
//        $this->dbManager = new Capsule();
//        //созд класса для аутенф-ии на основе настроек приложения
//        $this->auth = new $this->settings->app['auth'];
//
//        //настройка для работы с бд
//        $this->dbRun();
//        //инициализация класса польз-ля на основе настроек приложения
//        $this->auth::init(new $this->settings->app['identity']);
        $this->addProviders($settings['providers']??[]);
        $this->registerProviders();
        $this->bootProviders();

    }

    //зполнение списка провайдеров из массива
    public function addProviders(array $providers): void
    {
        foreach ($providers as $key => $class) {
            $this->providers[$key] = new $class($this);
        }
    }

    //запуск методов register() у всех провайдеров
    private function registerProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->register();
        }
    }

    //запуск методов bootProviders() у всех провайдеров
    private function bootProviders(): void
    {
        foreach ($this->providers as $provider) {
            $provider->boot();
        }
    }

    //публичный метод для добавления данных в приложение
    public function bind(string $key, $value): void
    {
        $this->binds[$key] = $value;
    }

    //доступ к внутренним данным извне
    public function __get($key)
    {
        if (array_key_exists($key, $this->binds)) {
            return $this->binds[$key];
        }
        throw new Error('Accessing a non-existent property in application');
    }

    public function run(): void
    {
        //запуск маршрутизации
        $this->route->start();
    }
}