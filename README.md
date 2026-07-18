# ForgeDesk Studio

A self-hosted, open-source business management system for freelance web developers. Manage clients, projects, tasks, time tracking, files, and messages in one place. Built with Arabic and Hebrew support and full RTL layout.

Built using vibe coding — AI-powered development from idea to production.

---

## Features

- Client management (CRUD, activation/deactivation, per-client project overview)
- Project management (statuses, priorities, budgets, hourly rates, estimated totals)
- Task tracking with statuses, priorities, due dates, and image attachments
- Time tracker with live timer, manual entry, CSV and PDF exports
- Built-in messaging between admin and clients per project
- File uploads with signed secure downloads and image previews
- Dark mode toggle
- Fully responsive mobile-first design
- Arabic and Hebrew translations (369+ keys per language)
- Role-based access control (Admin and Client), authorization policies, rate limiting, signed URLs

---

## Quick Start

```bash
git clone https://github.com/NosaibaWebDev/forgedesk.git
cd forgedesk

composer install
cp .env.example .env
php artisan key:generate

# Edit .env with your database credentials
php artisan migrate
php artisan db:seed

# For production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Requirements:** PHP 8.2 or later, Composer, MySQL 8+ or SQLite

## Default Credentials

After running `php artisan db:seed`, you can log in with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@forgedesk.dev | admin123456 |
| Client | client@forgedesk.dev | client123456 |

**Change these immediately after first login.**

---

## Built With

- Laravel 12
- Tailwind CSS
- Alpine.js
- Lucide Icons
- SQLite / MySQL

---

## License

This project is open source. You are free to use, modify, and distribute it however you wish. No restrictions, no obligations.

---

**https://nosaiba.com**

---

> **Note:** This README was written by AI. The entire project, from architecture and UI to translations and security hardening, was built using vibe coding — iterating with AI to design, code, audit, and refine every detail.
