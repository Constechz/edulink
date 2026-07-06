# EduSphere Ghana ERP (EduLink)
> **A Comprehensive, GES-Compliant, Multi-Tenant School ERP & SaaS Platform**

EduSphere Ghana ERP (EduLink) is an enterprise-grade SaaS platform built for schools in Ghana (Nursery, KG, Primary, JHS, SHS, TVET, Colleges, and Universities). It provides a full suite of management tools to run educational institutions efficiently, fully aligned with the Ghana Education Service (GES) continuous assessment standards and curriculum requirements.

---

## 🌟 Key Features

### 1. Multi-Tenant & Multi-Campus SaaS Core
*   **Super Admin Control Panel**: Live metrics, tenant onboarding approvals, subscription tier configurations, and system-wide setting controls.
*   **Billing & Billing Overrides**: Manually adjust subscription cycles, override package settings, or manage custom plan limits per school.
*   **Impersonation Engine**: Allows Super Admins to securely access school dashboards for real-time support.
*   **Access Logging**: Comprehensive audit trail capturing user IP, action, browser agent, and session timings.

### 2. Dynamic Scoring Engine
*   **GES Continuous Assessment Compliant**: Independently configure class scores (homework, projects, practicals, class tests) vs. terminal exam weightings (e.g., 30/70, 50/50, 40/60).
*   **Auto-Scaling Calculations**: Enter raw scores per component and the engine automatically handles scale aggregation to target scores.
*   **Flexible Grading Scales**: School-specific custom grading thresholds and grade descriptions per level or subject.

### 3. Dynamic Promotion Engine
*   **Cumulative 3-Term Academic Tracking**: Computes overall performance over the full academic year (Term 1, Term 2, Term 3) to determine promotions.
*   **Level-Specific Promotion Rules**: Setup distinct promotion parameters (minimum average, mandatory subject passes) for Nursery/KG, Primary, JHS, and SHS levels.
*   **Terminal Candidate Protection**: Automatically locks JHS 3 (Basic 9) and SHS 3 students out of internal promotions, routing them to BECE/WASSCE candidate tracking.
*   **Approval Workflows**: Review, override, and sign off on promotions by the Headteacher or Principal before publishing report cards.

### 4. Drag-and-Drop School Website Builder
*   **Integrated GrapesJS Page Builder**: Drag-and-drop landing page components (Hero, About, Admissions, Gallery, News, Events, Contact).
*   **ERP Content Feeds**: Publicly display upcoming school events, staff directories, and announcements direct from the ERP system database.
*   **Subdomain & Custom Domain Mapping**: Run each tenant's school website under its own subdomain or mapped custom domain.
*   **Branding & SEO Settings**: Fine-grained typography, logo customization, theme colors, metadata, and analytics configuration.

### 5. Academic & Student Management
*   **Student Registry & Admissions CRM**: Manage applications from online form submission to approval, onboarding, and classroom placement.
*   **Staff Registry & HR Portal**: Track staff biographical information, academic qualifications, credentials, and generate professional PDF staff profile reports.
*   **Academic Structure Builder**: Configurable departments, campuses, years, terms, and sections.
*   **Attendance & Timetable Planners**: Drag-and-drop scheduling calendars for classes and exams.

### 6. Billing, Invoices & Payments
*   **Custom Fee Structures**: Dynamic tuition items mapped to specific grade levels or campuses.
*   **Automated Invoice Dispatch**: Generate itemized term invoices automatically for enrolled students.
*   **Integrated Payments**: Process tuition payments online via the Paystack gateway or record manual cash, cheque, and mobile money transactions.
*   **Accounting Ledger**: Basic cashbooks, billing logs, and revenue generation tracking.

### 7. Communication & Integrations
*   **BMS Africa SMS Gateway**: Integrated text message engine for sending bulk notifications, payment reminders, student report highlights, and MFA tokens.
*   **Email Notification Engine**: System logs, custom newsletters, and testing suites.
*   **AI Portal Assistant**: Administrative analytics summaries and automated performance feedback.

