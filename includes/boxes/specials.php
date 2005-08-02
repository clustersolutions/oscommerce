<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if ($osC_Services->isStarted('specials')) {
    $Qspecials = $osC_Database->query('select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from :table_products p, :table_products_description pd, :table_specials s where s.status = 1 and s.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by s.specials_date_added desc limit :max_random_select_specials');
    $Qspecials->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qspecials->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qspecials->bindRaw(':table_specials', TABLE_SPECIALS);
    $Qspecials->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qspecials->bindInt(':max_random_select_specials', MAX_RANDOM_SELECT_SPECIALS);

    if ($Qspecials->executeRandomMulti()) {
?>
<!-- specials //-->
          <tr>
            <td>
<?php
      $info_box_contents = array();
      $info_box_contents[] = array('text' => BOX_HEADING_SPECIALS);

      new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_SPECIALS));

      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'center',
                                   'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qspecials->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qspecials->value('products_image'), $Qspecials->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qspecials->valueInt('products_id')) . '">' . $Qspecials->value('products_name') . '</a><br><s>' . $osC_Currencies->displayPrice($Qspecials->valueDecimal('products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</s>&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($Qspecials->valueDecimal('specials_new_products_price'), $Qspecials->valueInt('products_tax_class_id')) . '</span>');

      new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- specials_eof //-->
<?php
      $Qspecials->freeResult();
    }
  }
?>
