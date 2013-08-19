function processUpload() {
    if (!window.FormData) {
        alert('Ваш браузер не поддерживает объект FormData');
        return false;
    }
    var loader = document.getElementById('ajax-loader');
    loader.style.display = 'inline';

    var form = document.getElementById('upload-form');

    xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState != 4) // complete
            return;
        loader.style.display = 'none';

        var response;
        if (xhr.status == 200) {
            response = JSON.parse(xhr.responseText);
            form.reset();
        }
        else
            response = [
                {error: 'Ошибка при загрузке: ' + xhr.statusText}
            ];

        var status = document.getElementById('upload-status');
        status.innerHTML = '';
        var loaded = 0;
        var list = document.getElementById('upload-list');
        for (var i = 0; i < response.length; i++) {
            if (response[i].error !== undefined)
                status.innerHTML += '<p class="error">' + response[i].error + "</p>";
            else {
                var node = document.createElement("div");
                node.className = "item";
                node.innerHTML = '<a href="/uploads/' + response[i].filename + '" target="_blank">' +
                    '<img src="/uploads/' + response[i].thumbname + '"/>' + '</a>';
                list.insertBefore(node, list.firstChild);
                loaded += 1;
            }
        }
        if (loaded)
            status.innerHTML += '<p class="success">Загружено файлов: ' + loaded + '.</p>';
    }
    xhr.open("POST", form.action);
    xhr.send(new FormData(form));
    return false;
}
