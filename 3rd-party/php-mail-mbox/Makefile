DISTDEBIAN=$(shell [ -f /etc/debian_version ] && echo debian)
DISTREDHAT=$(shell grep -qi 'Red Hat' /etc/issue && echo rh)
DISTSUSE=$(shell grep -qi 'SuSE' /etc/issue && echo rh)
DIST=$(DISTDEBIAN)$(DISTREDHAT)$(DISTSUSE)

PKGDIR=php-mail-mbox-0.6.3
PKGDSC=$(shell ls *.dsc | sort -V -r | head -1)
MINOR=$(shell grep ^Version $(PKGDSC) | sed 's/.[^-]*-\(.*\)/\1/')
PARAM=PKGDIR=$(PKGDIR) svnrev=$(MINOR)

default: $(PKGDIR)
	@make -f Makefile.$(DIST) $(PARAM) default
	@cat ../../Makefile.$(DIST) | grep '^.*:.*#$$' | sed 's/FUSIONFORGE/$(FUSIONFORGE)/' | sed 's/^\(.*:\).*#\(.*\)#$$/\1		\2/'

%: $(PKGDIR)
	@make -f Makefile.$(DIST) $(PARAM) $@

$(PKGDIR):
	#pear download channel://pear.php.net/Mail_Mbox-0.6.3
	# gives Mail_Mbox-0.6.3.tgz
	# then dh-make-pear Mail_Mbox-0.6.3.tgz
	dpkg-source -x $(PKGDSC)

clean:
	rm -rf $(PKGDIR)
