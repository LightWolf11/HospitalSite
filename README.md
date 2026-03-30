# HospitalSite

Сайт больницы (конкурс **«ПрофМастерство»**). Приложение на PHP с БД (PDO) и динамической загрузкой услуг/врачей.

## Что нужно для запуска

1. PHP 8+.
2. MySQL / MariaDB.
3. Web-сервер (Apache/Nginx) или встроенный PHP-server.
4. Папка для загрузок: `uploads/` (должна существовать и быть доступной для записи).

## Настройка подключения к БД

1. Откройте `config/config.connection.php` и задайте:
   - `db.host`
   - `db.name`
   - `db.user`
   - `db.pass`
   - `db.charset` (обычно `utf8mb4`)

`includes/bootstrap.php` требует `config/config.php`.
В проекте он уже есть и собирает конфигурацию из `config.connection.php` (и, если доступен, из `config.mail.php`).

## Настройка почты (для уведомлений)

Уведомления отправляются через `config/config.mail.php` (SMTP).
Если файл не настроен/отсутствует — сайт в целом будет работать, но письма могут не уходить.

## Запуск (dev)

1. Поднимите сервер (из корня проекта):
   - `php -S localhost:8000`
2. Откройте в браузере:
   - `http://localhost:8000/index.php`
3. При первом запуске приложение создаст/обновит схему БД через `includes/db_schema_ensure.php`.

## Админ-панель (важно)

Доступ в админку включается полем `users.is_admin` (или ролью `users.role='admin'`).

Чтобы первый раз зайти в админку, включите суперадмина в вашей БД:

```sql
UPDATE users
SET is_admin = 1
WHERE email = 'admin@hospital.local';
```

Дальше управление пользователями/врачами — в:
- `admin/users.php`
- `admin/doctors.php`
- `admin/services.php`

## Страницы

- Главная: `index.php`
- Услуги: `pages/services.php`
- Кабинет пациента: `cabinet/`
- Кабинет врача: `doctor/`

