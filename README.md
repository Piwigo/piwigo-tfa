# TFA : Two-factor Authentication plugin for Piwigo

*Warning ! This plugin is currently in BETA, it may have bugs and missing feature.*

* Internal name: `TFA` (in uppercase, directory name in `plugins/`)

Make sure that your piwigo admin user email address is correct and that you have access to it before activating this plugin !

Allow a Two-factor Authentication by mail (by a 6 digit temporary code). 

Work with a correct SMTP server linked to piwigo.

Example of configuration in `local\config\config.inc.php`, with the freebox smtp.
```
$conf['smtp_host'] = 'smtp.free.fr:465';
$conf['smtp_user'] = 'yourmailadress';
$conf['smtp_password'] = 'yourpassword';
$conf['smtp_secure'] = 'ssl';
```
