<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $Qwhatsnew = $osC_Database->query('select products_id, products_image, products_tax_class_id, products_price from :table_products where products_status = 1 order by products_date_added desc limit :max_random_select_new');
  $Qwhatsnew->bindTable(':table_products', TABLE_PRODUCTS);
  $Qwhatsnew->bindInt(':max_random_select_new', MAX_RANDOM_SELECT_NEW);

  if ($Qwhatsnew->executeRandomMulti()) {
?>
<!-- whats_new //-->
          <tr>
            <td>
<?php
    $new_products_name = tep_get_products_name($Qwhatsnew->valueInt('products_id'));

    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_PRODUCTS_NEW));

    $new_products_price = $osC_Currencies->displayPrice($Qwhatsnew->valueDecimal('products_price'), $Qwhatsnew->valueInt('products_tax_class_id'));

    if ($osC_Services->isStarted('specials') && $osC_Specials->isActive($Qwhatsnew->valueInt('products_id'))) {
      $new_products_specials_price = $osC_Specials->getPrice($Qwhatsnew->valueInt('products_id'));

      $new_products_price = '<s>' . $new_products_price . '</s>&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($new_products_specials_price, $Qwhatsnew->valueInt('products_tax_class_id')) . '</span>';
    }

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qwhatsnew->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qwhatsnew->value('products_image'), $new_products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qwhatsnew->valueInt('products_id')) . '">' . $new_products_name . '</a><br>' . $new_products_price);

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- whats_new_eof //-->
<?php
    $Qwhatsnew->freeResult();
  }
?>
