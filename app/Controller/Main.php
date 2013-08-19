<?php

namespace Controller;

class Main extends \Kernel\Controller
{
    public $layoutTitle;

    public function actionIndex()
    {
        $list = \Model\Upload::findAll([], 'ORDER BY id DESC');
        $this->render('index', ['list' => $list]);
    }

    public function actionUpload()
    {
        if (\Kernel\App::identity()->isEmpty() || !array_key_exists('file', $_FILES))
            $this->error(403, 'Доступ запрещен');

        $result = \Model\Upload::processUpload();
        header("Content-type: text/javascript");
        echo json_encode($result);
    }

    public function actionRegister()
    {
        $model = new \Model\User;

        if (array_key_exists('User', $_POST)) {
            $model->setAttributes($_POST['User']);
            if ($model->validate()) {
                $model->create();
                $this->sendMail($model);
                \Kernel\App::redirect('/');
            }
        }
        $this->render('register', ['model' => $model]);
    }

    public function actionActivate()
    {
        if (!array_key_exists('user', $_GET))
            $this->error(403, 'Неизвестный аккаунт');
        $userId = $_GET['user'];
        if (!array_key_exists('key', $_GET))
            $this->error(403, 'Ключ активации не указан');
        $key = $_GET['key'];
        $user = \Model\User::findOne(['id' => $userId]);
        if (!isset($user))
            $this->error(403, 'Аккаунт не найден');
        if ($key !== $user->activate_key)
            $this->error(403, 'Некорректный ключ активации');
        if (!empty($user->active))
            $this->error(403, 'Аккаунт уже активирован.');
        $user->active = \Model\User::STATE_ACTIVE;
        $user->activate_key = null;
        $user->update(['active', 'activate_key']);
        \Kernel\App::setFlash('success', 'Аккаунт активирован.');
        \Kernel\App::redirect('/login');
    }

    public function actionLogin()
    {
        if (array_key_exists('User', $_POST)) {
            $errorMessage = 'Некорректный e-mail или пароль';
            $email = array_key_exists('email', $_POST['User']) ? $_POST['User']['email'] : '';

            $model = \Model\User::findOne(['email' => $email]);

            if (isset($model)) {
                $password = array_key_exists('password', $_POST['User']) ? $_POST['User']['password'] : '';

                if ($model->active == \Model\User::STATE_INACTIVE) {
                    $errorMessage = 'Вам необходимо активировать акккаунт. Для этого перейдите по ссылке из письма, полученном после регистрации.';
                } else if ($model->active == \Model\User::STATE_ACTIVE) {
                    if (crypt($password, $model->password_salt) === $model->password_hash) {
                        \Kernel\App::identity()->set($model->id);
                        \Kernel\App::redirect('/');
                    }
                } else {
                    $errorMessage = 'Аккаунт заблокирован';
                }
            }
            $model = new \Model\User();
            $model->errors[] = $errorMessage;
            $model->email = $email;
        } else {
            $model = new \Model\User();
        }
        $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        \Kernel\App::identity()->set(null);
        session_destroy();
        \Kernel\App::redirect('/');
    }

    public function actionResend()
    {
        if (array_key_exists('User', $_POST)) {
            $email = array_key_exists('email', $_POST['User']) ? $_POST['User']['email'] : '';
            $model = \Model\User::findOne(['email' => $email]);
            if (is_null($model) || $model->active != \Model\User::STATE_INACTIVE) {
                $model->errors[] = 'Аккаунт с этим e-mail не найден или уже активирован.';
            } else {
                $this->sendMail($model);
                \Kernel\App::redirect('/');
            }
        } else {
            $model = new \Model\User();
        }

        $this->render('resend', ['model' => $model]);
    }

    public function renderFormErrors($model)
    {
        return $this->includeTemplate('Main/_form_errors', ['model' => $model]);
    }

    public function error($code, $message)
    {
        header("HTTP/1.0 " . $code);
        $this->render("error", ['code' => $code, 'message' => $message]);
        exit(1);
    }

    private function sendMail($model)
    {
        $message = $this->includeTemplate("Mailer/register", [
            'link' => $this->activationUrl($model->id, $model->activate_key),
        ]);
        if (\Kernel\App::config()->sendmail)
            mail(
                $model->email,
                "Подтверждение регистрации на сайте " . $_SERVER["SERVER_NAME"],
                $message,
                "Content-type: text/plain; charset=\"utf-8\""
            );
        \Kernel\App::setFlash('success', "На ваш e-mail выслано письмо со ссылкой для активации аккаунта.");
    }

    private function activationUrl($userId, $key)
    {
        return 'http://' . $_SERVER["SERVER_NAME"] . "/activate?user=$userId&key=$key";
    }

}