#!/bin/bash

echo -n "Checking Project Web Directories: "

for i in `cd /home/groups ; ls | grep -v lost+found | grep -v quota.group | grep -v ^ftp ` ; do
	if [ ! -d /home/groups/$i/log ] ; then
		mkdir /home/groups/$i/log
		chown dummy:$i /home/groups/$i/log
		chmod 0774 /home/groups/$i/log
	fi

	if [ ! -d /home/groups/$i/cgi-bin ] ; then
		mkdir /home/groups/$i/cgi-bin
		chown dummy:$i /home/groups/$i/cgi-bin
		chmod 0774 /home/groups/$i/cgi-bin
	fi

	if [ ! -d /home/groups/$i/htdocs ] ; then
		mkdir /home/groups/$i/htdocs
		chown dummy:$i /home/groups/$i/htdocs
		chmod 0774 /home/groups/$i/htdocs
	fi

	if [ "`ls /home/groups/$i/htdocs/`" = "" ] ; then
		cp /root/alexandria/utils/default_page.php /home/groups/$i/htdocs/index.php
		chown dummy:$i /home/groups/$i/htdocs/index.php
                chmod 0664 /home/groups/$i/htdocs/index.php
        fi
done

echo "Done."

/etc/rc.d/init.d/apache restart
