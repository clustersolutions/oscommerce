<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['products_id'])) {
    $Qmanufacturer = $osC_Database->query('select m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, mi.manufacturers_url from :table_manufacturers m left join :table_manufacturers_info mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = :languages_id), :table_products p  where p.products_id = :products_id and p.manufacturers_id = m.manufacturers_id');
    $Qmanufacturer->bindTable(':table_manufacturers', TABLE_MANUFACTURERS);
    $Qmanufacturer->bindTable(':table_manufacturers_info', TABLE_MANUFACTURERS_INFO);
    $Qmanufacturer->bindTable(':table_products', TABLE_PRODUCTS);
    $Qmanufacturer->bindInt(':languages_id', $osC_Session->value('languages_id'));
    $Qmanufacturer->bindInt(':products_id', $_GET['products_id']);
    $Qmanufacturer->execute();

    if ($Qmanufacturer->numberOfRows()) {
?>
<!-- manufacturer_info //-->
          <tr>
            <td>
<?php
      $info_box_contents = array();
      $info_box_contents[] = array('text' => BOX_HEADING_MANUFACTURER_INFO);

      new infoBoxHeading($info_box_contents, false, false);

      $manufacturer_info_string = '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
      if (tep_not_null($Qmanufacturer->value('manufacturers_image'))) $manufacturer_info_string .= '<tr><td align="center" class="infoBoxContents" colspan="2">' . tep_image(DIR_WS_IMAGES . $Qmanufacturer->value('manufacturers_image'), $Qmanufacturer->value('manufacturers_name')) . '</td></tr>';
      if (tep_not_null($Qmanufacturer->value('manufacturers_url'))) $manufacturer_info_string .= '<tr><td valign="top" class="infoBoxContents">-&nbsp;</td><td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_REDIRECT, 'action=manufacturer&manufacturers_id=' . $Qmanufacturer->valueInt('manufacturers_id')) . '" target="_blank">' . sprintf(BOX_MANUFACTURER_INFO_HOMEPAGE, $Qmanufacturer->value('manufacturers_name')) . '</a></td></tr>';
      $manufacturer_info_string .= '<tr><td valign="top" class="infoBoxContents">-&nbsp;</td><td valign="top" class="infoBoxContents"><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $Qmanufacturer->valueInt('manufacturers_id')) . '">' . BOX_MANUFACTURER_INFO_OTHER_PRODUCTS . '</a></td></tr>' .
                                   '</table>';

      $info_box_contents = array();
      $info_box_contents[] = array('text' => $manufacturer_info_string);

      new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- manufacturer_info_eof //-->
<?php
    }
  }
?>
