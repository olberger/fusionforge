#! /bin/sh
# postinst script for @OLDPACKAGE@
#
# see: dh_installdeb(1)

set -e
# set -x				# Be verbose, be very verbose.

# summary of how this script can be called:
#        * <postinst> `configure' <most-recently-configured-version>
#        * <old-postinst> `abort-upgrade' <new version>
#        * <conflictor's-postinst> `abort-remove' `in-favour' <package>
#          <new-version>
#        * <deconfigured's-postinst> `abort-deconfigure' `in-favour'
#          <failed-install-package> <version> `removing'
#          <conflicting-package> <version>
# for details, see /usr/share/doc/packaging-manual/
#
# quoting from the policy:
#     Any necessary prompting should almost always be confined to the
#     post-installation script, and should be protected with a conditional
#     so that unnecessary prompting doesn't happen if a package's
#     installation fails and the `postinst' is called with `abort-upgrade',
#     `abort-remove' or `abort-deconfigure'.

#. /usr/share/debconf/confmodule

case "$1" in
    configure)

#	@PACKAGE@-config
        # Patch Exim configuration files into templates
#	/usr/share/@OLDPACKAGE@/bin/install-exim4.sh configure-files

	# use_mailman=false
	# if [ -f /usr/share/@OLDPACKAGE@/bin/install-exim4-mailman.sh ]
	# then
	#     use_mailman=true
	# fi
	# use_sympa=false
	# if [ -f /usr/share/@OLDPACKAGE@/bin/install-exim4-sympa.sh ]
	# then
	#     use_sympa=true
	# fi

	
	# if [ -f /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-mta-exim4/etc/aliases.@OLDPACKAGE@-new ]
	# then
	# 	ucf --debconf-ok /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-mta-exim4/etc/aliases.@OLDPACKAGE@-new /etc/aliases
	# 	rm /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-mta-exim4/etc/aliases.@OLDPACKAGE@-new
	# fi
	# if [ "$use_mailman" = "true" ]
	# then
	#     # prepare patched versions of the exim4 config files suitable for sympa
	#     if [ -x /usr/share/@OLDPACKAGE@/bin/install-exim4-mailman.sh ]
	#     then
	# 	/usr/share/@OLDPACKAGE@/bin/install-exim4-mailman.sh configure-files
	#     fi

	#     if [ -f /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.@OLDPACKAGE@-lists-mailman-new ]
	#     then
	# 	ucf --debconf-ok /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.@OLDPACKAGE@-lists-mailman-new /etc/exim4/exim4.conf
	# 	rm /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.@OLDPACKAGE@-lists-mailman-new
	#     fi
	#     if [ -f /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.template.@OLDPACKAGE@-lists-mailman-new ]
	#     then
	# 	ucf --debconf-ok /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.template.@OLDPACKAGE@-lists-mailman-new /etc/exim4/exim4.conf.template
	# 	rm /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/exim4/exim4.conf.template.@OLDPACKAGE@-lists-mailman-new
	#     fi
	#     if [ -f /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/01_exim4-config_listmacrosdefs.@OLDPACKAGE@-lists-mailman-new ]
	#     then
	# 	ucf --debconf-ok /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/01_exim4-config_listmacrosdefs.@OLDPACKAGE@-lists-mailman-new /etc/exim4/conf.d/main/01_exim4-config_listmacrosdefs
	# 	rm /var/lib/@OLDPACKAGE@/@OLDPACKAGE@-lists-mailman/etc/01_exim4-config_listmacrosdefs.@OLDPACKAGE@-lists-mailman-new
	#     fi
	# fi
	# if [ "$use_sympa" = "true" ]
	# then
	    # prepare patched versions of the exim4 config files suitable for sympa
#	    if [ -x /usr/share/@OLDPACKAGE@/bin/install-exim4-sympa.sh ]
#	    then
		/usr/share/gforge/bin/install-exim4-sympa.sh configure-files
#	    fi

	    if [ -f /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new ]
	    then
		ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf
		rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new
	    fi
	    if [ -f /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new ]
	    then
		ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.template
		rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new
	    fi
	    if [ -f /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new ]
	    then
		ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.localmacros
		rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new
	    fi
#	fi

	update-exim4.conf

    ;;

    abort-upgrade|abort-remove|abort-deconfigure)
    ;;

    remove)
	    # prepare patched versions of the exim4 config files suitable for sympa
#	    if [ -x /usr/share/gforge/bin/install-exim4-sympa.sh ]
#	    then
		/usr/share/gforge/bin/install-exim4-sympa.sh purge-files
#	    fi

	    if [ -f /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new ]
	    then
		ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf
		rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new
		ucf --purge /etc/exim4/exim4.conf
	    fi

	    ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.template
	    rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new
	    ucf --purge /etc/exim4/exim4.conf.template

	    ucf --debconf-ok /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.localmacros
	    rm /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new
	    ucf --purge /etc/exim4/exim4.conf.localmacros

#	fi

#	    if [ -x /usr/share/gforge/bin/install-exim4-sympa.sh ]
#	    then
		/usr/share/gforge/bin/install-exim4-sympa.sh purge
#	    fi

	;;
    *)
        echo "postinst called with unknown argument \`$1'" >&2
        exit 0
    ;;
esac

# dh_installdeb will replace this with shell code automatically
# generated by other debhelper scripts.

#DEBHELPER#

exit 0
