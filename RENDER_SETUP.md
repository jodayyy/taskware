# Render Deployment Setup - Quick Reference

## Files Created for Deployment

### Core Configuration
- ✅ `render.yaml` - Infrastructure as Code configuration
- ✅ `Dockerfile` - Container definition
- ✅ `nginx.conf` - Web server configuration
- ✅ `build.sh` - Build process script
- ✅ `start.sh` - Application startup script

### Supporting Files
- ✅ `.dockerignore` - Files to exclude from Docker build
- ✅ `.renderignore` - Files to exclude from Render deployment
- ✅ `DEPLOYMENT.md` - Comprehensive deployment guide
- ✅ `render-test.sh` - Local testing script

## What Was Modified for Production Compatibility

### Database Configuration (`config/database.php`)
- ✅ Added `guest_pgsql` connection for production guest data
- ✅ Updated PostgreSQL configuration to use `DATABASE_URL`
- ✅ Added `sslmode` configuration for secure connections

### Models (Guest System)
Updated to use configurable database connection:
- ✅ `app/Models/GuestUser.php`
- ✅ `app/Models/GuestTask.php`
- ✅ `app/Models/GuestProject.php`

### Migrations (Guest Tables)
Updated to support both SQLite (dev) and PostgreSQL (prod):
- ✅ `database/migrations/2025_11_04_000000_create_guest_users_table.php`
- ✅ `database/migrations/2025_11_04_000100_create_guest_tasks_table.php`
- ✅ `database/migrations/2025_11_05_171117_create_guest_projects_table.php`
- ✅ `database/migrations/2025_11_05_172558_add_project_id_to_guest_tasks_table.php`

### Commands
- ✅ `app/Console/Commands/CleanupOldGuestData.php` - Updated for configurable connection

### Documentation
- ✅ `README.md` - Added Render deployment section

## Environment Variables Required

### Must Set Manually
```bash
APP_KEY=base64:...  # Generate with: php artisan key:generate --show
```

### Auto-configured by render.yaml
```bash
# Application
APP_NAME=Taskware
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.onrender.com

# Database (Main)
DB_CONNECTION=pgsql
DB_HOST=<auto-filled>
DB_PORT=<auto-filled>
DB_DATABASE=<auto-filled>
DB_USERNAME=<auto-filled>
DB_PASSWORD=<auto-filled>

# Database (Guest)
GUEST_DB_CONNECTION=guest_pgsql
GUEST_DB_HOST=<auto-filled>
GUEST_DB_PORT=<auto-filled>
GUEST_DB_DATABASE=<auto-filled>
GUEST_DB_USERNAME=<auto-filled>
GUEST_DB_PASSWORD=<auto-filled>

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Logging
LOG_CHANNEL=stderr
LOG_LEVEL=info
LOG_STDERR_FORMATTER=Monolog\Formatter\JsonFormatter
```

## Services Created

### 1. Web Service (`taskware`)
- **Type**: Docker container
- **Port**: 8080
- **Runtime**: PHP 8.2 + Nginx
- **Auto-deploy**: Enabled on Git push
- **Health check**: Enabled on `/`
- **Region**: Singapore

### 2. Database (`taskware-db`)
- **Type**: PostgreSQL
- **Plan**: Free tier
- **Region**: Singapore
- **Used by**: Both main and guest data

### 3. Scheduled Tasks
- **Setup Required**: See [SCHEDULED_TASKS.md](SCHEDULED_TASKS.md)
- **Purpose**: Cleanup old guest data (30+ days)
- **Options**: External cron service (free) or background worker (paid)

## How It Works

### Build Process (build.sh)
1. Install PHP dependencies (Composer)
2. Install Node dependencies (NPM)
3. Build frontend assets (Vite)
4. Set up storage directories
5. Run database migrations (main + guest)
6. Cache configuration/routes/views

### Startup Process (start.sh)
1. Run any pending migrations
2. Clear and rebuild caches
3. Start PHP-FPM (background)
4. Start Nginx (foreground)

### Database Strategy
- **Local Development**: Uses SQLite for both main and guest DBs
- **Production (Render)**: Uses PostgreSQL for both
  - Main data: Regular tables
  - Guest data: Tables with `guest_` prefix

## Testing Deployment Locally

### Using Docker
```bash
# Build and run the container
./render-test.sh

# Or manually:
docker build -t taskware .
docker run -p 8080:8080 \
  -e APP_KEY=base64:$(openssl rand -base64 32) \
  taskware
```

