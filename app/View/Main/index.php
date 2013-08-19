<?php $this->layoutTitle = 'Главная'; ?>
<?php if (!\Kernel\App::identity()->isEmpty()): ?>
    <form id="upload-form" class="upload-form" onsubmit="return processUpload();" method="post" action="/upload"
          enctype="multipart/form-data">
        <h2>Загрузка фотографий</h2>

        <div id="upload-status"></div>

        <p>Чтобы загрузить несколько фотографий, удерживайте Shift или Ctrl при выборе файлов.</p>
        <input name="file[]" type="file" multiple=""/>
        <input type="submit" value="Загрузить"/>
        <img id="ajax-loader" src="/images/ajax-loader.gif"/>
    </form>
<?php endif; ?>

<h2>Фотографии</h2>
<div id="upload-list" class="upload-list">
    <?php foreach ($list as $item): ?>
        <div class="item">
            <a href="/uploads/<?php echo $item->filename; ?>" target="_blank">
                <img src="/uploads/<?php echo $item->thumbname; ?>"/>
            </a>
        </div>
    <?php endforeach; ?>
</div>