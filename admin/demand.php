<?php
defined('TFA_PATH') or die('Hacking attempt!');

// +-----------------------------------------------------------------------+
// | Home tab                                                              |
// +-----------------------------------------------------------------------+

// send variables to template
$template->assign(array(
  'tfa' => $conf['tfa'],
  'INTRO_CONTENT' => load_language('intro.html', TFA_PATH, array('return'=>true)),
  ));

// define template file
$template->set_filename('tfa_content', realpath(TFA_PATH . 'admin/template/home.tpl'));
