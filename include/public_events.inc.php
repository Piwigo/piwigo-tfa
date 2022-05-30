<?php
defined('TFA_PATH') or die('Hacking attempt!');

/**
 * detect current section
 */
function tfa_loc_end_section_init()
{
  global $tokens, $page, $conf;

  if ($tokens[0] == 'tfa')
  {
    $page['section'] = 'tfa';

    // section_title is for breadcrumb, title is for page <title>
    $page['section_title'] = '<a href="'.TFA_IDENTIFICATION.'">'.l10n('Two Factor Authentification').'</a>';
    $page['title'] = l10n('Two Factor Authentification');

    $page['body_id'] = 'tfa';
    $page['is_external'] = true;
  }
}

/**
 * include public page
 */
function tfa_loc_end_page()
{
  global $page;

  if (isset($page['section']) and $page['section']=='tfa')
  {
    include(TFA_PATH . 'include/tfa_validate_page.inc.php');
  }
}

/**
 * Setup the TFA at the login
*/
function tfa_try_log_user($success, $username, $password, $remember_me)
{
  global $conf;

  if ($success) 
  {
    if (is_tfa_required()) 
    {
      redirect('index.php?/tfa');
    }
  }

  return true;
}