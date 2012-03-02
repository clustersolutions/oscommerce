<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\Cache;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\PDO;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Session;

  class Controller implements \osCommerce\OM\Core\SiteInterface {
    protected static $_default_application = 'Dashboard';
    protected static $_guest_applications = array('Dashboard', 'Login');

    public static function initialize() {
      Registry::set('MessageStack', new MessageStack());
      Registry::set('Cache', new Cache());
      Registry::set('PDO', PDO::initialize());

      foreach ( OSCOM::callDB('Shop\GetConfiguration', null, 'Site') as $param ) {
        define($param['cfgkey'], $param['cfgvalue']);
      }

      Registry::set('Session', Session::load('adminSid'));
      Registry::get('Session')->start();

      Registry::get('MessageStack')->loadFromSession();

      Registry::set('Language', new Language());

      if ( !self::hasAccess(OSCOM::getSiteApplication()) ) {
        Registry::get('MessageStack')->add('header', 'No access.', 'error');

        OSCOM::redirect(OSCOM::getLink(null, OSCOM::getDefaultSiteApplication()));
      }

      Registry::set('Template', new Template());

      $application = 'osCommerce\\OM\\Core\\Site\\Admin\\Application\\' . OSCOM::getSiteApplication() . '\\Controller';
      Registry::set('Application', new $application());

      $OSCOM_Template = Registry::get('Template');
      $OSCOM_Template->setApplication(Registry::get('Application'));

      $OSCOM_Template->setValue('html_text_direction', Registry::get('Language')->getTextDirection());
      $OSCOM_Template->setValue('html_lang', OSCOM::getDef('html_lang_code')); // HPDL A better solution is to define the ISO 639-1 value at the language level
      $OSCOM_Template->setValue('html_character_set', Registry::get('Language')->getCharacterSet());
      $OSCOM_Template->setValue('html_page_title', 'OSCOMMERCE' . ($OSCOM_Template->hasPageTitle() ? ': ' . $OSCOM_Template->getPageTitle() : ''));
      $OSCOM_Template->setValue('default_site_application', OSCOM::getDefaultSiteApplication());
      $OSCOM_Template->setValue('current_site_application', OSCOM::getSiteApplication());
      $OSCOM_Template->setValue('batch_size', MAX_DISPLAY_SEARCH_RESULTS);
      $OSCOM_Template->setValue('tax_decimal_places', TAX_DECIMAL_PLACES);
      $OSCOM_Template->setValue('content_page_file', $OSCOM_Template->getPageContentsFile());
      $OSCOM_Template->setValue('template_header_file', $OSCOM_Template->getTemplateFile('header.php'));
      $OSCOM_Template->setValue('template_footer_file', $OSCOM_Template->getTemplateFile('footer.php'));
      $OSCOM_Template->setValue('template_has_header', $OSCOM_Template->hasPageHeader());
      $OSCOM_Template->setValue('template_has_footer', $OSCOM_Template->hasPageFooter());
      $OSCOM_Template->setValue('oscom_version', OSCOM::getVersion());
      $OSCOM_Template->setValue('logged_in', isset($_SESSION[OSCOM::getSite()]['id']));

      $apps_links = '';

      if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
        $apps_links .= '<ul>';

        foreach ( Access::getLevels() as $group => $links ) {
          $application = current($links);

          $apps_links .= '  <li><a href="' . OSCOM::getLink(null, $application['module']) . '"><span style="float: right;">&#9656;</span>' . Access::getGroupTitle($group) . '</a>' .
                         '    <ul>';

          foreach ( $links as $link ) {
            $apps_links .= '      <li><a href="' . OSCOM::getLink(null, $link['module']) . '">' . $OSCOM_Template->getIcon(16, $link['icon']) . '&nbsp;' . $link['title'] . '</a></li>';
          }

          $apps_links .= '    </ul>' .
                         '  </li>';
        }

        $apps_links .= '</ul>';
      }

      $OSCOM_Template->setValue('apps_links', $apps_links);

      $total_shortcuts = 0;
      $shortcut_links = '';

      if ( isset($_SESSION[OSCOM::getSite()]['id']) ) {
        $shortcut_links .= '<ul class="apps" style="float: right;">';

        if ( Registry::get('Application')->canLinkTo() ) {
          if ( Access::isShortcut(OSCOM::getSiteApplication()) ) {
            $shortcut_links .= '  <li class="shortcuts">' . HTML::link(OSCOM::getLink(null, 'Dashboard', 'RemoveShortcut&shortcut=' . OSCOM::getSiteApplication()), HTML::icon('shortcut_remove.png')) . '</li>';
          } else {
            $shortcut_links .= '  <li class="shortcuts">' . HTML::link(OSCOM::getLink(null, 'Dashboard', 'AddShortcut&shortcut=' . OSCOM::getSiteApplication()), HTML::icon('shortcut_add.png')) . '</li>';
          }
        }

        if ( Access::hasShortcut() ) {
          $shortcut_links .= '  <li class="shortcuts">';

          foreach ( Access::getShortcuts() as $shortcut ) {
            $shortcut_links .= '<a href="' . OSCOM::getLink(null, $shortcut['module']) . '" id="shortcut-' . $shortcut['module'] . '">' . $OSCOM_Template->getIcon(16, $shortcut['icon'], $shortcut['title']) . '<div class="notBubble"></div></a>';

            $total_shortcuts++;
          }

          $shortcut_links .= '  </li>';
        }

        $shortcut_links .= '  <li><a href="#">' . HTML::outputProtected($_SESSION[OSCOM::getSite()]['username']) . ' &#9662;</a>' .
                           '    <ul>' .
                           '      <li><a href="' . OSCOM::getLink(null, 'Login', 'Logoff') . '">' . OSCOM::getDef('header_title_logoff') . '</a></li>' .
                           '    </ul>' .
                           '  </li>' .
                           '</ul>';
      }

      $OSCOM_Template->setValue('total_shortcuts', $total_shortcuts);
      $OSCOM_Template->setValue('shortcut_links', $shortcut_links);

      $ms_pinned_sites = '';

      if ( Access::hasShortcut() ) {
        $ms_pinned_sites .= 'window.external.msSiteModeClearJumplist();' . "\n" .
                            'window.external.msSiteModeCreateJumplist("Shortcuts");' . "\n";

        foreach ( Access::getShortcuts() as $shortcut ) {
          $ms_pinned_sites .= 'window.external.msSiteModeAddJumpListItem("' . $shortcut['title'] . '", "' . OSCOM::getLink(null, $shortcut['module']) . '", "", "self");' . "\n";
        }

        $ms_pinned_sites .= 'window.external.msSiteModeShowJumplist();' . "\n";
      }

      $OSCOM_Template->setValue('ms_pinned_sites', $ms_pinned_sites);

// HPDL move following checks elsewhere
// check if a default currency is set
      if (!defined('DEFAULT_CURRENCY')) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_error_no_default_currency'), 'error');
      }

