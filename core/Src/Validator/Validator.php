<?php

namespace Src\Validator;

class Validator
{
    //разрешенные валидаторы
    private array $validators = [];
    //итоговые ошибки
    private array $errors = [];
    //проверяемые поля
    private array $fields = [];
    //массив правил
    private array $rules = [];
    //кастомные сообщения
    private array $messages = [];

    public function __construct(array $fields, array $rules, array $messages = [])
    {
        $this->validators = app()->settings->app['validators'] ?? [];
        $this->fields = $fields;
        $this->rules = $rules;
        $this->messages = $messages;
        $this->validate();
    }

    //перебор списка всех валидируемых полей
    //и вызов метода validateField() для каждого поля
    private function validate(): void
    {
        foreach ($this->rules as $fieldName => $fieldValidators)
            $this->validateField($fieldName, $fieldValidators);
    }

    //валидация отдельного поля
    private function validateField(string $fieldName, array $fieldValidators): void
    {
        //перебор всех валидаторов, ассоциированных с полем
        foreach ($fieldValidators as $validatorName) {
            //отделение от имени валидатора доп аргументов
            $tmp = explode(':', $validatorName);
            [$validatorName, $args] = count($tmp) > 1 ? $tmp : [$validatorName, null];
            $args = isset($args) ? explode(',', $args) : [];

            //соотношение имени валидатора с классом в массиве разрешенных валидаторов
            $validatorClass = $this->validators[$validatorName];
            if (!class_exists($validatorClass)) {
                continue;
            }
            //создание объекта валидатора, передача туда параметров
            $validator = new $validatorClass(
                $fieldName,
                $this->fields[$fieldName],
                $args,
                $this->messages[$validatorName]);

            //если валидация не прошла, то доб ошибка в общий массив ошибок
            if (!$validator->rule()) {
                $this->errors[$fieldName][] = $validator->validate();
            }
        }
    }

    //возврат массива найденных ошибок
    public function errors(): array
    {
        return $this->errors;
    }

    //признак успешной валидации
    public function fails(): bool
    {
        return (bool)count($this->errors);
    }
}