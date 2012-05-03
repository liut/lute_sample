#!/bin/sh

## $Id$

PATH=/sbin:/usr/sbin:/bin:/usr/bin:/usr/local/bin
OS=`uname -s`
echo $OS
if [ $OS = "Darwin" ]; then
	PREFIX=/opt
else
	PREFIX=/usr
fi

if [ ! -d $PREFIX ]; then
	echo "$PREFIX not exists"
	exit 1;
fi

if [ ! -e ${PREFIX}/sproot ]; then
	echo "path: ${PREFIX}/sproot not exists"
	exit 1;
fi

SPROOT=${PREFIX}/sproot

[ -e /sproot ] || ln -s ${SPROOT} /sproot

## LOG
LOG_DIR=${SPROOT}/logs
if [ ! -d $LOG_DIR ]; then
	mkdir $LOG_DIR
fi
if [ -d $LOG_DIR ]; then
	chmod a+w $LOG_DIR
	## Logs: dirs
	for dir in www my man info sto api stat bbs union
	do
		[ -e $LOG_DIR/$dir ] || mkdir $LOG_DIR/$dir
		if [ -d $LOG_DIR/$dir ]; then
			chmod a+w $LOG_DIR/$dir
		fi
	done
fi
WEB_DIR=${SPROOT}/web
CACHE_DIR=${WEB_DIR}/cache

## Cache
[ ! -d $CACHE_DIR -a ! -e $CACHE_DIR ] && mkdir $CACHE_DIR
if [ -d $CACHE_DIR ]; then
	chmod a+w $CACHE_DIR
	## Cache: dirs
	for dir in api page logs view thumb images eggs
	do
		[ -e $CACHE_DIR/$dir ] || mkdir $CACHE_DIR/$dir
		if [ -d $CACHE_DIR/$dir ]; then
			chmod a+w $CACHE_DIR/$dir
		fi
	done
fi

## Cache: View
[ -e $CACHE_DIR/view ] || mkdir $CACHE_DIR/view
if [ -d $CACHE_DIR/view ]; then
	chmod a+w $CACHE_DIR/view
	[ -e $CACHE_DIR/view/cache ] || mkdir $CACHE_DIR/view/cache
	chmod a+w $CACHE_DIR/view/cache
	[ -e $CACHE_DIR/view/tpl_c ] || mkdir $CACHE_DIR/view/tpl_c
	chmod a+w $CACHE_DIR/view/tpl_c
fi

## config
if [ ! -L ${SPROOT}/config -a -d ${SPROOT}/config_prd ]; then
	CWD=$PWD
	cd ${SPROOT} && ln -s config_prd config
	cd $CWD

fi

## php.ini
if [ $OS = "FreeBSD" ]; then
	[ -d /var/log/php ] || mkdir /var/log/php
	[ -d /var/log/php ] && chmod a+w /var/log/php

	LOCALBASE="/usr/local"
	CONF_DIST="${LOCALBASE}/etc/php.ini-development"
	[ -f ${CONF_DIST} -a ! -e ${LOCALBASE}/etc/php.ini ] && cp ${CONF_DIST} ${LOCALBASE}/etc/php.ini

	PHP_SCAN_DIR=${LOCALBASE}/etc/php
	if [ -d ${PHP_SCAN_DIR} -a -e ${SPROOT}/config/php/web.ini ]; then
		install -b -m 0644 ${SPROOT}/config/php/web.ini ${PHP_SCAN_DIR}
	fi
fi
if [ $OS = "Darwin" ]; then
	PHP_SCAN_DIR=/opt/local/var/db/php5
	if [ -d ${PHP_SCAN_DIR} -a -e ${SPROOT}/config/php/web.ini ]; then
		install -b -m 0644 ${SPROOT}/config/php/web.ini ${PHP_SCAN_DIR}
	fi
	
fi

## writable dir
[ -e $WEB_DIR/data ] || mkdir $WEB_DIR/data
if [ -d $WEB_DIR/data ]; then
	chmod a+w $WEB_DIR/data
fi

#[ -e $WEB_DIR/upload ] || mkdir $WEB_DIR/upload
#if [ -d $WEB_DIR/upload ]; then
#	chmod a+w $WEB_DIR/upload
#fi


