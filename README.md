# Statamic Shell Backup
=====================

A simple shell based flat file archiving system.  This system was initially built for use with Rackspace Cloud Sites / Rackspace Cloud Files, but can be easily customize to work with other hosts.

## Getting Started


### Installation
Copy the entire "statamic-shell-backup" folder to the root directory of the Cloud Site you would like to backup (not the webroot)

### Configuration

#### Configuring the shell script (backup_site.sh)
If you are installing this system on a website running under a Rackspace Cloud Site account, modifying this file should not be necessary (unless you want to of course).  If you are installing this system on any other host, the following information should allow you to get this file configured very quickly.


##### Variables:
**this_directory** - Shouldn't need to change anything here.  This is getting the directory path for this particular file (e.g. /path/to/my/statamic-backup-system)

**root_path** - This also shouldn't need to be changed.  This is getting the path to the parent directory of the Statamic Backup System (e.g. /path/to/my)

**directory_to_backup** - This variable sets the direct path to the specific directory you would like to backup.  In most situations, this would be your webroot (where all of your site files are stored).
If you are setting this up on a site not hosted by Rackspace, this will most likely need to be changed.
To change the directory that is being backed up when the system runs, modify the default '/web/content' path so it aligns with your specific webroot.
E.g.

**If your webroot is located at:**

  /path/to/my/httpdocs/my-site

**Change the directory_to_backup variable to:**

  directory_to_backup="$root_path/httpdocs/my-site"

**date** - This variable is used as part of the naming convention for the backup files that are created.  By default, the date string will appear as: YYYY-MM-DD-HH-SS
Feel free to change this to what ever you would like.  Please note however, to ensure that the backup is run, the file is saved, and no files are overwritten, you need to be sure
that the date string is completely unique. 

E.g.

If you change the date variable formatting to YYYY-MM-DD and you run your backups twice a day, when the system runs the second time for the day, you may end up overwriting the first backup file 
you created on that day.

**backup_name** - Simply stated - this is the name that will be assigned to the backup file that is created once the system has run.  By default backup names are generated as follows: backup.YYYY-MM-DD-HH-SS.zip


##### Processes:
There are two processes you can turn off/on within the backup_site.sh shell script which are:

1.  Running the upload to cloud files task - When the shell script is run, this by default this task will be executed.  If this task is unnecessary (i.e. you don't have a Rackspace Cloud Files account, or you simply
don't want to send the backup files to the account), you can disable it by commenting out the line of code below (comments are declared with a "#"). Note that this process can also be disabled in the config.php 
file settings (discussed in the next section).

  **php $send_to_cloud_path $this_directory/"backups"/$backup_name $backup_name**

**IMPORTANT NOTE:**  If you disable this process, be sure to also disable the process below.  If you don't, you will be deleting your backup files immediately after they have been created.


2.  Removing backups from your file system after the backup process has run - In order to backup your site, a compressed/archive file must first be created.  If you are running this backup system
and saving the archives to your Rackspace Cloud Files, it's probably not necessary to keep these files stored on your Cloud Site account.  This system is designed to automatically delete the backup file from your
from your web after it has been uploaded to your Cloud Files Account.  That said, you can disable this process by commenting out the following line of code (comments are declared with a "#").

  **rm $this_directory/"backups"/$backup_name**


#### Configuring the Cloud Files backup transfer (/php/config.php)
Before the system will send your backup files to your Rackspace Cloud Files account, you must first configure it to do so.  To do this, open the config.php file (located in statamic-shell-backup/php/) and configure the
following settings:

##### General Settings

  **username** - Enter the username for your Rackspace Cloud File Account
  **api_key** - Enter the API
  **container_name** - Enter the name of the container you would like these backups to be stored in
  **send_to_rackspace** - Specify whether you would like to send these backups to your rackspace cloud files account (true or false)
  **auth_url** - If you have a US based rackspace account - leave this as is.  If you have a UK Based Rackspace Account uncomment the following variable: 
  //$auth_url = "https://lon.identity.api.rackspacecloud.com/v2.0/";
                    

##### Customized Settings - Backup Tasks
If you are using the system in conjunction with Rackspace Cloud Files, it's also equiped with some settings to help keep your archives nice and tidy.  Configuring the variables below will enable
the system to automatically delete archives that are no longer needed.  This will help ensure that 2 years down the road, you don't have to sift through 730 backup files to find the one you are
looking for (or so you don't have to take the time to delete 700 backups which are no longer relevant).

  **prune_old_backups** - Specify true if you would like the system to delete up old backup files.  Set to false if you would to keep all backup files
  **prune_schedule** - Set to weekly to delete backup files on a weekly basis.  Set to monthly to delete backup files on a monthly basis 
  **number_of_backups_to_keep** - Specify the number of backups to keep based on your prune scheduled
  Example:  A prune_schedule of weekly with a value of 2 set here will delete all but 2 backups for each week.

**NOTE:** pruning will not delete the backup files in the current week or month.  Only past weeks/months backup files will be deleted here

###### Do Not Change:
  **backup_file** - This is configured by the backup_script.sh file
  **backup_file_name** - This is conifugred by the backup_script.sh file


#### Turning your backup system on
In order to run the backup system, you'll need to create a CRON Job which will executes backup_site.sh on a schedule that you determine.  Once the CRON Job has been configured and enabled, the system will take care of the rest.
