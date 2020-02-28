dbsource=db-to-anonymize
dbuser=db-user-to-connect
dbpass=db-pass-to-connect
patt=\\\\_%
que="SELECT CONCAT ('--ignore-table=${dbsource}.',GROUP_CONCAT(TABLE_NAME SEPARATOR ' --ignore-table=${dbsource}.')) from information_schema.TABLES WHERE TABLE_SCHEMA='${dbsource}' AND TABLE_NAME LIKE '${patt}' GROUP BY '1';"
ignores=$(MYSQL_PWD=$dbpass mysql -N -u $dbuser --execute="$que")
MYSQL_PWD=$dbpass mysqldump -u $dbuser $ignores --complete-insert --hex-blob $dbsource