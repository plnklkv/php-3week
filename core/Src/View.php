<?php

namespace Src;

use Exception;

class View
{
    private string $view = '';
    private array $data = [];
    private string $root = '';
    private string $layout = '/layouts/main.php';

    public function __construct(string $view = '', array $data = [])
    {
        $this->root = $this->getRoot();
        $this->view = $view;
        $this->data = $data;
    }

    //полный путь до директории с представл
    private function getRoot(): string
    {
        global $app;
        $root = $app->settings->getRootPath();
        $path = $app->settings->getViewsPath();

        return $_SERVER['DOCUMENT_ROOT'] . $root . $path;
    }

    //путь до осн файла с шаблоном сайта
    private function getPathToMain(): string
    {
        return $this->root . $this->layout;
    }

    //путь до текущего шаблона
    private function getPathToView(string $view = ''): string
    {
        $view = str_replace('.', '/', $view);
        return $this->getRoot() . "/$view.php";
    }

    public function render(string $view = '', array $data = []): string
    {
        $path = $this->getPathToView($view);

        if (file_exists($this->getPathToMain()) && file_exists($path)) {
            //импорт переменных из массива в текущую табл символов
            extract($data, EXTR_PREFIX_SAME, '');
            //вкл буферизации вывода
            ob_start();
            require $path;
            //помещение буфера в переменную и очистка
            $content = ob_get_clean();

            //возврат собранной страницы
            return require($this->getPathToMain());
        }
        throw new Exception('Error render');
    }

    public function __toString(): string
    {
        return $this->render($this->view, $this->data);
    }
}