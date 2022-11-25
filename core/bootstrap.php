<?php
//путь до дир с конфигр=урационными файлами
const DIR_CONFIG = '/../config';

//подкл автозагрузчика composer
require_once __DIR__ . '/../vendor/autoload.php';

//функция, возвр массив всех настроек приложения
function getConfigs(string $path = DIR_CONFIG): array
{
    $settings = [];
    foreach (scandir(__DIR__ . $path) as $file) {
        $name = explode('.', $file) [0];
        if (!empty($name))
            $settings[$name] = include __DIR__ . "$path/$file";
    }
    return $settings;
}

//подкл файла web.php
require_once __DIR__ . '/../routes/web.php';

//создается новый объект приложения которому в конструктор передается новый объект
//с настройками, которому в конструктор передается массив с настройками
return new Src\Application(new Src\Settings(getConfigs()));