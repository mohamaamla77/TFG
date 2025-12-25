#!/bin/bash

FECHA=$(date +%F)
tar -czf /mnt/backup_nas/backup_$FECHA.tar.gz /var/www/html