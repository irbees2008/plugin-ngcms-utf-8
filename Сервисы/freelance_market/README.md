# Freelance Market (NGCMS plugin)

Плагин фриланс‑биржи для заявок на физические работы: публикация задач заказчиками, отклики исполнителей, скрытие контактов заказчика и продажа доступа (10/30 дней) через Robokassa.

## Возможности

- Заявки (jobs): список, просмотр, создание (для авторизованных).
- Контакты скрыты, видны только при активном доступе или автору заявки.
- Покупка доступа на 10/30 дней (Robokassa, test/prod режим).
- Отклики (bids): исполнители отвечают на задачу, указывают сообщение и предложенную цену.
- Счётчик просмотров заявки.
- База для будущего рейтинга (таблица rating) и расширений.

## Установка

1. Зайдите в Админку → Плагины → freelance_market → Установить.
2. Плагин создаст таблицы:
   - `prefix_freelance_jobs` — заявки
   - `prefix_freelance_bids` — отклики
   - `prefix_freelance_user_access` — сроки доступа к контактам
   - `prefix_freelance_pay_order` — заказы на оплату
   - `prefix_freelance_rating` — оценки (задача на будущее)

## Настройки

Админка → Плагины → freelance_market → Настроить:

- Тарифы: `price_10`, `price_30` (руб.)
- Robokassa: `robokassa_login`, `robokassa_pass1`, `robokassa_pass2`, `robokassa_is_test`

В кабинете Robokassa пропишите адреса:

- Result URL: `https://<ваш-домен>/?plugin=freelance_market&handler=result`
- Success URL: `https://<ваш-домен>/?plugin=freelance_market&handler=success`
- Fail URL: `https://<ваш-домен>/?plugin=freelance_market&handler=fail`

## Маршруты (frontend)

- Список заявок: `/?plugin=freelance_market`
- Просмотр заявки: `/?plugin=freelance_market&handler=job&id=ID`
- Новая заявка: `/?plugin=freelance_market&handler=new` (требуется вход)
- Создание заявки (POST): `/?plugin=freelance_market&handler=create`
- Покупка доступа: `/?plugin=freelance_market&handler=buy[&days=10|30]`
- Обработчик платежа: `result`, страница `success`/`fail`
- Мои заявки: `/?plugin=freelance_market&handler=my`
- Добавить отклик (POST): `/?plugin=freelance_market&handler=bid`

## Виджет (блок) последних заявок

Плагин регистрирует Twig-функцию для вывода блока с последними заявками:

```
{{ freelance_market_show({ number: 5 }) }}
```

- `number` — количество заявок (по умолчанию 5).
- `template` — опционально, имя пользовательского шаблона блока (см. ниже).

Шаблон блока по умолчанию: `engine/plugins/freelance_market/tpl/block/block_freelance_market.tpl`.
Переопределить в теме: скопируйте в
`templates/<skin>/plugins/freelance_market/block/block_freelance_market.tpl` и правьте под свой дизайн.

## Шаблоны (tpl)

- `tpl/list.tpl` — список задач
- `tpl/view.tpl` — задача + контакты/скрытие + отклики + форма отклика
- `tpl/new.tpl` — форма создания задачи
- `tpl/buy.tpl` — выбор тарифа и кнопка оплаты
- `tpl/my.tpl` — список моих задач

## Поведение доступа

- Контакты задачи видны автору и пользователям с активным доступом (`freelance_user_access.access_until > time()`).
- При успешном уведомлении Robokassa `result` доступ продлевается на 10/30 дней от большей из дат (текущая/текущий доступ).

## Заметки по безопасности

- Подпись Robokassa: формируется и валидируется с использованием `Password1/Password2`.
- Вставки пользователя экранируются при выводе, описание задачи допускает HTML (`|raw`) — убедитесь, что доверяете источнику или применяете фильтр/санитизацию при сохранении.

## Дальнейшие улучшения (roadmap)

- UI откликов в «Мои заявки», уведомления автору о новых откликах.
- Выбор исполнителя, завершение задачи и рейтинг.
- Пагинация и фильтры списков (категории/города при необходимости).
- Дополнительные платёжные провайдеры (YooKassa/UnitPay/CloudPayments).

## Поддержка

Вопросы и предложения — через issues вашего репозитория или канал связи проекта NGCMS.
