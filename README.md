# lrvl2

Практический проект на Laravel для освоения фреймворка и подготовки к поиску работы.

## О проекте

Второй учебный проект на Laravel.  
Цель — глубже разобраться в современных практиках Laravel, работать с популярными пакетами и создать структуру, близкую к реальным проектам.

Первый проект (без Filament) — [lrvl1](https://github.com/SY-Soft/lrvl1).

## Использованные технологии

### Основное
- **Laravel 13.8**
- **PHP 8.3**
- **MySQL** (миграции + Eloquent)

### Админка и UI
- **FilamentPHP 4** — основная админ-панель
- **Tailwind CSS 4** + Vite
- **Bootstrap 5** + Bootstrap Icons (частично)

### Пакеты
- **spatie/laravel-permission** — управление ролями и правами
- **spatie/laravel-activitylog** — логирование действий пользователей
- **barryvdh/laravel-debugbar** — отладка
- **laravel/pint** — кодстайл

### Инструменты разработки
- **Docker** (работаю в контейнере из предыдущего репозитория [practice_docker](https://github.com/SY-Soft/practice_docker))
- **Vite** + Laravel Vite Plugin
- **Concurrently** — запуск dev-сервера + очередь + vite одновременно

## Что реализовано / изучается

- Полноценная установка Filament 4
- Настройка ролей и разрешений через Spatie Permission
- Activity Log (история изменений)
- Базовая структура моделей, ресурсов Filament, страниц
- Миграции, сиды, фабрики
- Настройка dev-окружения (одновременный запуск сервера, очереди, логов)

## Запуск проекта

```bash
composer install
cp .env.example .env
php artisan key:generate

# Или через скрипт
composer run setup
