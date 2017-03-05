
# Дополнительная оптимизация страниц

## Описание решения

Данный модуль перемещает css теги (link) вниз страницы, по [рекомендации Google](https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery?hl=ru).
А так же содержит набор скриптов (см. в /cli/ модуля) для оптимизации css, js и изображений в указанных папках.

## Описание установки и настройки решения

- включите опцию в настройках для перемещения тегов стилей вниз
- для тегов которые не нужно переносить - пропишите атрибут data-skip-moving="true"
- для использования оптимизированных версий css и js необходимо включить чекбокс в настройках "Главный модуль" - "Подключать минифицированные версии CSS и JS файлов"
- применить очистку файлов кеша /bitrix/admin/cache.php?lang=ru

### Утилиты для оптимизаций

Скачать из [репозитария](https://github.com/rivetweb/rodzeta.pageoptimizeplus/tree/master/bin) и положить в папку `bin` модуля на сайте.

Или скачать и положить в папку `bin` с сайта разработчиков:

- js optimize tools https://dl.google.com/closure-compiler/compiler-latest.zip

- css optimize tools https://github.com/fmarcia/UglifyCSS - `npm install uglifycss -g`

- image optimize tools https://ru.wordpress.org/plugins/ewww-image-optimizer/ (находятся в папке binaries плагина)

### Как вытащить список ресурсов для оптимизации для заданной страницы

После выполнения проверки страницы https://developers.google.com/speed/pagespeed/insights/?url=villa-mia.ru - в интерактивной строке в консоли браузера ввести

`var res = ["---"]; for (let obj of document.querySelectorAll(".url-external")) res.push(obj.getAttribute("data-title")); res.push("---"); console.log(res.join("\n"));`

### Как выполнить правило "Используйте кеш браузера"

Увеличить кол-во дней для кеширования ресурсов, пример для Apache
```
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType image/jpeg "access plus 30 day"
    ExpiresByType image/gif "access plus 30 day"
    ExpiresByType image/png "access plus 30 day"
    ExpiresByType text/css "access plus 30 day"
    ExpiresByType application/javascript "access plus 30 day"
</IfModule>
```

### Использование скриптов для оптимизации

- список файлов или папок для оптимизации задается в настройках модуля
- для оптимизации css и js - необходимо установить jre для возможности запуска java-приложений
- для сайта в Bitrix-окружении для Windows запускать из папки модуля `bitrix/modules/rodzeta.pageoptimizeplus/cli`
    ```
    "../../../../../apache2/zendserver/bin/php" optimize-css.php

    "../../../../../apache2/zendserver/bin/php" optimize-js.php

    "../../../../../apache2/zendserver/bin/php" optimize-images.php

    "../../../../../apache2/zendserver/bin/php" restore-images.php
    ```
- для любой версии интерпретатора или если есть возможность запуска на сервере - запускать из папки модуля `bitrix/modules/rodzeta.pageoptimizeplus/cli`
    ```
    php optimize-css.php

    php optimize-js.php

    php optimize-images.php

    php restore-images.php
    ```
- восстановить оригиналы `php restore-images.php` (при оптимизации изображений делается бекап файлов - добавляется расширение .original если такой файл еще не существует)

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, запросы только по [e-mail](mailto:rivetweb@yandex.ru)

[Багрепорты и предложения](https://github.com/rivetweb/rodzeta.pageoptimizeplus/issues)

## Ссылка на демо-версию

[http://villa-mia.ru/](http://villa-mia.ru/)
