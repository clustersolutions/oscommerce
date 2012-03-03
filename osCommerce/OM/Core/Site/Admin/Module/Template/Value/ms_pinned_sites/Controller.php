<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Value\ms_pinned_sites;

  use osCommerce\OM\Core\Access;
  use osCommerce\OM\Core\OSCOM;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $ms_pinned_sites = '';

      if ( Access::hasShortcut() ) {
        $ms_pinned_sites .= 'window.external.msSiteModeClearJumplist();' . "\n" .
                            'window.external.msSiteModeCreateJumplist("Shortcuts");' . "\n";

        foreach ( Access::getShortcuts() as $shortcut ) {
          $ms_pinned_sites .= 'window.external.msSiteModeAddJumpListItem("' . $shortcut['title'] . '", "' . OSCOM::getLink(null, $shortcut['module']) . '", "", "self");' . "\n";
        }

        $ms_pinned_sites .= 'window.external.msSiteModeShowJumplist();' . "\n";
      }

      return $ms_pinned_sites;
    }
  }
?>
