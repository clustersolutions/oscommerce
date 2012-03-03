<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Value\shortcut_links;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $OSCOM_Template = Registry::get('Template');

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

      return $shortcut_links;
    }
  }
?>
