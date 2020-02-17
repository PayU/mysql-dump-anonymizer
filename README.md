

Work In Progress

This library handles parsing of a mysql dump file and anonymizing it using data types and rules
mysql dump must be exported using:
-  --skip-add-drop-table
-  --skip-add-locks
-  --skip-comments
-  --skip-disable-keys
-  --skip-set-charset
-  --compact (all 5 above)
-  --complete-insert
-  --extended-insert (default on)

Optional for speed:
-  --quick (default on, for faster export)
-  --innodb-optimize-keys (for faster import)

Optional for consistency
-  --skip-lock-tables
-  --single-transaction (automatically enables the above one)
-  --lock-for-backup (percona specific)

Basically the command will look like:

`mysqldump --compact --complete-insert --quick --innodb-optimize-keys --single-transaction --lock-for-backup <database>`

and the output of it can be directly passed to anonymizer.

Or pass the sql file at input and at output:

`php bin/mysql-dump-anonymize.php <databse-dump.sql >databse-dump-anonymized.sql`