---

## 🛠️ Technology Stack
*   **Backend Framework**: PHP 8.2+ | Laravel 12.x
*   **Database**: MySQL 8.0+ / SQLite
*   **Caching & Queue**: Redis / Database Queue Driver
*   **Frontend**: Alpine.js, Bootstrap 5, jQuery, HTML5, Vanilla CSS3
*   **Visual Assets & Charts**: GrapesJS (Page Builder), Chart.js (Data Visualization)
*   **Export Tools**: DomPDF (PDF Report Cards)
*   **API Integrations**: Paystack (Payment Gateway), BMS Africa (SMS Gateway)

---

## 💻 Local Setup & Installation

Follow these steps to run the application on your local development machine (using XAMPP, Laragon, or a standalone PHP environment):

### Prerequisites
*   **PHP**: Version 8.2 or 8.3 with standard extensions (`pdo_mysql`, `mbstring`, `openssl`, `xml`, `zip`, `gd`, `bcmath`).
*   **Composer**: Dependency Manager for PHP.
*   **Node.js & NPM**: For compiling frontend assets.
*   **Database**: MySQL/MariaDB or SQLite.

### Setup Guide
1.  **Clone the Repository**:
    ```bash
    git clone https://github.com/Constechz/edulink.git
    cd edulink
    ```

2.  **Install PHP Dependencies**:
    ```bash
    composer install
    ```

3.  **Install Node.js Dependencies & Compile Assets**:
    ```bash
    npm install
    npm run build
    ```

4.  **Configure the Environment Variables**:
    Copy the example file and edit database/integration variables:
    ```bash
    cp .env.example .env
    ```
    *Open the `.env` file and update your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), mail credentials, and API keys.*

5.  **Generate Application Encryption Key**:
    ```bash
    php artisan key:generate
    ```

6.  **Run Database Migrations and Seeders**:
    ```bash
    php artisan migrate --seed
    ```

7.  **Run the Development Server**:
    *   To run the artisan server:
        ```bash
        php artisan serve
        ```
    *   To run the asset watcher in parallel:
        ```bash
        npm run dev
        ```
    *Alternatively, you can run:*
    ```bash
    composer dev
    ```

---

## 🌐 Production Deployment on CloudPanel

This deployment guide assumes you are using **CloudPanel** on a Debian or Ubuntu server to manage your web applications.

### Step 1: Create a PHP/Laravel Site in CloudPanel
1.  Log in to your **CloudPanel** admin interface.
2.  Click on **Add Site** in the top right corner and select **Create a PHP Site**.
3.  Choose the **Laravel** application template (CloudPanel automatically configures rewrite rules for `/public`).
4.  Fill in the site details:
    *   **Domain Name**: `your-domain.com` (or subdomains like `app.your-domain.com`)
    *   **PHP Version**: Select `PHP 8.2` or `PHP 8.3` (matching your composer.json requirements)
    *   **Site User**: E.g., `edulink-user` (Choose a unique username)
    *   **Site User Password**: Enter a secure password
5.  Click **Create** to initialize the hosting virtual host.

### Step 2: Set Up the Database in CloudPanel
1.  In the CloudPanel sidebar, go to **Databases** and click **Add Database**.
2.  Select your site from the list.
3.  Define the database details:
    *   **Database Name**: `edulink_db`
    *   **Database User**: `edulink_db_user`
    *   **Database Password**: Create or generate a strong password
4.  Click **Create** and copy these credentials.

### Step 3: Clone Codebase via SSH
1.  Log in to your server via SSH using your CloudPanel site user (Do not run this as `root` to preserve site user file permissions):
    ```bash
    ssh edulink-user@your-server-ip
    ```
2.  Navigate to your site directory:
    ```bash
    cd htdocs
    ```
3.  Remove the default index folder created by CloudPanel:
    ```bash
    rm -rf your-domain.com
    ```
4.  Clone your Git repository into the target directory:
    ```bash
    git clone https://github.com/Constechz/edulink.git your-domain.com
    cd your-domain.com
    ```

