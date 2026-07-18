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
php artisan app:create-admin

# For production
php artisan config:cache
php artisan route:cache
php artisan view:cache