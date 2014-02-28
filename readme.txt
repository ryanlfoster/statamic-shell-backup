Included in this package are the core files for the Statamic Backup System. 

This system is designed, for the most part, to be plug and play and requires minimal configuration.

To get everything up and running quickly please follow the instructions below:


Instructions:

	Step 1 - Installation
		Copy the entire "backup_system" folder to the root directory of the Cloud Site you would like to backup

	Step 2 - Configure config.php
		In the folder titled "php" of this package, you should see a file titled "config.php"
		Open this file and configure the following settings:
			username     				Enter the username for your Rackspace Cloud File Account
			api_key          		Enter the API 
			container_nane			Enter the name of the container you would like these backups to be stored in
			send_to_rackspace		Specify whether you would like to send these backups to your rackspace cloud files account (true or false)
			auth_url						If you have a US based rackspace account - leave this as is.  If you have a UK Based Rackspace Account
													uncomment the following variable: 
															//$auth_url = "https://lon.identity.api.rackspacecloud.com/v2.0/";
															
			CloudFiles Backup Tasks
			
			prune_old_backups						Specify true if you would like the system to delete up old backup files.  Set to false if you would to keep all backup files
			prune_schedule 							Set to weekly to delete backup files on a weekly basis.  Set to monthly to delete backup files on a monthly basis
			number_of_backups_to_keep		Specify the number of backups to keep based on your prune scheduled
																		Example:  A prune_schedule of weekly with a value of 2 set here will delete all but 2 backups for each week.
					NOTE: pruning will not delete the backup files in the current week or month.  Only past weeks/months backup files will be deleted here
			
			Do Not Change:
				backup_file 			This is configured by the backup_script.sh file
				backup_file_name	This is conifugred by the backup_script.sh file


	Step 3 - Configure CRON Job in Rackspace Cloud
		Open the Cloud Site Control Panel for the site you would like to back up
		Click on the 'Features' tab
		Click on the 'Add New Task' once the Features window has opened
		Configure your new scheduled task
			Under the "Command Language" select box, be sure to choose "perl"
			Under the "Command to Run" enter "backup_system/backup_site.sh"
				Where 'backup_system' is the name of the primary backup_system directory 
					(this will be backup_system unless you have changed it to something else)



	At this point, your backup system should be fully configured and ready to go!
	