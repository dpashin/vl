<?php
if (!empty($model->errors)) {
    echo '<ul class="form-errors">';
    foreach ($model->errors as $error)
        echo "<li>" . \Kernel\Html::encode($error) . "</li>";
    echo '</ul>';
}