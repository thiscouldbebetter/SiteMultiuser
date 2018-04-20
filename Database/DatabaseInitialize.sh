#! /bin/bash
read -s -p "Enter the database password: " password
mysql -u root --password=$password < Schema.sql
mysql -u root --password=$password < Data.sql

