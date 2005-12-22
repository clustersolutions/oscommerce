<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  class osC_Boxes_manufacturer_info extends osC_Modules {
    var $_title = 'Manufacturer Info',
        $_code = 'manufacturer_info',
        $_author_name = 'osCommerce',
        $_author_www = 'http://www.oscommerce.com',
        $_group = 'boxes';

    function osC_Boxes_manufacturer_info() {
//      $this->_title = BOX_HEADING_MANUFACTURER_INFO;
    }

    function initialize() {
      global $osC_Product, $osC_Database;

      if (isset($osC_Product) && is_a($osC_Product, 'osC_Product')) {
        $Qmanufacturer = $osC_Database->query('select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from :table_manufacturers m left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id), :table_products p  where p.products_id = :products_id and p.manufacturers_id = m.manufacturers_id');
        $Qmanufacturer->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
        $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
        $Qmanufacturer->bindTable(':table_products', TABLE_PRODUCTS);
        $Qmanufacturer->bindInt(':languages_id', $_SESSION['languages_id']);
        $Qmanufacturer->bindInt(':products_id', $osC_Product->getID());
        $Qmanufacturer->execute();

        if ($Qmanufacturer->numberOfRows()) {
          $data = '<table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";

          if (tep_not_null($Qmanufacturer->value('manufacturers_image'))) {
            $data .= '  <tr>' . "\n" .
                     '    <td align="center" class="infoBoxContents" colspan="2"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers=' . $Qmanufacturer->valueInt('manufacturers_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qmanufacturer->value('manufacturers_image'), $Qmanufacturer->value('manufacturers_name')) . '</a></td>' . "\n" .
                     '  </tr>' . "\n";
          }

          if (tep_not_null($Qmanufacturer->value('manufacturers_url'))) {
            $data .= '  <tr>' . "\n" .
                     '    <td valign="top" class="infoBoxContents">-&nbsp;</td>' . "\n" .
                     '    <td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_REDIRECT, 'action=manufacturer&manufacturers_id=' . $Qmanufacturer->valueInt('manufacturers_id')) . '" target="_blank">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $Qmanufacturer->value('manufacturers_name')) . '</a></td>' . "\n" .
                     '  </tr>' . "\n";
          }

          $data .= '  <tr>' . "\n" .
                   '    <td valign="top" class="infoBoxContents">-&nbsp;</td>' . "\n" .
                   '    <td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers=' . $Qmanufacturer->valueInt('manufacturers_id')) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a></td>' . "\n" .
                   '  </tr>' . "\n" .
                   '</table>' . "\n";

          $this->_content = $data;
        }
      }
    }
  }
?>
