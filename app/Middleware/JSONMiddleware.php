<?php

namespace Middlewares;

use Exception;
use Src\Request;
use Src\Session;
use function Collect\collection;

class JSONMiddleware
{
    public function handle(Request $request): Request
    {
        if ($request->method === 'GET') {
            return $request;
        }

        //получение неструктурированных json данных и преобразование их в массив
        $data = json_decode(file_get_contents("php://input"), true) ?? [];

        //слив массива в request
        collection($data)->each(function ($item, $key, $request) {
            $request->set($key, $item);
        }, $request);

        return $request;
    }
}