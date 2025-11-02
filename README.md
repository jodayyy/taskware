# Taskware

Taskware is a modern task management application built with Laravel 11 to help you organize and track your tasks efficiently.

## Features

- User Authentication
- Task Management
- Dashboard Overview
- User Profiles
- Responsive Design

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url> taskware
   cd taskware
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Set up the environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure the database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=taskware
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Build frontend assets:
   ```bash
   npm run build
   ```

7. Start the server:
   ```bash
   php artisan serve
   ```

   Visit `http://localhost:8000`.

## Development

- **Code Formatting**: Use Laravel Pint:
  ```bash
  ./vendor/bin/pint
  ```
- **Testing**: Run tests:
  ```bash
  php artisan test
  ```

## Deployment

1. Install production dependencies:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci --production
   npm run build
   ```

2. Optimize the application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Set permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

## License

This project is licensed under the [MIT license](LICENSE).