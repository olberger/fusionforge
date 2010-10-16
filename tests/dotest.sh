#!/bin/sh

PHP_PEAR_PHP_BIN=/opt/php/bin/php
export PHP_PEAR_BIN

PATH=/opt/php/bin/:$PATH
export PATH

exec 2>&1 | tee -a /tmp/dotest$$.log

mkdir -p /opt/reports

cd /opt/gforge

svn up /opt/gforge

id=`svn info | grep '^Revision:' | awk '{ print $2 }'`

cd /opt/gforge/tests
phpunit --testdox-html /opt/reports/phpunit-$id.html --verbose AllTests.php

mv /tmp/dotest$$.log /opt/reports/test-$id.txt