### Step 4: Environment & Dependency Setup
1.  Copy the environment file:
    ```bash
    cp .env.example .env
    ```
2.  Open `.env` in a text editor (e.g., nano) and update production variables:
    ```bash
    nano .env
    ```
    *   `APP_ENV=production`
    *   `APP_DEBUG=false`
    *   `APP_URL=https://your-domain.com`
    *   `DB_CONNECTION=mysql`
    *   `DB_HOST=127.0.0.1`
    *   `DB_PORT=3306`
    *   `DB_DATABASE=edulink_db`
    *   `DB_USERNAME=edulink_db_user`
    *   `DB_PASSWORD=your_strong_db_password`
    *   Configure `SMS_BMS_API_KEY`, Paystack details, and Mail Server settings.
3.  Save and close (`CTRL+O`, `Enter`, `CTRL+X`).
4.  Install Composer dependencies in production mode (excluding dev tools):
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
5.  Generate the application encryption key:
    ```bash
    php artisan key:generate
    ```
6.  Run the database migrations:
    ```bash
    php artisan migrate --force
    ```

### Step 5: Frontend Asset Compilation
1.  Install frontend dependencies and build for production:
    ```bash
    npm install
    npm run build
    ```

### Step 6: Configure File Permissions & Storage Link
1.  Create the symbolic storage link so uploaded assets are publicly accessible:
    ```bash
    php artisan storage:link
    ```
2.  Set the correct folder permissions for the server:
    ```bash
    chmod -R 775 storage bootstrap/cache
    ```

### Step 7: Configure Optimization Cache
Cache routes, configurations, views, and events to ensure maximum speed and minimal disk reading under load:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Step 8: Set Up the Scheduler Cron Job in CloudPanel
Laravel uses a single cron job to orchestrate recurring tasks (sending email alerts, processing SMS queues, updating billing metrics):
1.  Log in to your **CloudPanel** Dashboard.
2.  Go to your site and click on **Cron Jobs**.
3.  Click **Add Cron Job**.
4.  Configure the settings:
    *   **Schedule**: `* * * * *` (Every minute)
    *   **Command**: `php /home/edulink-user/htdocs/your-domain.com/artisan schedule:run >> /dev/null 2>&1`
5.  Click **Save**.

### Step 9: Configure Supervisor for Queue Workers (Optional but Recommended)
For queue tasks (such as mass SMS sending or report generation runs) to execute in the background without blocking the UI, you should configure a queue worker:
1.  SSH into your server as **root** (since Supervisor config resides in root directory `/etc/supervisor/conf.d/`).
2.  Create a configuration file:
    ```bash
    nano /etc/supervisor/conf.d/edulink-worker.conf
    ```
3.  Paste the following configuration (replace `edulink-user` and paths with your CloudPanel username and directory):
    ```ini
    [program:edulink-worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/edulink-user/htdocs/your-domain.com/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
    autostart=true
    autorestart=true
    stopasgroup=true
    killasgroup=true
    user=edulink-user
    numprocs=2
    redirect_stderr=true
    stdout_logfile=/home/edulink-user/htdocs/your-domain.com/storage/logs/worker.log
    stopwaitsecs=3600
    ```
4.  Save and exit. Reload Supervisor:
    ```bash
    supervisorctl reread
    supervisorctl update
    supervisorctl start edulink-worker:*
    ```

---

## 🚀 Post-Deployment Maintenance & Deploy Script

For simple updates, you can pull your changes and run our optimization commands.

On Windows development machines, you can run the localized helper:
```cmd
deploy.bat
```

For subsequent deployments on CloudPanel:
1.  SSH as your site user:
    ```bash
    cd /home/edulink-user/htdocs/your-domain.com
    git pull origin main
    composer install --no-dev --optimize-autoloader
    php artisan migrate --force
    npm install && npm run build
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
    ```

---

## 📄 License
The codebase is proprietary software. All rights reserved. Managed under Constechz, Ghana.
