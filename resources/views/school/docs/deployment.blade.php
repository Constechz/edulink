@extends('layouts.app')

@section('title', 'Production Deployment Guide — EduLink')
@section('header_title', 'Production Deployment Guide')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm text-white p-4" style="background: linear-gradient(135deg, #003366 0%, #0055a5 100%); border-radius: 16px;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                        <i class="bi bi-server fs-1"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-1">Production Deployment & Migration Guide</h2>
                        <p class="mb-0 text-white-50">Detailed references, setups, and procedures to migrate EduLink from local XAMPP to an enterprise-grade cloud server.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm glass-card p-3">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-requirements-tab" data-bs-toggle="pill" data-bs-target="#v-pills-requirements" type="button" role="tab" aria-controls="v-pills-requirements" aria-selected="true">
                        <i class="bi bi-list-check me-2"></i> System Requirements
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-nginx-tab" data-bs-toggle="pill" data-bs-target="#v-pills-nginx" type="button" role="tab" aria-controls="v-pills-nginx" aria-selected="false">
                        <i class="bi bi-hdd-network me-2"></i> Nginx Web Server
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-supervisor-tab" data-bs-toggle="pill" data-bs-target="#v-pills-supervisor" type="button" role="tab" aria-controls="v-pills-supervisor" aria-selected="false">
                        <i class="bi bi-cpu me-2"></i> Supervisor Workers
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-cron-tab" data-bs-toggle="pill" data-bs-target="#v-pills-cron" type="button" role="tab" aria-controls="v-pills-cron" aria-selected="false">
                        <i class="bi bi-alarm me-2"></i> Cron & Scheduler
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-backup-tab" data-bs-toggle="pill" data-bs-target="#v-pills-backup" type="button" role="tab" aria-controls="v-pills-backup" aria-selected="false">
                        <i class="bi bi-shield-check me-2"></i> Backup & Restore
                    </button>
                    <button class="nav-link text-start py-3 px-4 mb-2 d-flex align-items-center" id="v-pills-migration-tab" data-bs-toggle="pill" data-bs-target="#v-pills-migration" type="button" role="tab" aria-controls="v-pills-migration" aria-selected="false">
                        <i class="bi bi-arrow-left-right me-2"></i> XAMPP Migration
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Documentation Content -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm glass-card p-4">
                <div class="tab-content" id="v-pills-tabContent">
                    
                    <!-- Tab 1: System Requirements -->
                    <div class="tab-pane fade show active" id="v-pills-requirements" role="tabpanel" aria-labelledby="v-pills-requirements-tab">
                        <h4 class="fw-bold mb-3 text-primary">Production Server Requirements</h4>
                        <p class="text-muted">The production setup for EduLink ERP is optimized for Ubuntu Server 22.04 LTS or 24.04 LTS. Ensure your cloud VPS (AWS, DigitalOcean, Linode) meets the following recommendations:</p>
                        
                        <div class="row my-4">
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <i class="bi bi-cpu text-primary fs-2 mb-2"></i>
                                    <h6 class="fw-bold">Compute</h6>
                                    <p class="small text-muted mb-0">Minimum 2 vCPUs (Recommended: 4 vCPUs for 5,000+ active students)</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <i class="bi bi-memory text-primary fs-2 mb-2"></i>
                                    <h6 class="fw-bold">Memory</h6>
                                    <p class="small text-muted mb-0">Minimum 4GB RAM (Recommended: 8GB to support Redis and batch PDF renders)</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-3 border rounded text-center bg-light">
                                    <i class="bi bi-database text-primary fs-2 mb-2"></i>
                                    <h6 class="fw-bold">Database & Cache</h6>
                                    <p class="small text-muted mb-0">MySQL 8.0+ & Redis 7.0+ for high performance queue storage</p>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-secondary mt-4">PHP 8.3 Extension List</h5>
                        <p class="text-muted">The application requires PHP 8.3 with the following modules active:</p>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Extension</th>
                                        <th>Purpose</th>
                                        <th>Status Check Command</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>bcmath</code></td>
                                        <td>High-precision financial calculations (Fees & Ledger)</td>
                                        <td><code>php -m | grep bcmath</code></td>
                                    </tr>
                                    <tr>
                                        <td><code>gd</code> & <code>imagick</code></td>
                                        <td>Report Card signature / QR generation, user avatars</td>
                                        <td><code>php -m | grep gd</code></td>
                                    </tr>
                                    <tr>
                                        <td><code>redis</code></td>
                                        <td>SaaS session persistence, event broadcasts, queue speed</td>
                                        <td><code>php -m | grep redis</code></td>
                                    </tr>
                                    <tr>
                                        <td><code>zip</code> & <code>mbstring</code></td>
                                        <td>Backup archive handling, multibyte string purification</td>
                                        <td><code>php -m | grep mbstring</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Nginx Web Server -->
                    <div class="tab-pane fade" id="v-pills-nginx" role="tabpanel" aria-labelledby="v-pills-nginx-tab">
                        <h4 class="fw-bold mb-3 text-primary">Nginx Virtual Host Setup</h4>
                        <p class="text-muted">Deploy a tenant-aware reverse proxy routing using Nginx to handle custom domains and wildcard subdomains.</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-info text-dark mb-2">Config File: /etc/nginx/sites-available/edulink</span>
                            <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.85rem; overflow-x: auto;"><code>server {
    listen 80;
    server_name *.edulink.edu.gh edulink.edu.gh;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name *.edulink.edu.gh edulink.edu.gh;
    root /var/www/edulink/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/edulink.edu.gh/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/edulink.edu.gh/privkey.pem;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
    gzip on;
    gzip_types text/css application/javascript application/json;
}</code></pre>
                        </div>
                    </div>

                    <!-- Tab 3: Supervisor Queue Workers -->
                    <div class="tab-pane fade" id="v-pills-supervisor" role="tabpanel" aria-labelledby="v-pills-supervisor-tab">
                        <h4 class="fw-bold mb-3 text-primary">Supervisor Process Control</h4>
                        <p class="text-muted">Supervisor ensures queue worker processes run continuously, automatically restarting them if they fail or memory leak thresholds are hit.</p>
                        
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark mb-2">Config File: /etc/supervisor/conf.d/edulink-worker.conf</span>
                            <pre class="bg-dark text-light p-3 rounded" style="font-size: 0.85rem; overflow-x: auto;"><code>[program:edulink-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/edulink/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=/var/www/edulink
autostart=true
autorestart=true
numprocs=4
user=www-data
redirect_stderr=true
stdout_logfile=/var/log/supervisor/edulink-worker.log
stopwaitsecs=3600</code></pre>
                        </div>

                        <div class="bg-light p-3 rounded border">
                            <h6 class="fw-bold text-dark"><i class="bi bi-terminal me-2"></i>Manage Workers Commands</h6>
                            <pre class="bg-dark text-light p-2 mb-0 rounded" style="font-size: 0.8rem;"><code>sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start edulink-worker:*
sudo supervisorctl status</code></pre>
                        </div>
                    </div>

                    <!-- Tab 4: Cron & Scheduler -->
                    <div class="tab-pane fade" id="v-pills-cron" role="tabpanel" aria-labelledby="v-pills-cron-tab">
                        <h4 class="fw-bold mb-3 text-primary">Laravel Task Scheduler Registry</h4>
                        <p class="text-muted">A single cron entry on the host system triggers Laravel's internal scheduler, running the `db:backup` and `sys:health` commands at configured intervals.</p>
                        
                        <div class="bg-light p-3 rounded border mb-3">
                            <h6 class="fw-bold"><i class="bi bi-clock me-2"></i>Server Crontab Setup (www-data user)</h6>
                            <p class="small text-muted">Run <code>sudo crontab -u www-data -e</code> and append:</p>
                            <pre class="bg-dark text-light p-2 mb-0 rounded" style="font-size: 0.85rem;"><code>* * * * * php /var/www/edulink/artisan schedule:run >> /dev/null 2>&1</code></pre>
                        </div>

                        <h5 class="fw-bold text-secondary mt-4">Scheduled Commands Checklist</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Artisan Command</th>
                                        <th>Target Execution Interval</th>
                                        <th>Purpose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>db:backup</code></td>
                                        <td>Daily at 02:00</td>
                                        <td>Performs incremental DB backup to local storage and alerts if failures occur.</td>
                                    </tr>
                                    <tr>
                                        <td><code>sys:health</code></td>
                                        <td>Every 5 Minutes</td>
                                        <td>Performs diagnostic checks on disk usage, connection pools, and memory availability.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 5: Backup & Restore -->
                    <div class="tab-pane fade" id="v-pills-backup" role="tabpanel" aria-labelledby="v-pills-backup-tab">
                        <h4 class="fw-bold mb-3 text-primary">Backup & Restore Procedures</h4>
                        <p class="text-muted">Automated system backups secure SQL tables and application attachments. Follow these recovery procedures in a staging environment monthly to verify data integrity.</p>

                        <div class="card border-warning mb-3">
                            <div class="card-header bg-warning bg-opacity-20 text-warning-emphasis fw-bold">
                                <i class="bi bi-exclamation-triangle me-2"></i>Important Restore Gating
                            </div>
                            <div class="card-body bg-warning bg-opacity-5">
                                <p class="card-text small mb-0">Prior to performing database restorations on live platforms, force-stop all queue workers using supervisor to avoid state collisions or partial queues.</p>
                            </div>
                        </div>

                        <h5 class="fw-bold text-secondary">Manual Database Dump & Restore Commands</h5>
                        <div class="mb-3">
                            <h6>1. Manual Dump</h6>
                            <pre class="bg-dark text-light p-2 rounded" style="font-size: 0.8rem;"><code>mysqldump -u edulink_user -p edulink_db > backup_$(date +%Y%m%d).sql</code></pre>
                        </div>
                        <div class="mb-3">
                            <h6>2. Stop Active Workers</h6>
                            <pre class="bg-dark text-light p-2 rounded" style="font-size: 0.8rem;"><code>sudo supervisorctl stop edulink-worker:*</code></pre>
                        </div>
                        <div class="mb-3">
                            <h6>3. Restore Backup</h6>
                            <pre class="bg-dark text-light p-2 rounded" style="font-size: 0.8rem;"><code>mysql -u edulink_user -p edulink_db < backup_YYYYMMDD.sql</code></pre>
                        </div>
                        <div class="mb-3">
                            <h6>4. Clear Cache & Start Workers</h6>
                            <pre class="bg-dark text-light p-2 rounded" style="font-size: 0.8rem;"><code>php artisan cache:clear && sudo supervisorctl start edulink-worker:*</code></pre>
                        </div>
                    </div>

                    <!-- Tab 6: XAMPP Migration -->
                    <div class="tab-pane fade" id="v-pills-migration" role="tabpanel" aria-labelledby="v-pills-migration-tab">
                        <h4 class="fw-bold mb-3 text-primary">XAMPP to Cloud Production Migration Path</h4>
                        <p class="text-muted">A step-by-step checklist to port your local XAMPP installation data to the cloud production web host.</p>
                        
                        <div class="list-group list-group-numbered">
                            <div class="list-group-item py-3">
                                <strong>Export Local Database:</strong> Dump local MySQL database from XAMPP phpMyAdmin or command line.
                            </div>
                            <div class="list-group-item py-3">
                                <strong>Transfer Uploads & Attachments:</strong> Copy files in local <code>storage/app/public/</code> directory using SFTP or rsync to server's <code>/var/www/edulink/storage/app/public/</code> directory.
                            </div>
                            <div class="list-group-item py-3">
                                <strong>Update Production Environment:</strong> Configure target <code>.env</code> file with <code>APP_ENV=production</code>, <code>APP_DEBUG=false</code>, and SSL-enabled absolute <code>APP_URL</code>.
                            </div>
                            <div class="list-group-item py-3">
                                <strong>Symlink Storage:</strong> Trigger <code>php artisan storage:link</code> to map user uploads to the public directory.
                            </div>
                            <div class="list-group-item py-3">
                                <strong>Optimize Application Cache:</strong> Run deployment optimization commands:
                                <pre class="bg-dark text-light p-2 mt-2 mb-0 rounded" style="font-size: 0.8rem;"><code>composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache</code></pre>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
