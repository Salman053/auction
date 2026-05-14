# Yahoo Auctions Japan - Platform & Scraper

A complete web platform and background scraper for mirroring, monitoring, and bidding on Yahoo Auctions Japan items in real-time. Built on Laravel 12.

## 🌟 Key Features

- **Automated Scraping Engine**: Automatically runs in the background pulling items from Yahoo Auctions Japan across multiple categories.
- **Smart Synchronization**: Queues high-res images and auction details recursively to prevent blocking.
- **Proxy Bidding Wallet System**: Users can deposit funds and place proxy bids on live items.
- **Admin Dashboard**: Full control over scraping tasks, users, and shipment statuses.

---

## 💻 Local Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL or MariaDB
- Redis (Highly recommended for the background queues)

### Installation Steps

1. **Clone the repository and install dependencies:**

    ```bash
    composer install
    npm install
    ```

2. **Environment Configuration:**
   Copy the example `.env` file:

    ```bash
    cp .env.example .env
    ```

    Generate the app key:

    ```bash
    php artisan key:generate
    ```

    _Make sure to configure your `DB_` (Database) and `REDIS_` settings in `.env`._

3. **Database Setup:**
   Run the migrations and seeders to create the admin user and necessary tables:

    ```bash
    php artisan migrate:fresh --seed
    ```

4. **Compile Assets:**

    ```bash
    npm run build
    ```

5. **Start the Local Services:**
   You will need to run three separate terminal windows locally for everything to work:

    ```bash
    # Terminal 1: Run the web server
    php artisan serve

    # Terminal 2: Run the Queue Worker (Processes the high-res image syncs)
    php artisan queue:work --queue=default,sync

    # Terminal 3: Run the Scheduler (Triggers the scrapers every 10 mins)
    php artisan schedule:work
    ```

---

## 🚀 Production Hosting Guide (From Start to Finish)

To host this application in a live production environment, we recommend using a standard Ubuntu 22.04/24.04 server managed by **Laravel Forge** or **Laravel Cloud**. If you are doing it manually, follow this guide:

### Step 1: Server Requirements

Provision a VPS (DigitalOcean, AWS, Linode) with at least:

- 2GB RAM (4GB recommended due to background scraping)
- Ubuntu Linux
- PHP 8.2, Nginx/Apache, MySQL 8.0+, Redis

### Step 2: Deploying the Code

1. Clone the repository into `/var/www/yahoo-auction`.
2. Run `composer install --optimize-autoloader --no-dev`.
3. Set your `.env` file variables. **Crucial Settings for Production:**
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://yourdomain.com
    QUEUE_CONNECTION=redis
    CACHE_STORE=redis
    ```
4. Run `php artisan migrate --force`.
5. Run `npm install` and `npm run build` to compile the CSS/JS.
6. Set correct permissions:
    ```bash
    chown -R www-data:www-data /var/www/yahoo-auction
    chmod -R 775 /var/www/yahoo-auction/storage
    chmod -R 775 /var/www/yahoo-auction/bootstrap/cache
    ```

### Step 3: Configure the Scheduler (Cron Job)

The application relies heavily on the Laravel Scheduler to trigger the Yahoo Scraper automatically.
Add the following single Cron entry to your server (run `crontab -e`):

```bash
* * * * * cd /var/www/yahoo-auction && php artisan schedule:run >> /dev/null 2>&1
```

_This allows Laravel to evaluate `routes/console.php` every minute and launch the scraper exactly when needed._

### Step 4: Configure Queue Workers (Supervisor)

Because scraping fetches thousands of high-res images via the `SyncAuctionDetails` job, you **must** have background queue workers running permanently.

1. Install Supervisor:

    ```bash
    sudo apt-get install supervisor
    ```

2. Create a configuration file at `/etc/supervisor/conf.d/yahoo-worker.conf`:

    ```ini
    [program:yahoo-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /var/www/yahoo-auction/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=www-data
    numprocs=4
    redirect_stderr=true
    stdout_logfile=/var/www/yahoo-auction/storage/logs/worker.log
    stopwaitsecs=3600
    ```

    _Note: `numprocs=4` tells the server to run 4 concurrent workers. Do not set this higher than 5 to avoid IP Rate Limiting from Yahoo._

3. Start the workers:
    ```bash
    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start yahoo-worker:*
    ```

### Step 5: Web Server Configuration (Nginx Example)

Point your Nginx site configuration to the `/public` directory of the application:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/yahoo-auction/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Updates & Maintenance

Whenever you pull new code from Git to your server, you must run the following sequence to safely restart the scrapers:

```bash
git pull origin main
composer install --no-dev
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan queue:restart   # <--- CRITICAL: Restarts background jobs to load new code
```

---

## 🛑 Important Warning: IP Rate Limiting

Yahoo Auctions Japan employs strict rate limits. If you process too many background jobs concurrently, Yahoo will temporarily block your server's IP address (returning HTTP 429 errors or captchas).

- Ensure your `numprocs` in Supervisor stays between `2` and `4`.
- If you notice missing images or `null` responses in production, check your `storage/logs/worker.log` for 429 errors and reduce the number of queue workers.
