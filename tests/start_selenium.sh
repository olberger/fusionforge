#!/bin/sh

cd ~/selenium-remote-control-0.9.2/selenium-server-0.9.2

export PATH=/usr/lib/j2re1.5-sun/bin:/usr/lib/iceweasel:/bin 

exec java -jar selenium-server.jar -interactive
