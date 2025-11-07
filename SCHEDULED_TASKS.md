# Setting Up Scheduled Tasks on Render

Since Render's free tier and YAML blueprint don't support dedicated cron jobs, here are alternative methods to run Laravel's scheduled tasks (like guest data cleanup).

## Option 1: External Cron Service (Recommended for Free Tier)

Use a free external service to trigger your scheduler endpoint.

### Step 1: Create a Scheduler Endpoint

Create a new route in `routes/web.php`:

```php
// Add this to routes/web.php
Route::get('/cron/run', function () {
    // Verify the request is from your cron service (optional but recommended)
    $token = request()->query('token');
    if ($token !== env('CRON_TOKEN')) {
        abort(403, 'Unauthorized');
    }
    
    Artisan::call('schedule:run');
    
    return response()->json([
        'status' => 'success',
        'message' => 'Scheduler executed successfully',
        'time' => now()->toDateTimeString()
    ]);
})->middleware('throttle:5,1'); // Limit to 5 requests per minute
```

### Step 2: Generate a Cron Token

In your Render environment variables, add:
```
CRON_TOKEN=your_random_secure_token_here
```

Generate a secure token with:
```bash
openssl rand -base64 32
```

### Step 3: Set Up External Cron Service

Use one of these free services:

#### A. **EasyCron** (Recommended)
1. Sign up at [easycron.com](https://www.easycron.com)
2. Create a new cron job
3. URL: `https://your-app.onrender.com/cron/run?token=your_cron_token`
4. Schedule: `0 */1 * * *` (every hour)
5. Email notifications: Enable for failures

#### B. **Cron-Job.org**
1. Sign up at [cron-job.org](https://cron-job.org)
2. Create new cron job
3. URL: `https://your-app.onrender.com/cron/run?token=your_cron_token`
4. Schedule: Every 60 minutes

#### C. **UptimeRobot**
1. Sign up at [uptimerobot.com](https://uptimerobot.com)
2. Add new monitor (HTTP(s) type)
3. URL: `https://your-app.onrender.com/cron/run?token=your_cron_token`
4. Monitoring Interval: 60 minutes
5. This also serves as uptime monitoring!

## Option 2: Render Background Worker (Paid Plans)

If you upgrade to a paid plan, you can add a background worker.

### Create a Background Worker

Add to `render.yaml`:

```yaml
services:
  - type: worker
    name: taskware-scheduler
    runtime: docker
    plan: starter
    region: singapore
    dockerfilePath: ./Dockerfile
    dockerContext: .
    dockerCommand: php artisan schedule:work
    envVars:
      # Same environment variables as web service
      - key: APP_NAME
        value: Taskware
      # ... (copy all env vars from web service)
```

The `schedule:work` command runs the scheduler every minute automatically.

## Option 3: Manual Cron Job Setup in Shell

For paid plans with shell access:

1. Go to your web service in Render Dashboard
2. Click **Shell**
3. Add to crontab:
```bash
# This won't work on Render as containers are ephemeral
# Not recommended
```

## Option 4: Queue-Based Scheduler (Advanced)

Use Laravel's queue system to schedule tasks.

### Step 1: Create a Recurring Job

Create `app/Console/Commands/SchedulerDaemon.php`:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SchedulerDaemon extends Command
{
    protected $signature = 'schedule:daemon';
    protected $description = 'Run the scheduler as a daemon';

    public function handle(): int
    {
        while (true) {
            $this->call('schedule:run');
            
            // Wait for 1 hour
            sleep(3600);
        }
        
        return 0;
    }
}
```

### Step 2: Run as Background Worker

Add to `render.yaml`:

```yaml
services:
  - type: worker
    name: taskware-scheduler
    runtime: docker
    plan: starter
    region: singapore
    dockerfilePath: ./Dockerfile
    dockerContext: .
    dockerCommand: php artisan schedule:daemon
```

## Verification

### Test Your Scheduler Endpoint

```bash
# Test locally
curl "http://localhost:8000/cron/run?token=your_token"

# Test on Render (after deployment)
curl "https://your-app.onrender.com/cron/run?token=your_token"
```

Expected response:
```json
{
    "status": "success",
    "message": "Scheduler executed successfully",
    "time": "2025-11-07 12:00:00"
}
```

### Check Laravel Logs

After triggering the scheduler, check logs to verify execution:

1. Go to Render Dashboard
2. Click your web service
3. View Logs
4. Look for: `Running scheduled command: Closure`

## Security Considerations

### 1. Token-Based Authentication
Always use a strong, random token for your cron endpoint:
```bash
CRON_TOKEN=$(openssl rand -base64 32)
```

### 2. IP Whitelisting (Optional)
Add IP restriction in your route:

```php
Route::get('/cron/run', function () {
    $allowedIps = explode(',', env('CRON_ALLOWED_IPS', ''));
    if (!in_array(request()->ip(), $allowedIps)) {
        abort(403);
    }
    // ... rest of code
});
```

### 3. Rate Limiting
The route already includes throttling middleware to prevent abuse.

### 4. HTTPS Only
Render enforces HTTPS, so your cron token is always encrypted in transit.

## Monitoring

### Check Scheduler Execution

View when tasks last ran:

```bash
# In Render Shell or locally
php artisan schedule:list
```

### Set Up Alerts

Configure the external cron service to email you if:
- Endpoint returns an error
- Endpoint times out
- Endpoint is unreachable

## Cost Comparison

| Method | Cost | Pros | Cons |
|--------|------|------|------|
| External Cron | Free | Simple, no extra cost | Requires external service |
| Background Worker | $7/month | Native to Render, reliable | Additional cost |
| Paid plan + Shell | Included | Full control | Need paid plan anyway |

## Recommended Setup

**For Free Tier Users:**
- Use **Option 1** (External Cron Service)
- Specifically: **EasyCron** or **UptimeRobot**
- UptimeRobot bonus: Also monitors uptime and prevents sleep

**For Paid Plan Users:**
- Use **Option 2** (Background Worker)
- Most reliable and native solution

## Implementation Steps

### Quick Setup (5 minutes)

1. **Add the cron endpoint to routes/web.php**
   ```php
   Route::get('/cron/run', function () {
       if (request()->query('token') !== env('CRON_TOKEN')) {
           abort(403);
       }
       Artisan::call('schedule:run');
       return response()->json(['status' => 'success']);
   })->middleware('throttle:5,1');
   ```

2. **Generate and set CRON_TOKEN in Render**
   ```bash
   openssl rand -base64 32
   ```
   Add to Render Environment Variables

3. **Deploy the changes**
   ```bash
   git add .
   git commit -m "Add cron endpoint"
   git push
   ```

4. **Set up EasyCron or UptimeRobot**
   - URL: `https://your-app.onrender.com/cron/run?token=YOUR_TOKEN`
   - Schedule: Every 60 minutes

5. **Test it**
   ```bash
   curl "https://your-app.onrender.com/cron/run?token=YOUR_TOKEN"
   ```

Done! Your scheduled tasks will now run hourly. 🎉

## Troubleshooting

### Endpoint Returns 403
- Check CRON_TOKEN is set correctly in Render
- Verify token in URL matches environment variable

### Endpoint Returns 429 (Too Many Requests)
- Rate limit hit
- Wait 1 minute and try again
- Check cron service isn't hitting too frequently

### Tasks Not Running
1. Check Laravel logs in Render Dashboard
2. Verify schedule is defined in `app/Console/Kernel.php`
3. Test manually: `php artisan guest:cleanup --days=30`

### External Service Can't Reach Endpoint
- Verify app is deployed and running
- Check URL is correct
- Ensure Render service is not sleeping (upgrade or use UptimeRobot)

## Alternative: Prevent Sleep Without Cron

If you just want to prevent your app from sleeping:

**Use UptimeRobot:**
1. Monitor your homepage every 5 minutes
2. App stays awake
3. Bonus: Uptime monitoring
4. Free tier: 50 monitors

Then manually trigger cleanup occasionally, or upgrade to a paid plan for the background worker.

---

**Recommended Solution**: External Cron (Option 1) with UptimeRobot for free tier users.

