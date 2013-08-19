<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sample App</title>
    <link rel="stylesheet" href="/css/reset.css" type="text/css"/>
    <link rel="stylesheet" href="/css/main.css" type="text/css"/>
    <script type="text/javascript" src="/js/main.js"></script>
</head>
<body>
<div>
    <a href="/">Главная</a>
    <?php if (\Kernel\App::identity()->isEmpty()):?>
        <a href="/login">Войти</a>
        <a href="/register">Регистрация</a>
    <?php else: ?>
        <a href="/logout">Выйти</a>
    <?php endif; ?>
</div>
<?php
if (!empty($this->layoutTitle)) echo '<h1>' . \Kernel\Html::encode($this->layoutTitle) . '</h1>';
$flash = \Kernel\App::getFlash('success');
if (!empty($flash)) echo '<div class="flash-success">' . \Kernel\Html::encode($flash) . '</div>';
?>
<?php echo $content; ?>
</body>
</html>
