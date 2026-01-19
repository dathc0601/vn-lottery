# Lottery Background Jobs & Scheduler Documentation

This document explains how the automated data fetching and statistics generation works in the VN Lottery application.

## Overview

The application uses **Laravel's Queue System** and **Task Scheduler** to automatically:
1. Fetch lottery results from the API after each draw time
2. Generate number frequency statistics weekly
3. Allow manual data fetching and backfilling

---

## Available Artisan Commands

### 1. `lottery:fetch {province_code?} {--limit=10}`

Fetch lottery results for a specific province.

**Examples:**
```bash
# Fetch 10 results for Miền Bắc
php artisan lottery:fetch miba

# Fetch 30 results for Quảng Ngãi
php artisan lottery:fetch qung --limit=30

# List all available provinces (run without arguments)
php artisan lottery:fetch
```

---

### 2. `lottery:fetch-all {--limit=5} {--region=}`

Fetch lottery results for all active provinces (or specific region).

**Examples:**
```bash
# Fetch 5 results for all provinces
php artisan lottery:fetch-all

# Fetch 10 results for all provinces
php artisan lottery:fetch-all --limit=10

# Fetch only North region provinces
php artisan lottery:fetch-all --region=north

# Fetch only Central region provinces
php artisan lottery:fetch-all --region=central

# Fetch only South region provinces
php artisan lottery:fetch-all --region=south
```

**After running:** Don't forget to process the queue with `php artisan queue:work`

---

### 3. `lottery:generate-stats {province_code?}`

Generate number frequency statistics (00-99) for lottery results.

**Examples:**
```bash
# Generate statistics for all provinces
php artisan lottery:generate-stats

# Generate statistics for Miền Bắc only
php artisan lottery:generate-stats miba

# Generate statistics for Hồ Chí Minh
php artisan lottery:generate-stats tphc
```

**After running:** Don't forget to process the queue with `php artisan queue:work`

---

### 4. `lottery:seed-historical {days=30} {--province=} {--region=}`

Backfill historical lottery data (for initial setup or catching up).

**Examples:**
```bash
# Fetch 30 days of data for all provinces (default)
php artisan lottery:seed-historical

# Fetch 100 days of data for all provinces
php artisan lottery:seed-historical 100

# Fetch 60 days for Miền Bắc only
php artisan lottery:seed-historical 60 --province=miba

# Fetch 90 days for all South region provinces
php artisan lottery:seed-historical 90 --region=south

# Fetch 200 days for Quảng Ngãi
php artisan lottery:seed-historical 200 --province=qung
```

**Important:** This command can dispatch many jobs. Monitor with `php artisan queue:work`

---

## Background Jobs

### 1. `FetchLotteryResultsJob`

Fetches lottery results for a single province from the API.

**Dispatched by:**
- `lottery:fetch` command
- `lottery:fetch-all` command
- `lottery:seed-historical` command
- Scheduled tasks (daily after draw times)

---

### 2. `FetchAllProvincesJob`

Queues individual fetch jobs for multiple provinces.

**Dispatched by:**
- `lottery:fetch-all` command
- Scheduled tasks (daily after draw times)

**Parameters:**
- `limitNum` (int): Number of results to fetch per province
- `region` (string|null): Optional region filter

---

### 3. `GenerateStatisticsJob`

Calculates number frequency statistics (00-99) for lottery results.

**Dispatched by:**
- `lottery:generate-stats` command
- Scheduled task (weekly on Sundays at midnight)

**What it does:**
- Analyzes all lottery results
- Counts frequency of each number (00-99)
- Tracks last appearance date
- Calculates cycle (days since last appearance)
- Stores in `number_statistics` table

---

## Scheduled Tasks

The application automatically runs the following tasks (configured in `routes/console.php`):

### Daily Tasks

| Task | Time | Description |
|------|------|-------------|
| Fetch XSMB (North) | 18:45 daily | Fetches Miền Bắc results after 18:30 draw time |
| Fetch XSMT (Central) | 17:45 daily | Fetches Miền Trung results after 17:30 draw time |
| Fetch XSMN (South) | 16:50 daily | Fetches Miền Nam results after 16:35 draw time |

### Weekly Tasks

| Task | Time | Description |
|------|------|-------------|
| Generate Statistics | Sunday 00:00 | Updates number frequency statistics for all provinces |

---

## How to Start the Scheduler

### Option 1: Add to Crontab (Production)

Add this single cron entry to your server's crontab:

```bash
* * * * * cd /path/to/vn-lottery && php artisan schedule:run >> /dev/null 2>&1
```

This runs Laravel's scheduler every minute, which then checks if any scheduled tasks need to run.

