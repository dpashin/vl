<?php $this->layoutTitle = 'Регистрация'; ?>
<form class="register-form" method="post" action="">
    <?php echo $this->renderFormErrors($model); ?>
    <p>
        <label for="User[email]">E-mail</label>
        <input name="User[email]" type="email" value="<?php echo \Kernel\Html::encode($model->email); ?>" required=""/>
    </p>

    <p>
        <label for="User[password]">Пароль</label>
        <input name="User[password]" type="password" value="<?php echo \Kernel\Html::encode($model->password); ?>"
               required=""/>
    </p>

    <p>
        <label for="User[password]">Повторите пароль</label>
        <input name="User[password_repeat]" type="password"
               value="<?php echo \Kernel\Html::encode($model->password_repeat); ?>" required=""/>
    </p>

    <p>
        <input type="submit" value="Зарегистрироваться"/>
    </p>

    <p>
        <a href="/resend">Повторная отправка письма для активации аккаунта</a>
    </p>
</form>

