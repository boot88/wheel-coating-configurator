# Wheel Coating Configurator — подбор покрытия дисков

Сайт услуг полимерной покраски дисков на Laravel: визуальный подбор размера и покрытия, ориентировочная стоимость, галерея работ и форма обращения.

## Возможности основной ветки

- Три размера дисков: R15, R17 и R19.
- Шесть вариантов покрытия с локальными изображениями.
- Переключение превью и ориентировочной стоимости без перезагрузки.
- Увеличение изображений и галерея работ.
- Адаптивная страница, мобильное меню, карта и контакты.
- Форма заявки с валидацией, AJAX-ответом и почтовой отправкой.

В основной ветке `master` разрешены конфликты слияния в PHP, маршрутах, CSS и Blade-шаблонах. Дальнейшие изменения интерфейса и доставки заявок находятся в отдельной ветке [codex/audit-and-hardening](https://github.com/boot88/wheel-coating-configurator/tree/codex/audit-and-hardening); её возможности не следует смешивать с основной версией.

## Технологии

PHP 8.2+, Laravel 12, Blade, JavaScript, CSS и почтовые средства Laravel. Базовый макет использует Tailwind через CDN; в репозитории также настроена сборка ассетов через Vite.

## Навигация по коду

| Задача | Файл |
| --- | --- |
| Размеры, покрытия и цены для страницы | [HomeController](app/Http/Controllers/HomeController.php) |
| Валидация заявки и передача в почту | [LeadController](app/Http/Controllers/LeadController.php) |
| Интерфейс конфигуратора | [Шаблон главной страницы](resources/views/home.blade.php) |
| Макет и мобильное меню | [Основной шаблон](resources/views/layouts/app.blade.php) |
| Маршруты | [routes/web.php](routes/web.php) |
| Проверки страницы и формы | [WheelConfiguratorTest](tests/Feature/WheelConfiguratorTest.php) |

## Локальный запуск

```bash
git clone https://github.com/boot88/wheel-coating-configurator.git
cd wheel-coating-configurator
composer install
cp .env.example .env
php artisan key:generate
```

Настройте отдельную локальную базу в `.env`. При использовании SQLite создайте `database/database.sqlite`.

```bash
php artisan migrate
php artisan serve
```

Страница: `http://127.0.0.1:8000`.

Для сборки ассетов при необходимости:

```bash
npm ci
npm run build
```

Для настоящей доставки заявок настройте SMTP в `MAIL_*`; текущий получатель письма задаётся в `LeadController`. При `MAIL_MAILER=log` письмо записывается в лог вместо отправки.

## Проверки

```bash
php artisan test
npm run build
```

Функциональные проверки охватывают рендер страницы, наличие изображений всех вариантов, валидацию телефона и передачу заявки почтовому обработчику. В тестах почта подменяется, настоящие письма не отправляются.
