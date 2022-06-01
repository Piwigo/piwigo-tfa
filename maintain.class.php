<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');


/**
 * This class is used to expose maintenance methods to the plugins manager
 * It must extends PluginMaintain and be named "PLUGINID_maintain"
 * where PLUGINID is the directory name of your plugin.
 */
class tfa_maintain extends PluginMaintain
{
  private $default_conf = array(
    'on_new_machine' => false,
    'code_tries' => 3,
    'duration' => 0,
    'tfa_type' => 0, // 0 : Mail, 1 : Phone, 3 : User's choice
  );
  
  function __construct($plugin_id)
  {
    parent::__construct($plugin_id); 
    
    global $prefixeTable;
    
    $this->table_login = $prefixeTable . 'tfa_login';
    $this->table_opt_key = $prefixeTable . 'tfa_opt_key';
    $this->table_demand = $prefixeTable . 'tfa_demand';
  }

  function install($plugin_version, &$errors=array())
  {
    global $conf;

    if (empty($conf['tfa']))
    {
      conf_update_param('tfa', $this->default_conf, true);
    }
    else
    {
      $old_conf = safe_unserialize($conf['tfa']);

      conf_update_param('tfa', $old_conf, true);
    }

    // add a new tabletable_machine
    pwg_query('
CREATE TABLE IF NOT EXISTS `'. $this->table_login .'` (
  `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `machine_token` varchar(32) NOT NULL DEFAULT 0,
  `method` int DEFAULT 0,
  `time` timestamp NOT NULL,
  PRIMARY KEY (`user_id`, `machine_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

//     pwg_query('
// CREATE TABLE IF NOT EXISTS `'. $this->table_opt_key .'` (
//   `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
//   `opt_key` varchar(32) NOT NULL DEFAULT 0,
//   PRIMARY KEY (`user_id`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8
// ;');

    pwg_query('
CREATE TABLE IF NOT EXISTS `'. $this->table_demand .'` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `reason` longtext,
  `time` timestamp NOT NULL,
  `machine_token` varchar(32),
  `accepted` int(1) DEFAULT 0,
  `used` int(1) DEFAULT 0,
  `admin_id` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');
  }

  /**
   * Plugin activation
   *
   * This function is triggered after installation, by manual activation or after a plugin update
   * for this last case you must manage updates tasks of your plugin in this function
   */
  function activate($plugin_version, &$errors=array())
  {
    $this->install($plugin_version, $errors);
  }

  /**
   * Plugin deactivation
   *
   * Triggered before uninstallation or by manual deactivation
   */
  function deactivate()
  {
    $this->uninstall();
  }

  /**
   * Plugin (auto)update
   *
   * This function is called when Piwigo detects that the registered version of
   * the plugin is older than the version exposed in main.inc.php
   * Thus it's called after a plugin update from admin panel or a manual update by FTP
   */
  function update($old_version, $new_version, &$errors=array())
  {
    // I (mistic100) chosed to handle install and update in the same method
    // you are free to do otherwize
    // $this->install($new_version, $errors);
  }

  /**
   * Plugin uninstallation
   *
   * Perform here all cleaning tasks when the plugin is removed
   * you should revert all changes made in 'install'
   */
  function uninstall()
  {
    // delete configuration
    conf_delete_param('tfa');

    // delete table
    pwg_query('DROP TABLE IF EXISTS`'. $this->table_login .'`;');
    pwg_query('DROP TABLE IF EXISTS `'. $this->table_opt_key .'`;');
    pwg_query('DROP TABLE IF EXISTS`'. $this->table_demand.'`;');
  }
}