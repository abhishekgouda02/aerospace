======================================================
     AEROBOOK - ONLINE FLIGHT BOOKING SYSTEM
               HOW TO RUN LOCALLY
======================================================

This project can be run easily on any local machine. It uses PHP and is configured to run with SQLite by default, meaning NO complicated database installation is required out of the box.

------------------------------------------------------
METHOD 1: Using PHP's Built-in Web Server (Recommended)
------------------------------------------------------
If you have PHP installed globally on your machine (e.g., via command prompt/terminal):

1. Open your terminal or command prompt (cmd/powershell).
2. Navigate to the project directory:
   cd "d:\New Location\OneDrive\Desktop\ticket"
3. Start the PHP server by running:
   php -S localhost:8000
4. Open your web browser and go to:
   http://localhost:8000

------------------------------------------------------
METHOD 2: Using XAMPP / WAMP / MAMP
------------------------------------------------------
If you prefer using a local server stack like XAMPP:

1. Move or copy the entire "ticket" folder into your XAMPP's "htdocs" folder (or WAMP's "www" folder).
   (e.g., C:\xampp\htdocs\ticket)
2. Open the XAMPP Control Panel and Start "Apache".
3. Open your web browser and go to:
   http://localhost/ticket

------------------------------------------------------
INITIALIZING THE DATABASE (Required on First Run)
------------------------------------------------------
Before you can log in or search for flights, you must create the database structure:

1. In your browser, navigate to the setup script:
   - If using Method 1: http://localhost:8000/setup.php
   - If using Method 2: http://localhost/ticket/setup.php
2. You should see a message saying "Database schema created and dummy flights inserted successfully."
3. Next, navigate to the admin setup script to generate your Admin account:
   - Method 1: http://localhost:8000/admin_setup.php
   - Method 2: http://localhost/ticket/admin_setup.php

------------------------------------------------------
ADMIN LOGIN CREDENTIALS
------------------------------------------------------
After running `admin_setup.php`, you can log in to the admin panel with:
Email:    admin@aerobook.com
Password: admin

------------------------------------------------------
SWITCHING TO MYSQL (Optional)
------------------------------------------------------
By default, the system uses SQLite (database.sqlite will be auto-generated). If you want to use MySQL instead:
1. Import the provided `database.sql` file into your MySQL server (via phpMyAdmin).
2. Edit `config.php`:
   - Remove/comment out the SQLite connection lines.
   - Add your MySQL PDO connection string.
     Example: $pdo = new PDO('mysql:host=localhost;dbname=aerobook', 'root', '');
3. Run `admin_setup.php` again in your browser to re-generate the admin password securely in MySQL.
