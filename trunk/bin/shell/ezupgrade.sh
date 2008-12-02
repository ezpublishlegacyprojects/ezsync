#!/bin/bash

DIR_TO_UPGRADE="/var/www/destefani"
EZPUBLISH_DIR="/var/www/ezpublish-4.0.1"
APACHE_WEB_ROOT="/var/www/"
EZPUBLISH_HTTP_PATH="http://ez.no/content/download/242355/1643191/version/1/file/"
EZPUBLISH_FILE="ezpublish-4.0.1-gpl.tar.gz"
ADMIN_SITEACCESS_NAME="panel"
MYSQL_USR="root"
MYSQL_PWD=""
MYSQL_DATABASE="destefaniez"
EZPUBLISH_PATCH="ezpublish-4.0.1-patch-rev22404.tar.gz"

echo "Start upgrading"
echo "DUMP DATABASE"

cd $DIR_TO_UPGRADE

if [ "$MYSQL_PWD" != "" ]; then
	MYSQL_PWD="-p"$MYSQL_PWD
fi

mysqldump -u $MYSQL_USR $MYSQL_PWD $MYSQL_DATABASE > $MYSQL_DATABASE.sql

echo "DUMP DATA"
cp -R $DIR_TO_UPGRADE $DIR_TO_UPGRADE.dump

cd $APACHE_WEB_ROOT

# mkdir $EZPUBLISH_DIR
# cd $EZPUBLISH_DIR

echo "Download eZ Publish file"

if [ -f $EZPUBLISH_FILE ]; then
	echo "File $EZPUBLISH_FILE exists"
else
	wget $EZPUBLISH_HTTP_PATH$EZPUBLISH_FILE
fi

tar xfz $EZPUBLISH_FILE
tar xfz $EZPUBLISH_PATCH

cd $EZPUBLISH_DIR

echo "Delete some files"
rm -rf settings/siteaccess/* settings/override/* var/*

echo "Copy new file to dir"

cp * -R $DIR_TO_UPGRADE
cd $DIR_TO_UPGRADE

php bin/php/ezconvertmysqltabletype.php --newtype=innodb -s panel

php update/common/scripts/4.0/fixobjectremoteid.php -s $ADMIN_SITEACCESS_NAME

mysql -u $MYSQL_USR $MYSQL_PWD $MYSQL_DATABASE < update/database/mysql/4.0/dbupdate-4.0.0-to-4.0.1.sql

php bin/php/ezpgenerateautoloads.php --kernel
php bin/php/ezpgenerateautoloads.php --extension

php extension/ezurlaliasmigration/scripts/migrate.php --create-migration-table
php extension/ezurlaliasmigration/scripts/migrate.php -s panel
php extension/ezurlaliasmigration/scripts/migrate.php --migrate

mysql -u $MYSQL_USR $MYSQL_PWD $MYSQL_DATABASE -sN -e "TRUNCATE ezurlalias_ml"

php bin/php/updateniceurls.php --fetch-limit=1000
php extension/ezurlaliasmigration/scripts/migrate.php --restore
php bin/php/ezcache.php --clear-all --purge
php bin/php/flatten.php all
php update/common/scripts/cleanup.php all -s panel
./bin/modfix.sh

echo "FINSIH UPGRADING"
