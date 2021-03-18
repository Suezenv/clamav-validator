#!/bin/bash



#echo "Starting the update daemon"
#/usr/bin/freshclam -d -c 6

echo "Starting clamav daemon"
/usr/sbin/clamd -c /etc/clamav/clamd.conf
echo "service started"