<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if ($current_category_id == '0') {
    $Qnewproducts = $osC_Database->query('select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, pd.products_name from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc limit :max_display_new_products');
    $Qnewproducts->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qnewproducts->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qnewproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qnewproducts->bindInt(':max_display_new_products', MAX_DISPLAY_NEW_PRODUCTS);
  } else {
    $Qnewproducts = $osC_Database->query('select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price, pd.products_name from :table_products p, :table_products_description pd, :table_products_to_categories p2c, :table_categories c where c.parent_id = :parent_id and c.categories_id = p2c.categories_id and p2c.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc limit :max_display_new_products');
    $Qnewproducts->bindRaw(':table_products', TABLE_PRODUCTS);
    $Qnewproducts->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qnewproducts->bindRaw(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qnewproducts->bindRaw(':table_categories', TABLE_CATEGORIES);
    $Qnewproducts->bindInt(':parent_id', $current_category_id);
    $Qnewproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
    $Qnewproducts->bindInt(':max_display_new_products', MAX_DISPLAY_NEW_PRODUCTS);
  }

  $Qnewproducts->setCache('new_products-' . $osC_Session->value('language') . '-' . $current_category_id, '720');

  $Qnewproducts->execute();

  if ($Qnewproducts->numberOfRows()) {
?>
<!-- new_products //-->
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));

    new contentBoxHeading($info_box_contents);

    $row = 0;
    $col = 0;

    $info_box_contents = array();

    while ($Qnewproducts->next()) {
      $products_price = $osC_Currencies->displayPrice($Qnewproducts->valueDecimal('products_price'), $Qnewproducts->valueInt('products_tax_class_id'));

      if ($osC_Services->isStarted('specials') && $osC_Specials->isActive($Qnewproducts->valueInt('products_id'))) {
        $specials_price = $osC_Specials->getPrice($Qnewproducts->valueInt('products_id'));

        $products_price = '<s>' . $products_price . '</s>&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($specials_price, $Qnewproducts->valueInt('products_tax_class_id')) . '</span>';
      }

      $info_box_contents[$row][$col] = array('align' => 'center',
                                             'params' => 'class="smallText" width="33%" valign="top"',
                                             'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qnewproducts->valueInt('products_id')) . '">' . tep_image(DIR_WS_IMAGES . $Qnewproducts->value('products_image'), $Qnewproducts->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qnewproducts->valueInt('products_id')) . '">' . $Qnewproducts->value('products_name') . '</a><br>' . $products_price);

      $col ++;

      if ($col > 2) {
        $col = 0;
        $row ++;
      }
    }

    new contentBox($info_box_contents);

    $Qnewproducts->freeResult();
?>
<!-- new_products_eof //-->
<?php
  }
?>
