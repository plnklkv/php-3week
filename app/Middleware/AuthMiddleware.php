<?php

namespace Middleware;

use Src\Auth\Auth;
use Src\Request;

class AuthMiddleware{
    public function handle(Request $request){
        //если польз-ль не авторизован, то редирект на страницу входа
        if(!Auth::check())
            app()->route->redirect('/login');
    }
}