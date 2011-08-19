#! /bin/sh
# postinst/prerm script for fusionforge-plugin-sympa
#

set -e
# set -x				# Be verbose, be very verbose.

apply_modified_conffile_with_ucf() {
    source_file=$1
    destination_file=$2
    package=$3
    template=$4

    debconf_template=$package/$template

    if [ -f $source_file ]
    then
	ucf_package=`ucfq -w $destination_file | cut -d ':' -f 2`
	if [ "x$ucf_package" != "x$package" ]
	then
	    ucf --debconf-ok --debconf-template $debconf_template $source_file $destination_file
	else
	    ucf --debconf-ok $source_file $destination_file
	fi
	ucfr $package $destination_file
	rm $source_file
    fi
}

cleanup_modified_conffile_with_ucf() {
    source_file=$1
    destination_file=$2
    package=$3

    ucf --debconf-ok $source_file $destination_file
    rm $source_file
    ucf --purge $destination_file
    ucfr --purge $package $destination_file
}

case "$1" in
    configure)

	echo "Generating customized exim4 configuration files in /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4"
	/usr/share/gforge/bin/install-exim4-sympa.sh configure-files

	echo "Using ucf to validate changes to exim4 configuration files in /etc/exim4/"
	apply_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf fusionforge-plugin-sympa ucfexim4changeprompt 

	apply_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.template fusionforge-plugin-sympa ucfexim4changeprompt 

	apply_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.localmacros fusionforge-plugin-sympa ucfexim4changeprompt 

	if [ /usr/share/gforge/bin/install-exim4.sh ]
	then
	    echo "Applying changes to exim4 config with update-exim4.conf"
	    update-exim4.conf
	fi
    ;;

    abort-upgrade|abort-remove|abort-deconfigure)
    ;;

    remove)

	echo "Generating clean exim4 configuration files in /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4"
	/usr/share/gforge/bin/install-exim4-sympa.sh purge-files

	echo "Using ucf to validate the restoration of clean exim4 configuration files in /etc/exim4/"
	if [ -f /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new ]
	then
	    cleanup_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf fusionforge-plugin-sympa
	fi

	cleanup_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.template.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.template fusionforge-plugin-sympa

	cleanup_modified_conffile_with_ucf /var/lib/gforge/fusionforge-plugin-sympa/etc/exim4/exim4.conf.localmacros.fusionforge-plugin-sympa-new /etc/exim4/exim4.conf.localmacro fusionforge-plugin-sympa

	/usr/share/gforge/bin/install-exim4-sympa.sh purge

	if [ /usr/share/gforge/bin/install-exim4.sh ]
	then
	    echo "Applying changes to exim4 config with update-exim4.conf"
	    update-exim4.conf
	fi

	;;
    *)
        echo "postinst called with unknown argument \`$1'" >&2
        exit 0
    ;;
esac

exit 0
