#! /bin/bash

echo "Script begins."

read -p "Host name: " hostName
read -p "Database admin username: " adminUsername
read -s -p "Database admin password: " adminPassword
echo ""
read -s -p "Unprivileged user password: " webPassword
echo ""

echo "Creating unprivileged user..."
echo "drop user if exists 'web'@'localhost';" > CreateWebUser.sql
echo "create user if not exists 'web'@'localhost' identified by '"$webPassword"';" >> CreateWebUser.sql
mysql -h $hostName -u $adminUsername --password=$adminPassword < CreateWebUser.sql
rm CreateWebUser.sql
echo "...done creating unprivileged user."

echo "Running Schema.sql..."
mysql -h $hostName -u $adminUsername --password=$adminPassword < Schema.sql
echo "...done running Schema.sql."

echo "Running Data.sql..."
mysql -h $hostName -u $adminUsername --password=$adminPassword < Data.sql
echo "...done running Data.sql."

echo "Script ends."

