<?php

namespace Src\Provider;

use Src\Application;

abstract class AbstractProvider{
    //поле для доступа к объекту приложения из классов-потомков
    protected Application $app;

    public function __construct(Application $app){
        $this->app = $app;
    }

    abstract public function register(): void;

    abstract public function boot(): void;
}