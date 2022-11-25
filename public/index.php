<?php

//вкл запрета на неявное преобразование типов
declare(strict_types=1);

try {
    //создание экземпляра приложение и его запуск
    $app = require_once __DIR__ . '/../core/bootstrap.php';
    $app->run();
} catch (\Throwable $exception) {
    echo '<pre>';
    print_r($exception);
    echo '<pre>';
}