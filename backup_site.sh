#!/bin/sh

#Set Directory Information....
this_directory=$(dirname -- $(readlink -fn -- "$0"))
root_path="$(dirname "$this_directory")"
directory_to_backup="$root_path/web/content"
send_to_cloud_path="$this_directory/php/config.php"

#Set the backup file name
date=`date '+%F-%H-%M'`
backup_name="backup.$date.zip"

#Backup Site
zip -r $this_directory/"backups"/$backup_name $directory_to_backup

#Upload your files to cloud files.
#First argument is the location of the backup file, second argument is the name to be used when uploaded
php $send_to_cloud_path $this_directory/"backups"/$backup_name $backup_name

#After your backup has been uploaded, remove the zip file from the filesystem.
rm $this_directory/"backups"/$backup_name 
