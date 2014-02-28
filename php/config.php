<?php

  $settings = array();

  //CloudFiles Container Configuration...
  $settings['username']       = "XXXXXXXX"; // username
  $settings['api_key']        = "XXXXXXXX"; // api key
  $settings['container_name'] = "XXXXXXXX";  // container name

  //set this to false if you don't want to send the files to rackspace cloud
  $settings['send_to_rackspace'] = true;

  //For US Based Accounts...
  $settings['auth_url'] = "https://identity.api.rackspacecloud.com/v2.0/";

  //For UK Based Accounts...
  //$settings['auth_url'] = "https://lon.identity.api.rackspacecloud.com/v2.0/";
  
  /**
  *
  * Rackspace Cloud Backup Tasks
  *
  * These settings will allow us to clean up old backup files automatically in our CloudFiles Container
  * This will help keeps things nice and tidy
  *
  *  Note:  Do not configure these settings if you are not planning on sending the backup files to a Rackspace CloudFiles Container
  *
  **/

  //Set this to false to keep all backup files in your cloud files container
  $settings['prune_old_backups'] = true;

  //Define the schedule on which to prune files - accepts a value of weekly or monthly
  $settings['prune_schedule'] = 'weekly';

  //Define the number of files to keep within your prune schedule
  //e.g. - With a weekly prune schedule and value of 2 for the number_of_backups_to_keep - 2 backups files will be kept in your CloudFiles container for each week
  $settings['number_of_backups_to_keep'] = 2;

  //Do not change...
  $settings['backup_file'] = $argv[1];
  $settings['backup_file_name'] = $argv[2];

  //And away we go!
  include(dirname(__FILE__) . '/send_to_cloud.php');
  $run = new CoreBackup($settings);

?>
