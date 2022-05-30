<?php
/**
 * This is the main administration page, if you have only one admin page you can put
 * directly its code here or using the tabsheet system like bellow
 */

defined('TFA_PATH') or die('Hacking attempt!');

global $template, $page, $conf;

// get current tab
$page['tab'] = isset($_GET['tab']) ? $_GET['tab'] : $page['tab'] = 'config';

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$tabsheet = new tabsheet();
$tabsheet->set_id('tfa');

$tabsheet->add('config', l10n('Configuration'), TFA_ADMIN . '-config');
//$tabsheet->add('demand', l10n('Demands'), TFA_ADMIN . '-demand');
$tabsheet->select($page['tab']);
$tabsheet->assign();


// include page
include(TFA_PATH . 'admin/' . $page['tab'] . '.php');

// template vars
$template->assign(array(
  'TFA_PATH'=> TFA_PATH, // used for images, scripts, ... access
  'TFA_ABS_PATH'=> realpath(TFA_PATH), // used for template inclusion (Smarty needs a real path)
  'TFA_ADMIN' => TFA_ADMIN,
  ));

// send page content
$template->assign_var_from_handle('ADMIN_CONTENT', 'tfa_content');
