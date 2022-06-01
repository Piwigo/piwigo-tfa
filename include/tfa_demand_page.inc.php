<?php
defined('TFA_PATH') or die('Hacking attempt!');

global $page, $template, $conf, $user, $tokens, $pwg_loaded_plugins;

if ($user["id"] == $conf["guest_id"])
  redirect('/identification.php');

if (isset($_POST['code'])) 
{

}

/* Template setup */
$template->assign(array(
  'TFA_PATH' => TFA_PATH,
  'TFA_ABS_PATH' => realpath(TFA_PATH).'/',
  'TFA_ACTION' => TFA_DEMAND,
  'USER_MAIL' => $user['email'],
  ));

$template->set_filename('tfa_page', realpath(TFA_PATH . 'template/tfa_demand_page.tpl'));
$template->assign_var_from_handle('CONTENT', 'tfa_page');