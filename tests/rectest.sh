#!/bin/sh

recordmydesktop -x 1 -y 25 -width 1024 -height 900 --overwrite -o test.ogg &
phpunit $@
scrot test.png
pkill recordmydesktop
