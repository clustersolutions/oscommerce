<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core\Site\Admin;

use osCommerce\OM\Core\{
    Access,
    Cache,
    Hash,
    OSCOM,
    PDO,
    Registry,
    Session
};

class Controller implements \osCommerce\OM\Core\SiteInterface
{
    protected static $_default_application = 'Dashboard';
    protected static $_guest_applications = [
        'Dashboard',
        'Login'
    ];

    public static function initialize()
    {
        Registry::set('MessageStack', new MessageStack());
        Registry::set('Cache', new Cache());
        Registry::set('PDO', PDO::initialize());

        foreach (OSCOM::callDB('Shop\GetConfiguration', null, 'Site') as $param) {
            define($param['cfgkey'], $param['cfgvalue']);
        }

        Registry::set('Session', Session::load('adminSid'));
        Registry::get('Session')->start();

        if (!isset($_SESSION[OSCOM::getSite()]['secure_token'])) {
            $_SESSION[OSCOM::getSite()]['secure_token'] = Hash::getRandomString(32);
        }

        Registry::set('Language', new Language());

        if (!self::hasAccess(OSCOM::getSiteApplication())) {
            if (!isset($_SESSION[OSCOM::getSite()]['id'])) {
                if (OSCOM::getSiteApplication() != 'Login') {
                    $_SESSION[OSCOM::getSite()]['redirect_origin'] = OSCOM::getSiteApplication();

                    OSCOM::redirect(OSCOM::getLink(null, 'Login'));
                }
            } else {
                Registry::get('MessageStack')->add('header', 'No access.', 'error');

                OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
            }
        }

        Registry::set('Template', new Template());

        $application = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
        Registry::set('Application', new $application());

        $OSCOM_Template = Registry::get('Template');
        $OSCOM_Template->setApplication(Registry::get('Application'));

// HPDL move following checks elsewhere
// check if a default currency is set
        if (!defined('DEFAULT_CURRENCY')) {
            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_no_default_currency'), 'error');
        }

// check if a default language is set
        if (!defined('DEFAULT_LANGUAGE')) {
            Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
        }

        if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false)) {
            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_uploads_disabled'), 'warning');
        }

// check if Work directories are writable
        $work_dirs = [];

        foreach (['Cache', 'CoreUpdate', 'Database', 'Logs', 'Session', 'Temp'] as $w) {
            if (!is_writable(OSCOM::BASE_DIRECTORY . 'Work/' . $w)) {
                $work_dirs[] = $w;
            }
        }

        if (!empty($work_dirs)) {
            Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_work_directories_not_writable'), OSCOM::BASE_DIRECTORY . 'Work/', implode(', ', $work_dirs)), 'error');
        }

        if (!OSCOM::configExists('time_zone', 'OSCOM')) {
            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_time_zone_not_defined'), 'warning');
        }

        if (!OSCOM::configExists('dir_fs_public', 'OSCOM') || !file_exists(OSCOM::getConfig('dir_fs_public', 'OSCOM'))) {
            Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_dir_fs_public_not_defined'), 'warning');
        }

// check if the upload directory exists
        if (is_dir(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload')) {
            if (!is_writeable(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload')) {
                Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_upload_directory_not_writable'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload'), 'error');
            }
        } else {
            Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_upload_directory_non_existant'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload'), 'error');
        }
    }

    public static function getDefaultApplication(): string
    {
        return static::$_default_application;
    }

    public static function hasAccess($application): bool
    {
        return isset($_SESSION[OSCOM::getSite()]['id']) && Access::hasAccess(OSCOM::getSite(), $application);
    }

    public static function getGuestApplications(): array
    {
        return static::$_guest_applications;
    }
}
