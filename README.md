# JobYaari Blog Management System

A responsive Laravel blog system with dynamic blog listing, AJAX + jQuery filtering, and a simple admin panel for CRUD operations.

## Features
- Public blog listing page with data fetched from the database
- AJAX + jQuery category/date filtering without page reload
- Full blog detail page
- Simple admin login with session-based protection
- Admin CRUD for adding, editing, and deleting blogs

## Admin Access
- URL: http://127.0.0.1:8000/admin/login
- Email: admin@jobyaari.dev
- Password: admin12345

## Tech Stack
- PHP / Laravel
- SQLite for local development
- Blade templates and responsive CSS
- jQuery + AJAX for live filtering

## Setup
1. composer install
2. php artisan key:generate
3. php artisan migrate --seed
4. php artisan serve

## Live Preview
- Public site: http://127.0.0.1:8000/
- Admin panel: http://127.0.0.1:8000/admin/login

## Verification
- php artisan test
