<?php

use Model\User;
use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @runInSeparateProcess
     */
    public function testSignup(string $httpMethod, array $userData, string $message): void
    {
        //выбор занятого логина из бд
        if ($userData['login'] === 'login is busy') {
            $userData['login'] = User::get()->first()->login;
        }

        //заглушка для класса Request
        $request = $this->createMock(\Src\Request::class);
        //переопределение метода all() и св-ва method
        $request->expects($this->any())
            ->method('all')
            ->willReturn($userData);
        $request->method = $httpMethod;

        //сохранение рез-та работы метода в переменную
        $result = (new \Controller\Site())->signup($request);

        if (!empty($result)) {
            //проверка вариантов с ошибками валидации
            $message = '/' . preg_quote($message, '/') . '/';
            $this->expectOutputRegex($message);
            return;
        }

        //проверка добавился ли пользователь в бд
        $this->assertTrue((bool)User::where('login', $userData['login'])->count());
        //Удаляем созданного пользователя из базы данных
        User::where('login', $userData['login'])->delete();

        //проверка редиректа при успешной регистрации
        $this->assertContains($message, xdebug_get_headers());
    }

    //возвр набор тестовых данных
    public function additionProvider(): array
    {
        return [
            ['GET', ['name' => '', 'login' => '', 'password' => ''],
                '<h3></h3>'
            ],
            ['POST', ['name' => '', 'login' => '', 'password' => ''],
                '<h3>{"name":["Поле name пусто"],
                      "login":["Поле login пусто"],
                      "password":["Поле password пусто"]}</h3>',
            ],
            ['POST', ['name' => 'admin', 'login' => 'login is busy', 'password' => 'admin'],
                '<h3>{"login":["Поле login должно быть уникально"]}</h3>',
            ],
            ['POST', ['name' => 'admin', 'login' => md5(time()), 'password' => 'admin'],
                'Location: /php-3week/login'
            ]
        ];
    }

    //настройка конфигурации окружения
    protected function setUp(): void
    {
        //установка переменной среды
        $_SERVER['DOCUMENT_ROOT'] = 'C:/xampp/htdocs'; // /var/www

        //создание экз приложения
        $GLOBALS['app'] = new \Src\Application(new \Src\Settings([
            'app' => include $_SERVER['DOCUMENT_ROOT']
                . '/php-3week/config/app.php',
            'db' => include $_SERVER['DOCUMENT_ROOT']
                . '/php-3week/config/db.php',
            'path' => include $_SERVER['DOCUMENT_ROOT']
                . '/php-3week/config/path.php'
        ]));

        //глобал ф-ия для доступа к объекту приложения
        if (!function_exists('app')) {
            function app()
            {
                return $GLOBALS['app'];
            }
        }
    }
}