<?php
// Code integrated to index.php
function tfa_identification() {
  global $page, $template, $conf, $user, $tokens, $pwg_loaded_plugins;
  include_once(PHPWG_ROOT_PATH.'include/phpmailer/Exception.php');

  // If the user is unknown and try to access the tfa page
  if ($user['id'] == $conf['guest_id'] && isset($_GET['tfa']))
    access_denied(); // return to login page
  
  // If the user is known and access to the login page
  if ($user['id'] != $conf['guest_id'] && !isset($_GET['tfa']))
    redirect_tfa(); // redirect to 2FA page
  
  if (isset($_GET['tfa']))
  {
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
          echo("Send mail fail...");
          //If the mail send fail
          $success = true;
        }
      }
    }
  
    // TO DEBUG
    // echo $_SESSION["TFA_mail_code"];
  
    if ($success) 
    {
      tfa_create_login($user['id'], get_machine_unique_token(), 0);
      clean_tfa_session();
      redirect('index.php');
    }
    
    /* Template setup */
    $template->assign(array(
      'TFA_PATH' => TFA_PATH,
      'TFA_ABS_PATH' => realpath(TFA_PATH).'/',
      'TFA_ACTION' => TFA_IDENTIFICATION,
      'TFA_DEMAND' => TFA_DEMAND,
      'TFA_METHOD' => $TFA_method,
      'USER_MAIL' => $user['email'],
      ));
    
    $template->set_filename('tfa_page', realpath(TFA_PATH . 'template/tfa_identification.tpl'));
    $template->assign_var_from_handle('TFA_IDENTIFICATION_PAGE', 'tfa_page');

    $template->set_prefilter('identification', 'tfa_identification_prefilter');
  } 

  function tfa_identification_prefilter($content)
  {
    $pattern = '/<form(.*\n)+.*<\/form>/m';
    $replace = '{$TFA_IDENTIFICATION_PAGE}';

    return preg_replace($pattern, $replace, $content);
  }
}