Here is a customized, professional `README.md` file for your `admin-app` project, based on the specific steps you provided.

I have formatted it to be clear, readable, and easy for other developers to follow.

---

### Instructions:
Copy the block below and save it as a file named `README.md` in the root directory of your project.

---

```markdown
# Admin App

A Laravel-based administration application.

## 🚀 Installation & Setup Guide

Follow these steps to get the application running on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:
* [PHP](https://www.php.net/downloads.php) (check Laravel version requirements)
* [Composer](https://getcomposer.org/)
* A database server ([MySQL](https://www.mysql.com/), [MariaDB](https://mariadb.org/), or [SQLite](https://www.sqlite.org/))

---

### Step 1: Clone the Repository

Clone the project from GitHub and navigate into the project directory:

```bash
git clone [https://github.com/pozzra/admin-app.git](https://github.com/pozzra/admin-app.git)
cd admin-app
```

### Step 2: Install Dependencies

Use Composer to install the required PHP packages:

```bash
composer install
```

### Step 3: Configure Environment Variables

Copy the example environment file to create your actual `.env` file:

```bash
cp .env.example .env
```

Now, open the `.env` file in your text editor and configure your database settings.

#### Option A: MySQL/MariaDB Configuration (Recommended)

First, create a new database on your server named `admin_app`. Then, update the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_app
DB_USERNAME=root
DB_PASSWORD=
```

#### Option B: SQLite Configuration

If you prefer SQLite, update the `.env` file like this (make sure an empty file exists at `database/database.sqlite` if necessary, though Laravel usually handles this):

```env
DB_CONNECTION=sqlite
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin_app
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Generate Application Key

Generate a unique secure key for your application:

```bash
php artisan key:generate
```

### Step 5: Run Database Migrations

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

### Step 6: Seed the Database (Create Default Admin)

Run the seeders to populate the database with initial data and create the default administrator user:

```bash
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed
```

### Step 7: Start the Development Server

You can now start the local development server:

```bash
php artisan serve
```

By default, the application will be accessible at [http://localhost:8000](http://localhost:8000).

---

## 🔐 Default Credentials

Once the server is running, you can log in to the admin panel using the following default credentials created during seeding:

* **Email:** `admin@gmai.com`
* **Password:** `admin12345`

> **Note:** It is highly recommended to change this password immediately after your first login for security purposes.
```
