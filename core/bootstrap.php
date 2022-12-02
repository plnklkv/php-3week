<?php
//подкл автозагрузчика composer
require_once __DIR__ . '/../vendor/autoload.php';

//новый объект приложения
$app = new Src\Application(require __DIR__ . '/../config/app.php');

//подкл хелперов
require_once __DIR__ . '/../core/helpers.php';

return $app;