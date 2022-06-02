<?php

defined('TFA_PATH') or die('Hacking attempt!');

function redirect_tfa() {
    $tfa_url =
        get_root_url().'identification.php?tfa';

    redirect_html($tfa_url);
}

function get_tfa_method($userid) {
    global $conf;

    // if ($conf['tfa']['tfa_type'] < 2)
    //     return $conf['tfa']['tfa_type'];
    
    // else check if the user has a opt key in the database

    return 0;
}

function is_tfa_required() {
    
    global $conf,$user;
    
    if (!tfa_exist_login($user['id'])) return true;

    if ($conf['tfa']['on_new_machine'] && !tfa_exist_login_on_machine($user['id'], get_machine_unique_token())) return true;
    
    if ($conf['tfa']['duration'] != 0) {
        $origin = new DateTime();
        $target = new DateTime(tfa_get_last_login_date($user['id']));
        $interval = $origin->diff($target);
        if ($interval->d > $conf['tfa']['duration'])
            return true;
    } 

    return false;
}

function generate_mail_code() {
    return rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
}

function get_user_mail($username) {
    global $conf;

    $result = pwg_query('
SELECT '.$conf['user_fields']['email'].' AS mail
    FROM '.USERS_TABLE.'
    WHERE '.$conf['user_fields']['email'].' = \''.pwg_db_real_escape_string($username).'\'
    OR '.$conf['user_fields']['username'].' = \''.pwg_db_real_escape_string($username).'\'
    ;');
    
    $firstRow = pwg_db_fetch_assoc($result);

    return $firstRow['mail'];
}

function get_geoip_info() {
    $geoip_info = array("valid"=>false);

    try {
        $geoip_location = geoip_region_by_name($_SERVER['REMOTE_ADDR']);
        $geoip_info["country"] = geoip_country_name_by_name($_SERVER['REMOTE_ADDR']);
        $geoip_info["region"] = geoip_region_name_by_code($geoip_location['country_code'], $geoip_location['region']);
        $geoip_info["valid"] = true;
    } catch (\Throwable $th) {
        //GeoIp is not installed
    }

    return $geoip_info;
}

function get_machine_unique_token() {
    return md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
}

function send_tfa_mail($username, $code) {

    global $conf;

    include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

    $mail_config = array(
        'subject' => '['.$conf['gallery_title'].'] '.l10n('Login code'),
        'mail_title' => l10n('Login code'),
        'mail_subtitle' => l10n('Enter this code on the gallery %s', $conf['gallery_title']),
        'content_format' => 'text/html',
        'from' => array(
          'name' => 'piwigo-2FA-plugin',
          'email' => get_webmaster_mail_address(),
          )
        );

    $mail_tpl = array(
    'filename' => 'mail',
    'dirname' => realpath(TFA_PATH . 'template'),
    'assign' => array(
        'CODE' => $code,
        'DATE' => format_date(new DateTime('now'), ["day", "month", "year", "time"]),
        //'GEO_IP_INFO' => get_geoip_info(),
        )
    );

    try {
        return pwg_mail(
            array(
                'name' => $username,
                'email' => get_user_mail($username),
                ),
            $mail_config,
            $mail_tpl
        );
    } catch (Exception $ex) {
        echo($ex);
        return false;
    }
}

function clean_tfa_session() {
    unset($_SESSION['TFA_opt_key']);
    unset($_SESSION['TFA_mail_code']);
    unset($_SESSION['TFA_code_tries']);

    trigger_notify('loc_end_identification');
}

function askDemand($reason) {
    global $user, $conf;

}

// Database part

function tfa_exist_login($userid) {
    return pwg_query('
SELECT * FROM '.TFA_TABLE_LOGIN.' WHERE user_id = '.$userid.';
    ')->num_rows != 0;
}

function tfa_exist_login_on_machine($userid, $machineToken) {
    return pwg_query('
SELECT * FROM '.TFA_TABLE_LOGIN.' WHERE user_id = '.$userid.' AND machine_token = "'.$machineToken.'";
    ')->num_rows != 0;
}

function tfa_get_last_login_date ($userid) {
    $result = pwg_query('
SELECT time FROM '.TFA_TABLE_LOGIN.' WHERE user_id = '.$userid.' ORDER BY time DESC LIMIT 1;
    ');
    $row = pwg_db_fetch_assoc($result);
    return $row['time']; 
}

function tfa_create_login($userid, $machineToken, $tfamethod = 0) {
    pwg_query('
REPLACE INTO '.TFA_TABLE_LOGIN.' (user_id, machine_token, time, method) VALUES ('.$userid.', "'.$machineToken.'", CURRENT_TIME, '.$tfamethod.');
    ');
}

function tfa_create_demand($userid, $reason, $machineToken) {
    pwg_query('
INSERT INTO '.TFA_TABLE_DEMAND.' (user_id, reason, machine_token, time) VALUES ('.$userid.', "'.$reason.'", "'.$machineToken.'", CURRENT_TIME);
    ');
}