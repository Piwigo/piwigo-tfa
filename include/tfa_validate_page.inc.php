<?php
include_once(PHPWG_ROOT_PATH.'include/phpmailer/Exception.php');
defined('TFA_PATH') or die('Hacking attempt!');

global $page, $template, $conf, $user, $tokens, $pwg_loaded_plugins;

if (!is_a_guest())
  redirect('/identification.php');

$success = false;

$TFA_method = get_tfa_method($user['id']);

if (isset($_POST['code'])) 
{
  if ($TFA_method == 0)
  {
    if ($_POST['code'] == $_SESSION['TFA_mail_code'])
    {
      $success = true;
    } 
    else
    {
      $_SESSION["TFA_code_tries"] =  $_SESSION["TFA_code_tries"] - 1;
      $page["errors"][] = l10n("Code incorrect, %d tries left", $_SESSION["TFA_code_tries"]);
    }
  }
}

if (!$success) {

  if (isset($_SESSION["TFA_mail_code"]) && $TFA_method == 0 && $_SESSION["TFA_code_tries"] <= 0) {
    clean_tfa_session();
    $page["errors"][] = l10n("All tries used, a new code has been generated.");
  }

  if ($TFA_method == 0 && !isset($_SESSION["TFA_mail_code"]))
  {
    $_SESSION["TFA_mail_code"] = generate_mail_code();
    $_SESSION["TFA_code_tries"] = $conf['tfa']['code_tries'];
    
    if (!send_tfa_mail($user['username'], $_SESSION["TFA_mail_code"]))
    {
      //If the mail send fail
      $success = true;
    }
  }
}

// echo $_SESSION["TFA_mail_code"];

if ($success) 
{
  tfa_create_login($user['id'], get_machine_unique_token(), 0);
  clean_tfa_session();
  redirect('index.php');
}

/* Template setup */
$template->assign(array(
  // this is useful when having big blocks of text which must be translated
  // prefer separated HTML files over big lang.php files
  'TFA_PATH' => TFA_PATH,
  'TFA_ABS_PATH' => realpath(TFA_PATH).'/',
  'TFA_ACTION' => TFA_IDENTIFICATION,
  'TFA_DEMAND' => TFA_DEMAND,
  'TFA_METHOD' => $TFA_method,
  'USER_MAIL' => "guetzac@gmail.com",
  ));

$template->set_filename('tfa_page', realpath(TFA_PATH . 'template/tfa_validate_page.tpl'));
$template->assign_var_from_handle('CONTENT', 'tfa_page');