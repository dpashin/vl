<?php $this->layoutTitle = 'Повторная отправка письма для активации аккаунта'; ?>
<form class="resend-form" method="post" action="">
    <?php echo $this->renderFormErrors($model); ?>
    <p>
        <label for="User[email]">E-mail</label>
        <input name="User[email]" type="email" value="<?php echo \Kernel\Html::encode($model->email); ?>" required=""/>
    </p>
    <p>
        <input type="submit" value="Отправить"/>
    </p>
</form>