**To edit crontab:**
```bash
crontab -e
```

---

### Option 2: Test Scheduler Locally

Run the scheduler once to see what would execute:

```bash
php artisan schedule:list
```

Manually trigger the scheduler (useful for testing):

```bash
php artisan schedule:run
```

---

## How to Process Queued Jobs

All background jobs are queued and need a queue worker to process them.

### Start Queue Worker (Development)

```bash
php artisan queue:work
```

This will process jobs as they are dispatched.

---

### Start Queue Worker (Production)

Use a process manager like **Supervisor** to keep the queue worker running.

**Supervisor configuration example** (`/etc/supervisor/conf.d/laravel-worker.conf`):

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/vn-lottery/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/vn-lottery/storage/logs/worker.log
stopwaitsecs=3600
```

**Supervisor commands:**
```bash
# Reload configuration
sudo supervisorctl reread
sudo supervisorctl update

# Start/stop workers
sudo supervisorctl start laravel-worker:*
sudo supervisorctl stop laravel-worker:*
sudo supervisorctl restart laravel-worker:*

# Check status
sudo supervisorctl status
```

---

## Monitoring Jobs

### View Failed Jobs

```bash
php artisan queue:failed
```

### Retry Failed Job

```bash
php artisan queue:retry {job-id}
```

### Retry All Failed Jobs

```bash
php artisan queue:retry all
```

### Clear All Failed Jobs

```bash
php artisan queue:flush
```

---

## Complete Setup Workflow

### Initial Setup (First Time)

1. **Seed historical data** (fetch last 30-100 days):
```bash
php artisan lottery:seed-historical 100
```

2. **Start queue worker** to process jobs:
```bash
php artisan queue:work
```

3. **Generate initial statistics**:
```bash
php artisan lottery:generate-stats
```

4. **Set up cron job** for scheduler (production):
```bash
crontab -e
# Add: * * * * * cd /path/to/vn-lottery && php artisan schedule:run >> /dev/null 2>&1
```

5. **Set up Supervisor** for queue workers (production).

---

### Daily Operations

The application will automatically:
- ✅ Fetch new lottery results after each draw time (XSMB at 18:45, XSMT at 17:45, XSMN at 16:50)
- ✅ Generate statistics every Sunday at midnight

**No manual intervention needed!**

---

## Manual Operations

### Fetch Latest Results Manually

```bash
# Fetch latest for all provinces
php artisan lottery:fetch-all --limit=1

# Process the jobs
php artisan queue:work
```

### Update Statistics Manually

```bash
php artisan lottery:generate-stats
php artisan queue:work
```

---

## Troubleshooting

### Jobs Not Processing

**Problem:** Jobs are queued but not executing.

**Solution:** Make sure queue worker is running:
```bash
php artisan queue:work
```

---

### Scheduler Not Running

**Problem:** Scheduled tasks are not executing.

**Check crontab:**
```bash
crontab -l
```

**Test scheduler manually:**
```bash
php artisan schedule:run
```

---

### Check Logs

**Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

**Queue worker logs (if using Supervisor):**
```bash
tail -f storage/logs/worker.log
```

---

## Configuration

### Queue Configuration

Edit `.env` file:

```env
QUEUE_CONNECTION=database
```

**Available drivers:**
- `sync` - Synchronous (no queue, immediate execution)
- `database` - Store jobs in database (default, recommended for small apps)
- `redis` - Fast, recommended for production
- `sqs` - AWS SQS
- `beanstalkd` - Beanstalkd queue

---

### Scheduler Configuration

Edit `routes/console.php` to modify scheduled tasks:

```php
// Fetch North region daily at 18:45
Schedule::job(new FetchAllProvincesJob(limitNum: 1, region: 'north'))
    ->dailyAt('18:45')
    ->name('fetch-xsmb-daily');
```

---

## Best Practices

1. **Always run queue worker** in production using Supervisor
2. **Monitor failed jobs** regularly with `php artisan queue:failed`
3. **Set up alerts** for failed jobs (optional: integrate with Slack/email)
4. **Test scheduler** before deploying to production
5. **Use Redis** for queue in production (faster than database)
6. **Limit historical data fetching** to avoid API rate limits (max 100-200 days at once)
7. **Monitor API logs** in `api_logs` table to track fetch success rate

---

## Summary

This application automates lottery data management with:
- ✅ **3 scheduled daily fetches** (after each region's draw time)
- ✅ **Weekly statistics generation** (Sundays at midnight)
- ✅ **4 artisan commands** for manual control
- ✅ **3 background jobs** for async processing
- ✅ **Queue system** for reliable job processing

**Everything runs automatically once configured!**
