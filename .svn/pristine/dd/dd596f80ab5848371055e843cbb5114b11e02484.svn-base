#!/bin/bash

USER=$1
PWD=$2

if mountpoint -q /mnt/pdresearch2
then
	return
else
	mount -t cifs -o username=$USER,password=$PWD,sec=ntlm,domain=UMHS,iocharset=utf8,file_mode=0777,dir_mode=0777,uid=1000,gid=1000 //10.30.214.61/UMMS-pdresearch2 /mnt/pdresearch2/
fi

