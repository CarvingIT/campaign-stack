# Repository Map: Campaign Stack (campaign-stack001)

## Overview
**Campaign Stack** is a Laravel 13 application for managing email marketing campaigns, contact segmentation, newsletter templates, outbound mail server rotation, and scheduled email queue dispatching.

## Tech Stack
- **Framework**: Laravel 13 (PHP 8.3+)
- **Frontend**: Blade, Vite 8, Tailwind CSS 3/4, Alpine.js, TinyMCE, FontAwesome 7
- **Database**: SQLite (default) / MySQL / PostgreSQL
- **Key Packages**:
  - `carving-i-t/laravel-user-roles`: User role management
  - `alexusmai/laravel-file-manager`: Media/file management
  - `laravel/breeze`: Authentication

## Key Application Architecture

### Models (`app/Models/`)
- `User.php`: User authentication & roles
- `OutboundMailAccount.php`: SMTP server configurations
- `Contact.php`, `Tag.php`, `ContactTag.php`: Subscriber contact directory and tag-based segmentation
- `Newsletter.php`, `NewsletterTag.php`, `NewsletterOutboundMailAccount.php`: Email templates & design bindings
- `Campaign.php`: High-level marketing campaign definitions
- `MailQueue.php`: Pending outbound email queue items
- `SentMail.php`: Sent email delivery logs & tracking

### Controllers (`app/Http/Controllers/`)
- `DashboardController.php`: Overview stats & metrics
- `OutboundMailAccountController.php`: Add/edit/delete outbound mail servers
- `ContactController.php`: Add/edit/delete/import contacts (CSV)
- `TagController.php`: Tag management
- `NewsletterController.php`: Newsletter content & TinyMCE editor handling
- `CampaignController.php`: Campaign lifecycle
- `EmailController.php`: Sent email logs & status monitoring

### Background & Console Commands (`app/Console/Commands/`)
- `QueueMails.php` (`php artisan queue:mails`): Processes campaigns and generates mail queue entries for target contacts.
- `FlushMailQueue.php` (`php artisan flush:mailqueue`): Dispatches queued emails via assigned outbound mail accounts and records sent logs.

### Key Routes (`routes/web.php`)
- Auth required: `/dashboard`, `/mail-accounts`, `/account-form/{id}`, `/tags`, `/tag-form/{id}`, `/contacts`, `/contact-form/{id}`, `/import-contact-form`, `/campaigns`, `/campaign-form/{id}`, `/newsletters`, `/newsletter-form/{id}`
- Public/Protected logs: `/emails`, `/emails/data`

## Configuration Files
- `.env.example`: DB (SQLite default), queue driver, mail credentials
- `composer.json`: Dependency specification & custom composer scripts (`setup`, `dev`, `test`)
- `package.json`: Vite & asset dependencies

## Default Credentials (Seeded via `php artisan db:seed`)
- **Admin**: `campaign-stack@carvingit.com` / `CampaignStack!@#`
- **Staff User**: `staff@campaign-stack.com` / `CampaignStack!@#`