### Access
- Application: http://localhost:8080
- View logs: `docker logs -f <container-id>`

## Deployment Checklist

Before deploying to Render:

- [ ] All changes committed to Git repository
- [ ] Repository connected to Render
- [ ] `APP_KEY` generated and ready to paste
- [ ] Review `render.yaml` configuration
- [ ] Check all migrations are up to date
- [ ] Verify `.env.example` is not committed
- [ ] Test build locally with `./render-test.sh` (optional)

## Post-Deployment Tasks

After first deployment:

1. **Set APP_KEY**
   - Generate locally: `php artisan key:generate --show`
   - Add to Render environment variables

2. **Verify Services**
   - Check web service is running
   - Check database is connected
   - Check cron job is scheduled

3. **Test Application**
   - Access the URL provided by Render
   - Test user registration/login
   - Test task creation
   - Test guest mode

4. **Monitor Logs**
   - Watch for any errors in deployment logs
   - Check application logs for runtime errors

## Common Issues & Solutions

### Build Fails
- **Issue**: Composer dependencies fail
- **Solution**: Check PHP version in Dockerfile matches requirements

### Database Connection Error
- **Issue**: Cannot connect to database
- **Solution**: Verify environment variables are linked correctly in render.yaml

### Assets Not Loading
- **Issue**: CSS/JS files not found
- **Solution**: Ensure `npm run build` completed successfully in build.sh

### Guest Data Not Persisting
- **Issue**: Guest data disappears
- **Solution**: Verify `GUEST_DB_CONNECTION=guest_pgsql` is set

## Maintenance

### Viewing Logs
```bash
# In Render Dashboard:
Services → taskware → Logs

# Filter by:
- Time range
- Log level (info, error, etc.)
```

### Running Commands
```bash
# In Render Dashboard:
Services → taskware → Shell

# Then run:
php artisan migrate --force
php artisan cache:clear
php artisan queue:work
```

### Database Backup
```bash
# Render provides automated backups on paid plans
# For free tier, use manual backup:

# In Render Dashboard:
Databases → taskware-db → Backups → Create Backup
```

## Updating Application

1. **Make changes locally**
2. **Test changes**
3. **Commit to Git**
   ```bash
   git add .
   git commit -m "Your changes"
   git push
   ```
4. **Render auto-deploys** (takes 5-10 minutes)
5. **Verify deployment** in Render dashboard

## Architecture Diagram

```
┌─────────────────────────────────────────────────┐
│                   Internet                       │
└─────────────────┬───────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────┐
│         Render Load Balancer (HTTPS)            │
└─────────────────┬───────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────┐
│  Web Service (taskware) - Singapore             │
│  ┌───────────────────────────────────────────┐  │
│  │  Nginx :8080                              │  │
│  │    ↓                                      │  │
│  │  PHP-FPM                                  │  │
│  │    ↓                                      │  │
│  │  Laravel Application                      │  │
│  └───────────────────────────────────────────┘  │
└─────────────────┬───────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────────────┐
│  PostgreSQL Database (taskware-db) - Singapore  │
│  ┌───────────────────────────────────────────┐  │
│  │  Main Tables:                             │  │
│  │  - users, sessions, tasks, projects       │  │
│  │                                           │  │
│  │  Guest Tables (guest_* prefix):          │  │
│  │  - guest_users, guest_tasks               │  │
│  │  - guest_projects                         │  │
│  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────┘
                  ▲
                  │
┌─────────────────┴───────────────────────────────┐
│  External Cron Service (Optional)               │
│  Triggers: /cron/run endpoint hourly            │
│  - Cleanup old guest data                       │
│  See: SCHEDULED_TASKS.md                        │
└─────────────────────────────────────────────────┘
```

## Cost Breakdown (Free Tier)

| Service | Free Tier Limits | Cost |
|---------|------------------|------|
| Web Service | 750 hrs/month, sleeps after 15min idle | $0 |
| PostgreSQL | 1GB storage, 90-day retention | $0 |
| External Cron | Free tier on various services | $0 |
| Bandwidth | 100GB/month | $0 |
| **Total** | | **$0/month** |

## Next Steps

1. ✅ All deployment files are ready
2. 📤 Push code to Git repository
3. 🔗 Connect repository to Render
4. 🚀 Deploy using render.yaml blueprint
5. 🔑 Set APP_KEY environment variable
6. ⏰ Set up scheduled tasks (see [SCHEDULED_TASKS.md](SCHEDULED_TASKS.md))
7. 🎉 Access your deployed application!

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md)

