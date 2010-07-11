#!/bin/sh -x

export CONFIG_PHP=func/config.php.buildbot
export SELENIUM_RC_HOST=192.168.0.204
export SELENIUM_RC_DIR=$WORKSPACE/reports
export SELENIUM_RC_URL=${HUDSON_URL}job/$JOB_NAME/ws/reports
export HOST=centos52.local
export CONFIGURED=true

mkdir -p build/packages reports/coverage
make -f Makefile.rh BUILDRESULT=$WORKSPACE/build/packages all

cd tests
phpunit --log-junit $WORKSPACE/reports/phpunit-selenium.xml RPMCentos52Tests.php

#cd ..
#cp $WORKSPACE/reports/phpunit-selenium.xml $WORKSPACE/reports/phpunit-selenium.xml.org; xalan -in $WORKSPACE/reports/phpunit-selenium.xml.org -xsl fix_phpunit.xslt -out $WORKSPACE/reports/phpunit-selenium.xml
