
# Модуль Дополнительная оптимизация страниц

## Описание решения

Данный модуль перемещает css теги (link) вниз страницы, по рекомендации https://developers.google.com/speed/docs/insights/OptimizeCSSDelivery?hl=ru.
А так же содержит набор скриптов (см. в /bin/ модуля) для оптимизации css, js и изображений в указанных папках.

## Описание установки и настройки решения

- включите опцию в настройках для перемещения тегов стилей вниз
- для тегов которые не нужно переносить - пропишите атрибут data-skip-moving="true"
- применить очистку файлов кеша /bitrix/admin/cache.php?lang=ru

### Использование скриптов для оптимизации

- для использования оптимизированных версий css и js необходимо включить чекбокс в настройках "Главный модуль" - "Подключать минифицированные версии CSS и JS файлов"

- список файлов или папок для оптимизации задается в настройках модуля

- для сайта в Bitrix-окружении для Windows запускать из папки модуля \bitrix\modules\rodzeta.pageoptimizeplus\bin

`"../../../../../apache2/zendserver/bin/php" optimize-css.php`

`"../../../../../apache2/zendserver/bin/php" optimize-js.php`

`"../../../../../apache2/zendserver/bin/php" optimize-images.php`

`"../../../../../apache2/zendserver/bin/php" restore-images.php`

- для любой версии интерпретатора или если есть возможность запуска на сервере - запускать из папки модуля

`php optimize-css.php`

`php optimize-js.php`

`php optimize-images.php`

`php restore-images.php`

- восстановить оригиналы `php restore-images.php` (при оптимизации изображений делается бекап файлов - добавляется расширение .original если такой файл еще не существует)

- для оптимизации css и js - необходимо установить jre для возможности запуска java-приложений

- для Linux необходимо установить optipng и jpegoptim, например

`sudo apt install optipng`

`sudo apt install jpegoptim`

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.pageoptimizeplus/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.pageoptimizeplus/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