// check if a default language is set
      if (!defined('DEFAULT_LANGUAGE')) {
        Registry::get('MessageStack')->add('header', ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
      }

      if (function_exists('ini_get') && ((bool)ini_get('file_uploads') == false) ) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_uploads_disabled'), 'warning');
      }

// check if Work directories are writable
      $work_dirs = array();

      foreach ( array('Cache', 'CoreUpdate', 'Database', 'Logs', 'Session', 'Temp') as $w ) {
        if ( !is_writable(OSCOM::BASE_DIRECTORY . 'Work/' . $w) ) {
          $work_dirs[] = $w;
        }
      }

      if ( !empty($work_dirs) ) {
        Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_work_directories_not_writable'), OSCOM::BASE_DIRECTORY . 'Work/', implode(', ', $work_dirs)), 'error');
      }

      if ( !OSCOM::configExists('time_zone', 'OSCOM') ) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_time_zone_not_defined'), 'warning');
      }

      if ( !OSCOM::configExists('dir_fs_public', 'OSCOM') || !file_exists(OSCOM::getConfig('dir_fs_public', 'OSCOM')) ) {
        Registry::get('MessageStack')->add('header', OSCOM::getDef('ms_warning_dir_fs_public_not_defined'), 'warning');
      }

// check if the upload directory exists
      if ( is_dir(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload') ) {
        if ( !is_writeable(OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload') ) {
          Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_upload_directory_not_writable'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload'), 'error');
        }
      } else {
        Registry::get('MessageStack')->add('header', sprintf(OSCOM::getDef('ms_error_upload_directory_non_existant'), OSCOM::getConfig('dir_fs_public', 'OSCOM') . 'upload'), 'error');
      }
    }

    public static function getDefaultApplication() {
      return self::$_default_application;
    }

    public static function hasAccess($application) {
      if ( !isset($_SESSION[OSCOM::getSite()]['id']) ) {
        $redirect = false;

        if ( $application != 'Login' ) {
          $_SESSION[OSCOM::getSite()]['redirect_origin'] = $application;

          $redirect = true;
        }

        if ( $redirect === true ) {
          OSCOM::redirect(OSCOM::getLink(null, 'Login'));
        }
      }

      return Access::hasAccess(OSCOM::getSite(), $application);
    }

    public static function getGuestApplications() {
      return self::$_guest_applications;
    }
  }
?>
