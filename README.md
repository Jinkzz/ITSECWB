# WecSeb Web Application

WecSeb is a PHP and MySQL-based web application deployed using XAMPP.



------------------------------------------------------------------------

#  System Requirements

-   XAMPP (Apache + MySQL)
-   Web Browser (Chrome, Edge, Firefox)
-   Windows OS (recommended)

Download XAMPP: https://www.apachefriends.org/

------------------------------------------------------------------------

# Project Installation (XAMPP Deployment)

# Move Project Folder

1.  Open your XAMPP installation directory: C:`\xampp`{=tex}\

2.  Open the `htdocs` folder: C:`\xampp`{=tex}`\htdocs`{=tex}\

3.  Place the **wecseb** folder inside `htdocs`.

Final structure:

C:`\xampp`{=tex}`\htdocs`{=tex}`\wecseb`{=tex}

------------------------------------------------------------------------

# Start XAMPP Services

1.  Open **XAMPP Control Panel**
2.  Start:
    -   Apache
    -   MySQL

Make sure both services turn green.

------------------------------------------------------------------------

#  Database Setup

##  Open phpMyAdmin

Go to:

http://localhost/phpmyadmin

------------------------------------------------------------------------

# Create Database

1.  Click **New**
2.  Enter database name:

user_system

3.  Click **Create**

------------------------------------------------------------------------

## 3️⃣ Import Database File

1.  Click the database you created (`wecseb_db`)
2.  Click **Import**
3.  Click **Choose File**
4.  Select the file:

users.sql

5.  Click **Go**

The database tables will now be created.

------------------------------------------------------------------------

# ⚙️ Configure Database Connection

Open:

config.php

Make sure the database credentials match:

``` php
$host = "localhost";
$username = "root";
$password = "";
$database = "wecseb_db";
```

Save the file after confirming.

------------------------------------------------------------------------

# ▶️ Running the Application

Open your browser and go to:

https://localhost/wecseb/login.php

OR

https://localhost/wecseb/registration.php

------------------------------------------------------------------------

# 🔒 HTTPS Setup (Self-Signed Certificate)

The app enforces HTTPS in `core.php`, so Apache SSL should be enabled.

1. Open XAMPP Control Panel and click **Config** for Apache.
2. Ensure these lines are enabled in `httpd.conf`:
    - `LoadModule ssl_module modules/mod_ssl.so`
    - `Include conf/extra/httpd-ssl.conf`
3. Start Apache and confirm HTTPS works:
    - https://localhost/
4. If your browser shows a certificate warning, proceed manually (self-signed is acceptable for local development).

Optional: if you use a custom virtual host, bind it to port 443 in `httpd-ssl.conf` and point to your `htdocs/wecseb` directory.

------------------------------------------------------------------------

# 📁 Project File Overview

-   `login.php` -- User login page
-   `registration.php` -- User registration page
-   `profile.php` -- User profile page
-   `admin_page.php` -- Admin dashboard
-   `logout.php` -- Logout function
-   `config.php` -- Database configuration
-   `users.sql` -- Database structure
-   `uploads/` -- Stores uploaded files

------------------------------------------------------------------------
