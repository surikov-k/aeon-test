

Авторизация по любому из номеров телефонов в таблице users, код подтверждения 1111. В проекте сейчас только одна вкладка Plots (раздел с участками, где выводится краткая информация о них и функционал редактирования). Надо добавить второй раздел Users (пользователи) с аналогичным функционалом, где будет выводиться информация о владельцах участков и возможность отредактировать данные пользователя.

Требования:
- [x] таблица Users с владельцами участков (колонки Plot ID, First name, Last Name, Phone, Email, Last login)
- [x] пагинация по 20 записей на страницу (аналогично таблице Plots)
- [x] поиск по номеру телефона, имени и email пользователя
- [x] страница реализуется в схожем дизайне, как страница с Plots
- [x] возможность создания/редактирования пользователя (поля First name, Last name, Phone, Email, Plots)
- [x] должна поддерживаться возможность добавить пользователя сразу к нескольким участкам (через запятую в поле Plots)
- [x] если при редактировании какие-либо поля, кроме Plots не заполнены, не давать сохранить данные
- [x] при сохранении данных телефон фильтруется по нечисловым символам, email переводится в lower case
- [x] в меню при выборе раздела Users он должен подсвечиваться аналогично выбору Plots
- [x] возможность удаления пользователя
