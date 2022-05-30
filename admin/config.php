<?php
defined('TFA_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Configuration tab                                                     |
// +-----------------------------------------------------------------------+

// save config
if (isset($_POST['save_config']))
{
  $conf['tfa']['on_new_machine'] = isset($_POST['on_new_machine']);
  $conf['tfa']['duration'] = intval($_POST['duration']);
  $conf['tfa']['code_tries'] = intval($_POST['code_tries']);
  
  conf_update_param('tfa', $conf['tfa']);
  $page['infos'][] = l10n('Information data registered in database');
}

// send config to template
$template->assign(array(
  'tfa' => $conf['tfa'],
  'remember' => array(
    'authorize' => $conf['authorize_remembering'],
    'length' => ((intval($conf['remember_me_length'])/60)/60)/24
  )
));

// define template file
$template->set_filename('tfa_content', realpath(TFA_PATH . 'admin/template/config.tpl'));
