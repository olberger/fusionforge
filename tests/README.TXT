DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER 
DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER 

Running the test suite will drop your current database, this is
to be used only on test environment.

DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER 
DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER DANGER 


First step, you have to start the selenium remote server.
untar the 1.0.1 release of selenium, then

cd ~/selenium-remote-control-1.0.1/selenium-server-1.0.1
java -jar selenium-server.jar -interactive

Second step, before launching tests, copy file 'config.php.sample'
to 'config.php' and edit the file.

cp config.php.sample config.php
vi config.php

Once configured, run the test suite.

# For PostgreSQL: (NOTE: seems no needed)

configure ~/.pgpass to allow connection from your account.

echo 'localhost:*:gforge:gforge:myadmin' >>  ~/.pgpass
chmod 600 ~/.pgpass

cd tests
php AllTests.php

If you want to run only a specific part, then you can use:

cd tests
phpunit Trackers_AllTests Trackers/AllTests.php


To create an empty database for recording a test:
cd tests
php db_reload.php

===========================================================================
NOTES:

ALL:
For tests checking outgoing emails (like monitoring tests), you have to
configure the "local.inc" file with the following variable:
$sys_sendmail_path='<forge_root>/tests/scripts/catch_mail.php';

For wiki tests checking outgoing emails, you have to configure
the php.ini file with the following variable:
sendmail_path=<forge_root>/tests/scripts/catch_mail.php

MYSQL:
* testMyTestCase(CreateForum) is failing on forum.php:80
=> Seems to be a bug in the mysql port.
