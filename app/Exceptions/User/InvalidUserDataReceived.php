<?php


namespace App\Exceptions\User;


use Throwable;

class InvalidUserDataReceived extends \Exception
{
    protected $errors = [];
    const MESSAGE = "Invalid user data was received.";

    public function __construct($errors = [])
    {
        $this->errors = $errors;
        parent::__construct(self::MESSAGE, 0, null);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
