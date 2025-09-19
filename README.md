# Laravel Blog Management Platform

A full-featured **Blog Management System** for authors built with **Laravel 8**, **Blade templates**, and **API endpoints**. Authors can create, edit, publish, soft-delete, restore, and view posts. Includes user authentication via **Sanctum**.

---

## Features

- User registration and login
- Create, edit, and delete posts
- Publish posts or save drafts
- Soft-delete posts and restore them later
- View trashed posts
- Dashboard with author statistics
- Responsive design using Bootstrap 5
- API endpoints for all post operations

---

## Tech Stack

- PHP 8+
- Laravel 8
- Blade Templates
- MySQL / MariaDB
- Bootstrap 5
- JWT for API authentication

---

## Prerequisites

- PHP >= 8.0
- Composer
- Node.js & npm
- MySQL or other supported database
- Git (optional, for cloning repository)

---

## Installation & Setup

1. **Clone the repository**

```bash
git clone https://github.com/yourusername/laravel-blog.git
cd laravel-blog
```

2. **Install PHP dependencies**

```bash
composer install
```
3. **Environment setup**

Copy .env.example to .env:

```bash
cp .env.example .env
```

4. **Generate app key and jwt secret key**

```bash
php artisan key:generate
php artisan jwt:secret
```

6. **Run database migration** 

```bash
php artisan migrate
```

7. **Serve the application**
```bash
php artisan serve
```


- Visit http://127.0.0.1:8000 in your browser.


# API Endpoints

| Method | Endpoint                       | Description                                  |
| ------ | ------------------------------ | -------------------------------------------- |
| POST   | `/api/auth/register`           | Register a new user                           |
| POST   | `/api/auth/login`              | Login a user                                  |
| GET    | `/api/auth/user`               | Get logged-in user info                       |
| POST   | `/api/posts`                   | Create a new post                             |
| GET    | `/api/posts`                   | List posts (use `?mine=true` to get user’s posts) |
| GET    | `/api/posts/{id}`              | View a single post                            |
| PUT    | `/api/posts/{id}`              | Update a post                                 |
| DELETE | `/api/posts/{id}`              | Soft-delete a post                            |
| PATCH  | `/api/posts/{id}/restore`      | Restore a soft-deleted post                   |
| DELETE | `/api/posts/{id}/force-delete` | Permanently delete a post                     |
| PATCH  | `/api/posts/{id}/publish`      | Publish a post                                |
| PATCH  | `/api/posts/{id}/unpublish`    | Unpublish a post                              |
| GET    | `/api/posts/trashed`           | List all trashed posts                        |
