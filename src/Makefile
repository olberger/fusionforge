ID=$(shell LANG=C svnversion)
BRANCH=$(shell LANG=C svn info |grep URL | awk -F/ '{print $$(NF-1)}')

PHPUNIT=phpunit

YUICOMPRESSOR = java -jar plugins/wiki/www/themes/default/yuicompressor-2.4.2.jar

js: www/js/common-min.js

gettext:
	utils/manage-translations.sh refresh
	utils/manage-translations.sh build

www/js/common-min.js: www/js/common.js www/js/sortable.js www/scripts/prototype/prototype.js www/scripts/scriptaculous/scriptaculous.js www/scripts/codendi/Tooltip.js www/scripts/codendi/LayoutManager.js www/scripts/codendi/ReorderColumns.js www/plugins/hudson/hudson_tab.js
	cat www/js/common.js www/js/sortable.js www/scripts/prototype/prototype.js www/scripts/scriptaculous/scriptaculous.js www/scripts/codendi/Tooltip.js www/scripts/codendi/LayoutManager.js www/scripts/codendi/ReorderColumns.js www/plugins/hudson/hudson_tab.js > /tmp/combined.js
	$(YUICOMPRESSOR) -o $@ /tmp/combined.js
	rm -f /tmp/combined.js

build-unit-tests:
	mkdir -p $(BUILDDIR)/reports/coverage
	ssh $(HOST) 'svn up -r $(ID) /opt/acosforge-$(BRANCH)'
#	ssh $(HOST) 'sh /opt/acosforge-$(BRANCH)/src/acde/scripts/forge_switch.sh $(BRANCH)'
	ssh $(HOST) 'rm -f /opt/gforge; ln -s acosforge-$(BRANCH)/src /opt/gforge'
	ssh $(HOST) 'rm -fr /tmp/reports; mkdir -p /tmp/reports/coverage'
	ssh $(HOST) 'chown -R gforge /etc/gforge/http'
	ssh $(HOST) '$(PHPUNIT) --log-junit /tmp/reports/phpunit.xml --coverage-clover /tmp/reports/coverage/clover.xml --coverage-html /tmp/reports/coverage/ /opt/acosforge-$(BRANCH)/tests/UnitTests.php'
	scp -rq $(HOST):/tmp/reports $(BUILDDIR)/
	cd $(BUILDDIR)/reports; cp phpunit.xml phpunit.xml.org; xalan -in phpunit.xml.org -xsl ~/phpunit.xslt -out phpunit.xml

build-full-tests:
	mkdir -p $(BUILDDIR)/reports/coverage
	-phpcs --standard=PEAR --report=checkstyle common > $(BUILDDIR)/reports/checkstyle.xml
	ssh $(HOST) 'svn up -r $(ID) /opt/acosforge-$(BRANCH)'
#	ssh $(HOST) 'sh /opt/acosforge-$(BRANCH)/src/acde/scripts/forge_switch.sh $(BRANCH)'
	ssh $(HOST) 'rm -f /opt/gforge; ln -s acosforge-$(BRANCH)/src /opt/gforge'
	ssh $(HOST) 'rm -fr /tmp/reports; mkdir -p /tmp/reports/coverage'
	ssh $(HOST) 'chown -R gforge /etc/gforge/http'
	-ssh $(HOST) '$(PHPUNIT) --log-junit /tmp/reports/phpunit.xml --coverage-clover /tmp/reports/coverage/clover.xml --coverage-html /tmp/reports/coverage/ /opt/acosforge-$(BRANCH)/tests/func'
	scp -rq $(HOST):/tmp/reports $(BUILDDIR)/
	cd $(BUILDDIR)/reports; cp phpunit.xml phpunit.xml.org; xalan -in phpunit.xml.org -xsl ~/phpunit.xslt -out phpunit.xml

init: ../tests/func/Init/empty.php db/gforge.sql
	$(PHPUNIT) ../tests/func/Init/empty.php
	../tests/scripts/pg_backup.pl ../tests/func/gforge-init.csql
