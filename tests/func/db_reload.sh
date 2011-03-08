#! /bin/sh
[ $# -eq 1 ] || exit 1

echo "Cleaning up the database"
if type invoke-rc.d 2>/dev/null
then
	invoke-rc.d apache2 stop
	invoke-rc.d postgresql restart
else
	service httpd stop
	service postgresql restart
fi

su - postgres -c "dropdb -e $1"
echo "Executing: pg_restore -C -d template1 < /root/dump"
su - postgres -c "pg_restore -C -d template1" < /root/dump

if type invoke-rc.d 2>/dev/null
then
	invoke-rc.d apache2 start
else
	service httpd start
fi
