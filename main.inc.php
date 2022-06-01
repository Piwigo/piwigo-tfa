<?php
/*
Plugin Name: TFA
Version: auto
Description: Add a two factor authentification to Piwigo
Plugin URI: auto
Author: Zacharie Guet
Author URI: zacharieg.github.io/
Has Settings: true
*/

/**
 * This is the main file of the plugin, called by Piwigo in "include/common.inc.php" line 137.
 * At this point of the code, Piwigo is not completely initialized, so nothing should be done directly
 * except define constants and event handlers (see http://piwigo.org/doc/doku.php?id=dev:plugins)
 */

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

if (basename(dirname(__FILE__)) != 'TFA')
{
  add_event_handler('init', 'tfa_error');
  function tfa_error()
  {
    global $page;
    $page['errors'][] = '2FA folder name is incorrect, uninstall the plugin and rename it to "TFA"';
  }
  return;
}


// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+
global $prefixeTable;

define('TFA_ID',      basename(dirname(__FILE__)));
define('TFA_PATH' ,   PHPWG_PLUGINS_PATH . TFA_ID . '/');
define('TFA_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . TFA_ID);
define('TFA_DIR',     PHPWG_ROOT_PATH . PWG_LOCAL_DIR . 'tfa/');

define('TFA_IDENTIFICATION',  get_absolute_root_url() . "/identification.php?tfa");
define('TFA_DEMAND',  get_absolute_root_url() . "/identification.php?tfa-demand");
define('TFA_SETUP',  get_absolute_root_url() . make_index_url(array('section' => 'tfa-setup')) . '/');


define('TFA_TABLE_LOGIN', $prefixeTable . 'tfa_login');
define('TFA_TABLE_OPT_KEY', $prefixeTable . 'tfa_opt_key');
define('TFA_TABLE_DEMAND', $prefixeTable . 'tfa_demand');

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+

$public_event_files = TFA_PATH.'include/public_events.inc.php';
$tfa_identification = TFA_PATH.'include/tfa_identification.php';



// init the plugin
add_event_handler('init', 'tfa_init');

// Integrate 2FA identification system to identifcation.php
add_event_handler('loc_begin_identification', 'tfa_identification', EVENT_HANDLER_PRIORITY_NEUTRAL, $tfa_identification);

// init the 2FA login after the normal login
add_event_handler('try_log_user', 'tfa_try_log_user', PHP_INT_MAX, $public_event_files);

// add 2FA public page
add_event_handler('loc_end_section_init', 'tfa_loc_end_section_init', EVENT_HANDLER_PRIORITY_NEUTRAL, $public_event_files);
add_event_handler('loc_end_index', 'tfa_loc_end_page', EVENT_HANDLER_PRIORITY_NEUTRAL, $public_event_files);

//add_event_handler('loc_end_index', 'tfa_test');
function tfa_test () {
  global $user;
  echo '<script>alert("status = '.TFA_IDENTIFICATION.'");</script>';
}

if (defined('IN_ADMIN'))
{
  // file containing all admin handlers functions
  $admin_file = TFA_PATH . 'include/admin_events.inc.php';
}
else
{
  // file containing all public handlers functions
  $public_file = TFA_PATH . 'include/public_events.inc.php';
}

function tfa_init()
{
  global $conf, $user;

  // load plugin language file
  load_language('plugin.lang', TFA_PATH);

  require_once(TFA_PATH . 'include/functions.inc.php');

  // prepare plugin configuration
  $conf['tfa'] = safe_unserialize($conf['tfa']);

  // Override user status if the TFA is needed
  if (!is_a_guest() && is_tfa_required()) {
    $user['status'] = 'guest';
  }
}
