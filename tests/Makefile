
PHPUNIT=phpunit

init: func/Init/empty.php ../src/db/gforge.sql
	$(PHPUNIT) func/Init/empty.php
	scripts/pg_backup.php func/gforge-init.sql
