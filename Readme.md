Online Store
============

A simple e-commerce site in PHP.

Installing and Running on Linux
-------------------------------

1. Use the "sudo apt install" command to install the packages "apache2", "mysql-server", "php", "libapache-mod-php", "php-mysql", and "php-curl".
2. Use the "git clone" command to clone the contents of this repository into a new subdirectory named "OnlineStore" in the "/var/www/html/" directory.
3. Create a new user by running the command "adduser web", and following the prompts, making a note of the user's password.
4. Use the "chown" and "chgrp" commands to assign the directory and its files to the user "web".
5. Run the command "chmod +x OnlineStore" to make the directory executable, a step that is evidently necessary to make subdirectories work in Apache.
6. Edit the file "Data.sql" to substitute in the required password for the "web" user, then use chmod to make the script "Database/DatabaseInitialize.sh" executable, run it, and supply the database password when prompted.
7. Edit the file "Configuration.php" to substitute in the appropriate values.
8. Restart the apache2 service by running "sudo service apache2 restart".
9. Start a web browser and navigate to "http://localhost/OnlineStore".

Installing and Running on Windows
---------------------------------
1. Download, install, and XAMPP.
2. Use the "git clone" command to clone the contents of this repository into a new subdirectory named "OnlineStore" in the root directory for XAMPP's web server, perhaps "C:\xampp\htdocs".
3. Open the XAMPP control panel and start MySQL.
4. Edit the file "Data.sql" to substitute in the required password for the "web" user, then run the script "Database/DatabaseInitialize.sh" and supply the database password when prompted.
5. Edit the file "Configuration.php" to substitute in the appropriate values.
6. Restart the Apache web server through the XAMPP control panel.
7. Start a web browser and navigate to "http://localhost/OnlineStore".
