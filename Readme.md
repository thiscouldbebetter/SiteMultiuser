Online Multiuser Site
=====================

A simple website in PHP that supports multiple users.

Screenshots
-----------

![Welcome](/Screenshots/Screenshot-Welcome.png?raw=true "Welcome")
![UserLogin](/Screenshots/Screenshot-UserLogin.png?raw=true "User Login")
![UserDetail](/Screenshots/Screenshot-UserDetail.png?raw=true "User Detail")

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
1. Download, install, and run XAMPP.
2. Use the "git clone" command to clone the contents of this repository into a new subdirectory named "OnlineStore" in the root directory for XAMPP's web server, perhaps "C:\xampp\htdocs".
3. Open the XAMPP control panel, click the Start buttons for Apache and MySQL, and verify that they run with no errors.
4. In the XAMPP control panel, click the "Shell" button to open a command prompt that is able to run the "mysql" command.
5. Within the XAMPP command prompt, navigate to the root directory of the repository cloned in a previous step, then into its Source\Database directory.
6. Run the script "DatabaseInitialize.bat" and follow the prompts, using "localhost" as the host name, "root" as the admin username, and values of your choice for the admin and unprivileged 'web' users' passwords.
7. Open the file "Configuration.php" in a text editor and modify its constructor to substitute in the appropriate values.  Notably, the databasePassword field should be changed from "[redacted]" to the actual password set in a previous step.
8. Restart the Apache web server through the XAMPP control panel.
9. Start a web browser and navigate to "http://localhost/OnlineStore".

Testing
-------
To test, log in as one of the default users created in the Data.sql file, for example, "adam", with password "Password_123".  After testing, it would be best to delete these test users and their associated data.  In a future release, the DatabaseInitialize script may be changed to prompt for a test username and password instead.