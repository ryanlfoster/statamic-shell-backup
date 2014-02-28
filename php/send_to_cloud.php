<?php

require(dirname(__FILE__) . '/core/lib/php-opencloud.php');
  
class CoreBackup {

  public $connection;
  public $ostore;
  public $container;
  public $settings;
  
/**
 *
 *  Constructor...
 *
**/
  function __construct($config_settings)
  {
    //set some default variables...
    $this->settings = $config_settings;
  
    if($this->settings['send_to_rackspace'] == false)
    {
      return;
    }
    
    $this->rackspace_connect();
    $this->build_container();
    $this->send_files_to_rackspace();
    $this->delete_old_backup_files();
    
    // Cleaning things up a bit...
    unset($this->connection);
    unset($this->ostore);
    unset($this->container);
    
    return;
  } //end __constructor()
  
  
/**
 *
 *  Connecting to Rackspace...
 *
**/
  function rackspace_connect()
  {
    // Connect to Rackspace
    $this->connection = new \OpenCloud\Rackspace(
      $this->settings['auth_url'],
      array(
        'username' => $this->settings['username'],
        'apiKey' => $this->settings['api_key']
      )
    );
  
    $this->connection->SetDefaults('ObjectStore', 'cloudFiles', 'DFW', 'publicURL');
    $this->ostore = $this->connection->ObjectStore(); // uses default values
  } //end rackspace_connect()


/**
 *
 *  Build the container
 *
**/
  function build_container()
  {
    $this->container = $this->ostore->Container();
    $this->container->Create(array('name' => $this->settings['container_name']));
  } //end build_container()
  

/**
 *
 *  Sending the files to rackspace....
 *
**/
  function send_files_to_rackspace()
  {
    // upload file to Rackspace
    $file_info = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $file_info->file($this->settings['backup_file']);

    $backup_object = $this->container->DataObject();
    $backup_object->Create(array('name' => $this->settings['backup_file_name'], 'content_type' => $mime_type), $this->settings['backup_file']);
  } //end send_files_to_rackspace()
  
  
/**
 *
 * Delete old backups files...
 *
 *
**/
  function delete_old_backup_files()
  {

    if($this->settings['prune_old_backups'] == true && $this->settings['number_of_backups_to_keep'] > 0)
    {
      $this_container = $this->ostore->Container($this->settings['container_name']);
      $obj_list = $this_container->ObjectList();
      
      while ($item = $obj_list->Next())
      {
        $container_objects[] = array(
          'name' => $item->name,
          'content_type' => $item->content_type,
          'bytes' => $item->bytes,
          'modified' => $item->last_modified
        );
      }
      usort($container_objects, array($this, 'date_compare'));
  
      if($this->settings['prune_schedule'] == 'weekly')
      {
        $object_count = 0;
        //We're cleaning up old backups on a weekly basis.  Thus, we only want X number of backups to be saved per week.
        foreach($container_objects as $object)
        {
          if(!isset($comparrison_obj) || empty($comparrison_obj)) 
          {
            $comparrison_obj = $object;
            $object_day_of_week = date('N', strtotime($comparrison_obj['modified']));
          }
          
          if(strtotime($comparrison_obj['modified']) >= strtotime('Last Sunday', time()))
          {
            //we want to keep all backups for the current week.
            break;
          }
          
          if(strtotime($object['modified']) <= date("U", strtotime($comparrison_obj['modified'] . ' + ' . (6 - $object_day_of_week) .' days')))
          {
            if($comparrison_obj['modified'] != $object['modified'])
            {
              $object_count++;
            }
            
            if($object_count >= $this->settings['number_of_backups_to_keep'])
            {
              $objects_to_delete[] = $comparrison_obj;
              $comparrison_obj = $object;
              $object_day_of_week = date('N', strtotime($comparrison_obj['modified']));
            }
          }
          else
          {
            $object_count = 0;
            $comparrison_obj = $object;
            $object_day_of_week = date('N', strtotime($comparrison_obj['modified']));          
          }
          
        }
              
      }
      elseif($this->settings['prune_schedule'] == 'monthly')
      {
        $object_count = 0;
        //We're cleaning up old backups on a weekly basis.  Thus, we only want X number of backups to be saved per week.
        foreach($container_objects as $object)
        {
          if(!isset($comparrison_obj)) 
          {
            $comparrison_obj = $object;
            $object_day_of_month = date('j', strtotime($comparrison_obj['modified']));
          }
          
          if(strtotime($comparrison_obj['modified']) >= strtotime('first day of this month', time()))
          {
            //we want to keep all backups for the current month.
            break;
          }
          
          if(strtotime($object['modified']) <= date("U", strtotime($comparrison_obj['modified'] . ' + ' . (date('t', strtotime($comparrison_obj['modified']) - $object_day_of_week) .' days') )))
          {
          
            if($comparrison_obj['modified'] != $object['modified'])
            {
              $object_count++;
            }
            
            if($object_count >= $this->settings['number_of_backups_to_keep'])
            {
              $objects_to_delete[] = $comparrison_obj;
              $comparrison_obj = $object;
              $object_day_of_week = date('W', strtotime($comparrison_obj['modified']));
            }
          }
          else
          {
            $object_count = 0;
            $comparrison_obj = $object;
            $object_day_of_week = date('W', strtotime($comparrison_obj['modified']));          
          }
          
        }
      }
      $this->delete_objects($this_container, $objects_to_delete);
    }

    return;
    
  } //end delete_old_backup_files()


/**
 *
 * Delete old backups files...
 *
 *
**/
  function delete_objects($this_container, $objects_to_delete)
  {
    foreach($objects_to_delete as $del_object)
    {
      $this_container->DataObject($del_object['name'])->Delete();    
    }
  } //end delete_objects()
   

/**
 *
 * A little helper to sort container objects by date...
 *
 *
**/
  function date_compare($a, $b)
  {
    $t1 = strtotime($a['modified']);
    $t2 = strtotime($b['modified']);
    return $t1 - $t2;
  }  
    
} //end class  

?>