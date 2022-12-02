<?php

namespace Src;

use Error;

class Settings
{
    private array $_settings;

    public function __construct(array $_settings = [])
    {
        $this->_settings = $_settings;
    }

    //доступ к настройкам
    public function __get($key)
    {
        if (array_key_exists($key, $this->_settings))
            return $this->_settings[$key];
        throw new Error('Accessing a non-existent property');
    }

    //возвращает url путь до приложения
    public function getRootPath(): string
    {
        return $this->path['root'] ? '/' . $this->path['root'] : '';
    }

    //путь до шабл представл
    public function getViewsPath(): string
    {
        return '/' . $this->path['views'] ?? '';
    }

    //возвр настроек бд
    public function getDbSetting(): array
    {
        return $this->db ?? [];
    }

    public function getRoutePath(): string
    {
        return '/' . $this->path['routes'] ?? '';
    }

    public function getAuthClassName(): string
    {
        return $this->app['auth'] ?? '';
    }

    public function getIdentityClassName(): string
    {
        return $this->app['identity'] ?? '';
    }

    public function removeAppMiddleware(string $key): void
    {
        unset($this->_settings['app']['routeAppMiddleware'][$key]);
    }

}