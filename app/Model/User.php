<?php

namespace Model;

class User extends \Kernel\Model
{
    public static $tablename = 'users';
    public static $columns = ['id', 'email', 'password_hash', 'password_salt', 'active', 'activate_key'];

    const STATE_INACTIVE = 0;
    const STATE_ACTIVE = 1;
    const STATE_DISABLED = 2;

    public $id;
    public $email;
    public $password;
    public $password_repeat;
    public $password_hash;
    public $password_salt;
    public $active = self::STATE_INACTIVE;
    public $activate_key;

    public function setAttributes($data)
    {
        foreach (['email', 'password', 'password_repeat'] as $key)
            if (array_key_exists($key, $data))
                $this->$key = $data[$key];
    }

    public function validate()
    {
        $this->errors = [];

        if (strlen($this->email) === 0)
            $this->errors[] = 'E-mail не указан.';

        if (strlen($this->email) > 50)
            $this->errors[] = 'Слишком длинный email (50 символов максимум).';

        if (strlen($this->password) < 6)
            $this->errors[] = 'Слишком короткий пароль (6 символов минимум).';

        if (strlen($this->password) > 50)
            $this->errors[] = 'Слишком длинный пароль (50 символов максимум).';

        if ($this->password !== $this->password_repeat)
            $this->errors[] = 'Пароль и подтверждение пароля не совпадают.';

        if (self::findOne(['email' => $this->email]) !== null)
            $this->errors[] = 'Пользователь с таким email уже зарегистрирован.';

        return !$this->hasErrors();
    }

    public function create()
    {
        $this->activate_key = md5(rand());
        $this->password_salt = rand();
        $this->password_hash = crypt($this->password, $this->password_salt);
        parent::create();
    }

}