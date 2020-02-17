# bx24.IT-M_localApp

Локальное приложение для Битрикс24 для компании IT-M

### Зависимости

Приложение использует

* [SDK Битрикс24](https://github.com/bitrix-tools/crest)
* [Vue.js](https://vuejs.org/)
* [Axios](https://github.com/axios/axios)

Для работы приложения необходимо использовать [SDK Битрикс24](https://github.com/bitrix-tools/crest)

### Структура файлов

`|/app` - папка приложения
`|-/api` - папка с файлами для ajax-запросов
`|--taskList.php` - формирует массив списка задач в статусе "Новая" и "Подтверждена мастером"
`|--taskDetail.php` - формирует массив карточки задачи с фотографиями (если есть) и списком товаров привязанной сделки
`|--taskUpdate.php` - меняет статус задачи и добавляет фотографии
`|--dealUpdate.php` - удаляет товары из прикреплённой к задаче сделке
`|-/configs` - папка с настройками. Сюда нужно загрузить файлы класса `CRest` (`crest.php` и `settings.php`)
`|--configs.php` - переменные и функции для работы приложения. Скопируйте их в `settings.php` или подключите в нужных местах
`|-/viev` - папка с шаблоном приложения
`|--/assets` - папка со стилями и скриптами
`|---app.js`
`|---style.css`
`|--header.php`
`|--footer.php`
`|app.php` - главная страница приложения, выводит список задач пользователя
`|detail.php` - страница карточки задачи
`install.php` - установочный файл приложения