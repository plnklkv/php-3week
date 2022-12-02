<?php

namespace Src\Auth;

use Src\Session;

class Auth
{
    //св-во для хранения любого класса, реализующего инт IdentityInterface
    private static IdentityInterface $user;

    //генерация нового токена для CSRF
    public static function generateCSRF(): string
    {
        $token = md5(time());
        Session::set('csrf_token', $token);
        return $token;
    }

    //Инициализация класса польз-ля
    public static function init(IdentityInterface $user): void
    {
        self::$user = $user;
        if (self::user())
            self::login(self::user());
    }

    //Вход польз-ля по модели
    public static function login(IdentityInterface $user): void
    {
        self::$user = $user;
        Session::set('id', self::$user->getId());
    }

    //Аутенфикация польз-ля и взод по учётным данным
    public static function attempt(array $credentials): bool
    {
        if ($user = self::$user->attemptIdentity($credentials)) {
            self::login($user);
            return true;
        }
        return false;
    }

    //Возврат текущего аутенфиц польз-ся
    public static function user()
    {
        $id = Session::get('id') ?? 0;
        return self::$user->findIdentity($id);
    }

    //Проверка явля-ся ли текущ польз аутентифицированным
    public static function check(): bool
    {
        if (self::user())
            return true;
        return false;
    }

    //выход текущ польз-ля
    public static function logout(): bool
    {
        Session::clear('id');
        return true;
    }
}