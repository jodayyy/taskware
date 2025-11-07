# Taskware Deployment Guide for Render

This guide walks you through deploying Taskware to Render.com, a cloud platform that makes it easy to deploy web applications.

## Prerequisites

- A [Render.com](https://render.com) account (free tier available)
- Your Taskware code in a Git repository (GitHub, GitLab, or Bitbucket)
- Basic knowledge of environment variables

## Architecture Overview

The deployment consists of:
1. **Web Service** - Runs the Laravel application using Docker
2. **PostgreSQL Database** - Main database for users, tasks, and projects

**Note:** Scheduled tasks (like guest data cleanup) need to be set up separately. See [SCHEDULED_TASKS.md](SCHEDULED_TASKS.md) for instructions.

## Quick Deploy

### Step 1: Prepare Your Repository

Ensure your repository includes these deployment files:
- `render.yaml` - Infrastructure configuration
- `Dockerfile` - Container configuration
- `nginx.conf` - Web server configuration
- `build.sh` - Build script
- `start.sh` - Startup script

### Step 2: Connect to Render

1. Log in to [Render Dashboard](https://dashboard.render.com)
2. Click **"New +"** button
3. Select **"Blueprint"**
4. Connect your Git repository
5. Select the repository containing Taskware

### Step 3: Configure Environment

Render will automatically detect the `render.yaml` file and create:
- Web service named `taskware`
- PostgreSQL database named `taskware-db`

**IMPORTANT**: You need to set the `APP_KEY` environment variable:

1. Generate a key locally:
   ```bash
   php artisan key:generate --show
   ```

2. Copy the output (e.g., `base64:...`)

3. In Render Dashboard:
   - Go to your web service
   - Click **Environment**
   - Find `APP_KEY` 
   - Paste your generated key
   - Click **Save Changes**

### Step 4: Deploy

1. Click **"Apply"** to create all services
2. Render will:
   - Create the PostgreSQL database
   - Build the Docker container
   - Run database migrations
   - Deploy the application
3. Wait for the build to complete (first build takes 5-10 minutes)

### Step 5: Access Your Application

Once deployed, Render provides a URL like: `https://taskware-xxxx.onrender.com`

## Configuration Details

### Environment Variables

The following environment variables are automatically configured via `render.yaml`:

#### Application
- `APP_NAME` - Application name
- `APP_ENV` - Environment (production)
- `APP_DEBUG` - Debug mode (false)
- `APP_KEY` - **MUST BE SET MANUALLY**

#### Database (Auto-configured)
- `DB_CONNECTION` - Database type (pgsql)
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

#### Guest Database (Auto-configured)
- `GUEST_DB_CONNECTION` - Guest database connection (guest_pgsql)
- Uses the same PostgreSQL database with `guest_` prefix for tables

#### Cache & Session
- `CACHE_STORE` - Database-backed cache
- `SESSION_DRIVER` - Database-backed sessions
- `QUEUE_CONNECTION` - Database-backed queues

#### Logging
- `LOG_CHANNEL` - stderr for Render logs
- `LOG_LEVEL` - info

### Database Configuration

Taskware uses two database configurations:

1. **Main Database (PostgreSQL)**
   - Stores users, tasks, and projects for authenticated users
   - Automatically provisioned by Render

2. **Guest Database (PostgreSQL)**
   - Stores guest user data
   - Uses the same PostgreSQL database with table prefixes
   - Data older than 30 days is automatically cleaned up

## Scheduled Tasks

Your Laravel application includes scheduled tasks (like guest data cleanup). Since Render's free tier doesn't support cron jobs in the YAML blueprint, you'll need to set these up separately.

**See [SCHEDULED_TASKS.md](SCHEDULED_TASKS.md) for complete instructions on:**
- Using free external cron services (recommended for free tier)
- Setting up background workers (paid plans)
- Preventing app sleep while running scheduled tasks

## Manual Configuration (Optional)

### Custom Domain

1. Go to your web service in Render Dashboard
2. Click **Settings** > **Custom Domain**
3. Follow the instructions to add your domain
4. Update DNS records as shown

### Additional Environment Variables

Add more environment variables as needed:

1. Go to web service settings
2. Click **Environment**
3. Add new variables:
   - `MAIL_MAILER`, `MAIL_HOST`, etc. for email
   - `AWS_*` for file storage
   - Custom application settings

### Scaling

Free tier limitations:
- Web service sleeps after 15 minutes of inactivity
- 750 hours/month of runtime
- PostgreSQL: 90 days data retention, 1GB storage

To upgrade:
1. Go to your service settings
2. Select a paid plan for always-on service and more resources

## Troubleshooting

### Build Fails

**Check build logs:**
1. Go to your web service
2. Click **Logs**
3. Look for error messages in the build output

**Common issues:**
- Missing `APP_KEY`: Generate and set it manually
- Composer dependencies: Check `composer.json` for incompatibilities
- NPM build errors: Check `package.json` and Node version

### Database Connection Errors

1. Verify database is running:
   - Go to **Databases** in Render Dashboard
   - Check status

2. Check environment variables:
   - Ensure `DB_*` variables are correctly linked

3. View database logs for connection attempts

### Application Errors

1. **Enable debug mode temporarily:**
   - Set `APP_DEBUG=true` in environment
   - **Remember to disable after debugging**

2. **Check application logs:**
   - Go to web service > Logs
   - Filter by time range

3. **Common issues:**
   - 500 errors: Check PHP errors in logs
   - 404 errors: May be routing issues
   - Database errors: Check migrations ran successfully

### Migration Issues

Migrations run automatically during build. To manually run:

1. Go to your web service
2. Click **Shell**
3. Run:
   ```bash
   php artisan migrate --force
   php artisan migrate --database=guest_pgsql --force
   ```

## Updating Your Application

Render auto-deploys when you push to your repository:

1. Make changes locally
2. Test thoroughly
3. Commit and push to Git
4. Render automatically:
   - Detects changes
   - Builds new container
   - Runs migrations
   - Deploys with zero downtime

### Manual Deploy

To manually trigger a deploy:
1. Go to web service
2. Click **Manual Deploy**
3. Select **Deploy latest commit**

## Performance Optimization

### Laravel Optimization

The deployment automatically runs:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Optimization

- Use database indexes (already configured)
- Monitor slow queries in PostgreSQL logs
- Consider upgrading database plan for more resources

### Caching

Configure Redis (optional):
1. Add Redis service in Render
2. Update environment variables:
   - `CACHE_STORE=redis`
   - `REDIS_HOST`, `REDIS_PASSWORD`

## Security Best Practices

1. **Never commit `.env` file**
2. **Use strong `APP_KEY`** (32+ characters)
3. **Keep dependencies updated**
4. **Use HTTPS only** (enforced by Render)
5. **Enable CSRF protection** (enabled by default)
6. **Regular backups** - Render provides automated backups on paid plans

## Monitoring

### Application Logs

View real-time logs:
1. Go to web service
2. Click **Logs**
3. Use filters to find specific issues

### Database Monitoring

Monitor database:
1. Go to **Databases**
2. Click your database
3. View metrics:
   - CPU usage
   - Memory usage
   - Disk usage
   - Connection count

### Uptime Monitoring

Render provides:
- Automatic health checks (configured in `render.yaml`)
- Uptime metrics in dashboard
- Email alerts for downtime (paid plans)

## Cost Estimation

### Free Tier
- Web service: Free (sleeps after inactivity)
- PostgreSQL: Free (90-day retention, 1GB)
- Cron: Free (750 hours/month shared)

**Total: $0/month** (with free tier limitations)

### Paid Plans
- **Starter** ($7/month): 400 build minutes, 400GB bandwidth
- **Standard** ($25/month): Always-on, auto-scaling
- **Pro** ($85/month): Priority support, advanced features

See [Render Pricing](https://render.com/pricing) for details.

## Support and Resources

- [Render Documentation](https://render.com/docs)
- [Laravel Documentation](https://laravel.com/docs)
- [Taskware Issues](https://github.com/your-repo/taskware/issues)

## Local Development vs Production

| Feature | Local (SQLite) | Production (Render) |
|---------|---------------|---------------------|
| Main DB | SQLite | PostgreSQL |
| Guest DB | SQLite | PostgreSQL (prefixed) |
| Cache | File | Database |
| Sessions | Database | Database |
| Queue | Sync | Database |
| Logs | File | Stderr (Render) |

The application automatically adapts based on environment variables.

## Rollback

If deployment fails or has issues:

1. **Rollback to previous deploy:**
   - Go to web service
   - Click **Events**
   - Find successful deploy
   - Click **Rollback to this deploy**

2. **Revert Git changes:**
   ```bash
   git revert HEAD
   git push
   ```
   Render will auto-deploy the reverted version.

## Advanced Configuration

### Multiple Environments

Create separate services for staging:
1. Duplicate `render.yaml` as `render.staging.yaml`
2. Update service names (e.g., `taskware-staging`)
3. Create separate Render blueprint

### Custom Build Steps

Edit `build.sh` to add custom steps:
```bash
# Example: Install additional dependencies
echo "==> Installing custom dependencies..."
composer require vendor/package
```

### Background Workers

To add queue workers:
1. Edit `render.yaml`
2. Add background worker service:
```yaml
- type: worker
  name: taskware-worker
  runtime: docker
  dockerfilePath: ./Dockerfile
  command: php artisan queue:work
```

---

**Congratulations!** Your Taskware application is now deployed on Render. 🎉

