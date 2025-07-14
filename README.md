# Ticktrack

An application for track interactions and status of customer support requests built with Laravel 12.


# 💻 Tech Stack:
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white) ![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)

## Prequisites

- PHP >= 8.1
- Composer
- MySQL (any versions)
- Laravel 12


## Features

- **Multi Role Authentication**: Supports role-based access control (admin and user)
- **Bearer Token Authentication**: Uses bearer tokens for secure API authentication.
- **Ticket CRUD**: Create, view, and delete tickets using unique codes.
- **Ticket Replies**: Add replies to support tickets.
- **Ticket Dashboard**: Provides a centralized dashboard for managing submitted tickets.
- **Configuration**: Supports environment variables configuration via .env files.
  

## REST API Design

The project provides a RESTful API for user authentication and manage the tickets. The API follows standard REST conventions:

### 🔐 Authentication

- `POST /api/login` - Log in and retrieve access token
- `POST /api/register` - Register a new user
- `GET /api/me` - Get the currently authenticated user
- `POST /api/logout` - Log out and revoke token


### 🎫 Tickets

- `POST /api/ticket` - Create a new ticket
- `GET /api/tickets` - Get all submitted tickets by currently authenticated user
- `GET /api/ticket/:code` - Get a specific ticket by code
- `DELETE /api/ticket/:code` - Delete a ticket by code


### 💬 Ticket Replies

- `POST /api/ticket/:code/reply` - Post a reply to a specific ticket by code


### 📊 Dashboard

- `GET /api/dashboard` - Get overall ticket statistics


## Getting Started

1. Clone the repository:

```sh
git clone https://github.com/ahmadammarm/ticktrack-api.git
```

2. Navigate to the project directory:

```sh
cd ticktrack-api
```

3. Install the project dependencies:

```sh
composer install
```

4. Configure Environment Variable: Copy the file `.env.example` to `.env` and adjust it to your configuration:

```sh
cp .env.example .env
```

5. Generate application key and run migrations:

```sh
php artisan key:generate
php artisan migrate --seed
```


6. Run the project:

```sh
php artisan serve
```


The project will be available at:

`http://127.0.0.1:8000`


## Contribution
Contributions are welcome! Feel free to open issues or submit pull requests. For major changes, please open an issue first to discuss what you would like to change.

