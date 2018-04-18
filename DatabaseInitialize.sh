#! /bin/bash
password=$1
mysql -u root --password=$password < Schema.sql
mysql -u root --password=$password < Data.sql


