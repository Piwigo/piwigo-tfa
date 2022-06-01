<?php
defined('TFA_PATH') or die('Hacking attempt!');

/**
 * detect current section
 */
function tfa_loc_end_section_init()
{
  global $tokens, $page, $conf;

  if ($tokens[0] == 'tfa-setup')
  {
    $page['section'] = 'tfa-setup';

    // section_title is for breadcrumb, title is for page <title>
    $page['section_title'] = '<a href="'.TFA_IDENTIFICATION.'">'.l10n('Two factor authentification setup').'</a>';
    $page['title'] = l10n('Two factor authentification setup');

    $page['body_id'] = 'tfa-setup';
    $page['is_external'] = true;
  } 
}

/**
 * include public page
 */
function tfa_loc_end_page()
{
  global $page;

  if (isset($page['section'])) 
  {
    if ($page['section']=='tfa-setup')
    {
      include(TFA_PATH . 'include/tfa_setup_page.inc.php');
    }

  }
}

/**
 * Redirect to the TFA at the login
*/
function tfa_try_log_user($success, $username, $password, $remember_me)
{
  global $conf;

  if ($success) 
  {
    if (is_tfa_required()) 
    {
      redirect_tfa();
    }
  }

  return true;
}