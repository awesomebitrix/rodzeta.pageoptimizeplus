
# Модуль Дополнительная оптимизация страниц

## Описание

Данный модуль перемещает css вниз страницы для выполнения требования инструмента PageSpeed Insights - "Удалите код JavaScript и CSS, блокирующий отображение верхней части страницы".

## Как работает

- включите опцию в настройках для перемещения тегов стилей вниз;
- для тегов которые не нужно переносить - пропишите атрибут data-skip-moving="true"
- применить очистку файлов кеша /bitrix/admin/cache.php?lang=ru

## Демо сайт

http://villa-mia.ru/

## Тех. поддержка и кастомизация

Оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.pageoptimizer/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.pageoptimizer/pulls

## TODO

- оптимизации js и css с помощью сторонних сервисов
