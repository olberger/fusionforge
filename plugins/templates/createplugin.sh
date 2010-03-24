#!/bin/sh

modelfullname=HelloWorld
modelminus=`echo $modelfullname | tr '[A-Z]' '[a-z]'`
modelplugdir=$modelminus
dopackage=0

usage() {
	echo Usage: $0 [--dopackage] PluginName
}

echo "Plugin template creator"
if [ $# -eq 0 ] 
then
	usage
else
	case $1 in 
		--dopackage)
			dopackage=1
			shift
			;;
		*)
			;;
	esac
	fullname=$1
	minus=`echo $1 | tr '[A-Z]' '[a-z]'`
	plugdir=$minus
	[ ! -d $modelplugdir/debian/fusionforge-plugin-$modelminus ] || (cd $modelplugdir ; debclean)
	echo "Creating $1 plugin"
	echo "Creating directory $plugdir"
	[ ! -d $plugdir ] && mkdir $plugdir
	[ ! -d $plugdir/bin ] && mkdir $plugdir/bin
	[ ! -d $plugdir/etc/plugins/$minus ] && mkdir -p $plugdir/etc/plugins/$minus
	[ ! -d $plugdir/common/languages ] && mkdir -p $plugdir/common/languages
	[ ! -d $plugdir/www ] && mkdir $plugdir/www

	if [ ! -f $plugdir/common/${fullname}Plugin.class.php ]
	then
		echo Creating $plugdir/common/${fullname}Plugin.class.php
		cat $modelplugdir/common/${modelfullname}Plugin.class.php | \
		sed "s/$modelminus/$minus/g" | \
		sed "s/$modelfullname/$fullname/g" > \
		$plugdir/common/${fullname}Plugin.class.php
	fi
	if [ ! -f $plugdir/common/$minus-init.php ]
	then
		echo Creating $plugdir/common/$minus-init.php
		cat $modelplugdir/common/$modelminus-init.php | \
		sed "s/$modelminus/$minus/g" | \
		sed "s/$modelfullname/$fullname/g" > \
		$plugdir/common/$minus-init.php
	fi
	if [ ! -f $plugdir/www/index.php ]
	then
		echo Creating $plugdir/www/index.php
		cat $modelplugdir/www/index.php | \
		sed "s/$modelminus/$minus/g" | \
		sed "s/$modelfullname/$fullname/g" > \
		$plugdir/www/index.php
	fi

	if [ $dopackage -ne 0 ]
	then
		echo "Doing package"
		(cd $modelplugdir;find debian;find utils)|sort|while read debfile
		do
			if [ -d $modelminus/$debfile ]
			then
				[ -d $plugdir/$debfile ] || (echo "Making directory $plugdir/$debfile" ; mkdir $plugdir/$debfile)
			else
				if [ ! -f $plugdir/$debfile ]
				then
					echo "Creating $plugdir/$debfile"
					cat $modelminus/$debfile | \
						sed "s/$modelminus/$minus/g" | \
						sed "s/$modelfullname/$fullname/g" > \
					$plugdir/$debfile
				fi
			fi
		done
		chmod +x $plugdir/utils/*
		(cd $modelplugdir;find packaging;find translations;find etc)|sort|while read debfile
		do
			if [ -d $modelminus/$debfile ]
			then
				[ -d $plugdir/$debfile ] || (echo "Making directory $plugdir/$debfile" ; mkdir $plugdir/$debfile)
			else
				newdebfile=`echo $debfile | sed "s/$modelminus/$minus/g"`
				if [ ! -f $plugdir/$newdebfile ]
				then
					echo "Creating $plugdir/$newdebfile"
					cat $modelminus/$debfile | \
						sed "s/$modelminus/$minus/g" | \
						sed "s/$modelfullname/$fullname/g" > \
					$plugdir/$newdebfile
				fi
			fi
		done
	fi
#	if [ ! -f $plugdir/common/languages/Base.tab ]
#	then
#		echo Creating $plugdir/common/languages/Base.tab
#		cat $modelplugdir/common/languages/Base.tab | \
#		sed "s/$modelminus/$minus/g" | \
#		sed "s/$modelfullname/$fullname/g" > \
#		$plugdir/common/languages/Base.tab
#	fi

fi

