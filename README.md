[![Build Status](https://travis-ci.com/PayU/mysql-dump-anonymizer.svg?branch=master)](https://travis-ci.com/PayU/mysql-dump-anonymizer)

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
-  --hex-blob
-  --extended-insert (default on)

Optional for speed:
-  --quick (default on, for faster export)
-  --innodb-optimize-keys (for faster import)

Optional for consistency
-  --skip-lock-tables
-  --single-transaction (automatically enables the above one)
-  --lock-for-backup (percona specific)

Basically the command will look like:

`mysqldump --complete-insert --hex-blob --innodb-optimize-keys --single-transaction --lock-for-backup <database>`
and the output of it can be directly passed to anonymizer.

When using --compact dump file will not have @@SQL_MODE setting of the source server. When dealing with options like STRICT_TRANS_TABLES / NO_ZERO_IN_DATE this can be a problem.

Check the system variables `net_buffer_length` and `max-allowed-packet` on the 
destination server to be at least the same amount as on the source server.
- https://dev.mysql.com/doc/refman/5.6/en/mysqldump.html#option_mysqldump_net-buffer-length


Or pass the sql file at input and at output:

`php bin/mysql-dump-anonymize.php --config=FILENAME <databse-dump.sql >databse-dump-anonymized.sql`

```text
Usage: mysqldump [options] <source-db> | php mysql-dump-anonymize.php --config=FILENAME [OPTIONS] | mysql [options] -D <destination-db>

mysql-dump-anonymize.php options:
 --config-type          Default Value: yaml
                        Specifies the type of the config used.

 --line-parser          Default Value: mysqldump
                        Specifies the type of the line parser used.

 --dump-size            When available, specify the length of the data being anonymized.
                        This will be used to show progress data at runtime.

 --show-progress        Default value: 1
                        Set to 0 to not show progress data.

 --salt                 Default value: md5(microtime())
                        Specifies the hashing salt value at runtime.

```
