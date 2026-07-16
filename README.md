# ForgeDesk Studio

Freelancer Web Developer Business Management System — Laravel 12, RTL/Hebrew-first.

## Quick Deploy to Cloud Host (VPS)

**Requirements:** Ubuntu 22.04/24.04, PHP 8.2+, Nginx, MySQL/PostgreSQL.

```bash
# On your VPS as root:
curl -O https://raw.githubusercontent.com/YOUR_USER/forgedesk/main/.deploy/setup.sh
bash setup.sh your-domain.com
```

Then edit `/var/www/forgedesk/.env` with your DB/MAIL credentials and run:

```bash
cd /var/www/forgedesk
php artisan migrate --force
php artisan app:create-admin
certbot --nginx -d your-domain.com
```

## Local Development

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed        # Creates demo data (admin: admin@forgedesk.dev)
php artisan serve
```

## Stack

- Laravel 12, PHP 8.2+
- SQLite (dev), MySQL/PostgreSQL (prod)
- Tailwind CSS (CDN), Alpine.js, Lucide Icons
- RTL layout, dark mode, mobile responsive
- Kanban board, time tracker, file uploads, CSV/PDF exports

## Commands

| Command | Purpose |
|---|---|
| `php artisan app:create-admin` | Create admin user (production) |
| `php artisan backup:database` | Backup DB + uploaded files |
| `php artisan test` | Run test suite (6 tests) |
| `php artisan optimize` | Build production caches |
