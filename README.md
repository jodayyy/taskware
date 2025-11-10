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

6. Run guest database migrations (defaults to SQLite for local development):
   ```bash
   php artisan migrate --database=${GUEST_DB_CONNECTION:-guest_sqlite}
   ```

7. Build frontend assets:
   ```bash
   npm run build
   ```

8. Start the server:
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

### Deploy to Render

Taskware includes configuration files for easy deployment to Render.

#### Quick Deploy

1. **Create a Render account** at [render.com](https://render.com)

2. **Create a new Web Service**:
   - Connect your GitHub/GitLab repository
   - Render will automatically detect the `render.yaml` configuration

3. **Create a PostgreSQL database**:
   - Name it `taskware-db` (or update `render.yaml` to match)
   - The database credentials will be automatically linked to your web service

4. **Set the APP_KEY environment variable**:
   - Generate a key locally: `php artisan key:generate --show`
   - Add it to your Render environment variables as `APP_KEY`

5. **Deploy**: Render will automatically build and deploy your application

#### Environment Variables

The following environment variables are automatically configured in `render.yaml`:
- `APP_NAME`, `APP_ENV`, `APP_DEBUG`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `CACHE_STORE`, `SESSION_DRIVER`, `QUEUE_CONNECTION`
- `LOG_CHANNEL`, `LOG_LEVEL`

#### Manual Deployment Steps

If you prefer manual deployment:

1. Install production dependencies:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm ci --omit=dev
   npm run build
   ```

2. Run migrations:
   ```bash
   php artisan migrate --force
   php artisan migrate --database=${GUEST_DB_CONNECTION:-guest_sqlite} --force
   ```

3. Optimize the application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Set permissions:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### Deployment Files

- `render.yaml` - Render infrastructure configuration
- `Dockerfile` - Container configuration
- `nginx.conf` - Nginx web server configuration
- `build.sh` - Build script for deployment
- `start.sh` - Application startup script
- `.renderignore` - Files to exclude from deployment
- `DEPLOYMENT.md` - Comprehensive deployment guide
- `RENDER_SETUP.md` - Quick reference for deployment setup

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md) or [RENDER_SETUP.md](RENDER_SETUP.md)

**Note:** For scheduled tasks (guest data cleanup), see [SCHEDULED_TASKS.md](SCHEDULED_TASKS.md) for setup instructions.

## License

This project is licensed under the [MIT license](LICENSE).