#!/bin/bash

# Check log to see if loris has been accessed in last hour
FILE=$(find /var/log/apache2/loris-access.log -mmin -59)

# If hasn't been accessed, unmount the drive
if [ -z "$FILE" ]
then
	umount -l /mnt/pdresearch2
fi
