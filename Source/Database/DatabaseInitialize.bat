@echo off

echo DatabaseInitialize.bat
echo.

echo Script begins.

set hostName=localhost
set adminUsername=root

set /p "hostName=Host name (default is '%hostName%'): "
set /p "adminUsername=Database admin username (default is '%adminUsername%'): " 
set /p "adminPassword=Database admin password (will be changed to this from '' if never yet set): "
echo.

echo About to set admin user password (this will fail if already set)...
set sqlCommand="alter user %adminUsername%@%hostName% identified by '%adminPassword%';"
mysql -h localhost -u root -e %sqlCommand%
echo ...done setting admin user password.
echo.

set /p "webPassword=Unprivileged 'web' user password to set: " 
echo.

echo Creating unprivileged user...
echo drop user if exists 'web'@'localhost'; > CreateWebUser.sql
echo create user if not exists 'web'@'localhost' identified by '%webPassword%'; >> CreateWebUser.sql
mysql -h %hostName% -u %adminUsername% --password=%adminPassword% < CreateWebUser.sql
del CreateWebUser.sql
echo ...done creating unprivileged user.

echo Running Schema.sql...
mysql -h %hostName% -u %adminUsername% --password=%adminPassword% < Schema.sql
echo ...done running Schema.sql.

echo Running Data.sql...
mysql -h %hostName% -u %adminUsername% --password=%adminPassword% < Data.sql
echo ...done running Data.sql.

echo Script ends.

