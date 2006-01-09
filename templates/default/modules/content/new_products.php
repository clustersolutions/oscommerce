<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if ($current_category_id == '0') {
    $Qnewproducts = $osC_Database->query('select p.products_id, p.products_image, p.products_tax_class_id, p.products_price, pd.products_name, pd.products_keyword from :table_products p, :table_products_description pd where p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc limit :max_display_new_products');
    $Qnewproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qnewproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qnewproducts->bindInt(':language_id', $osC_Language->getID());
    $Qnewproducts->bindInt(':max_display_new_products', MODULE_CONTENT_NEW_PRODUCTS_MAX_DISPLAY);
  } else {
    $Qnewproducts = $osC_Database->query('select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price, pd.products_name, pd.products_keyword from :table_products p, :table_products_description pd, :table_products_to_categories p2c, :table_categories c where c.parent_id = :parent_id and c.categories_id = p2c.categories_id and p2c.products_id = p.products_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id order by p.products_date_added desc limit :max_display_new_products');
    $Qnewproducts->bindTable(':table_products', TABLE_PRODUCTS);
    $Qnewproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qnewproducts->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qnewproducts->bindTable(':table_categories', TABLE_CATEGORIES);
    $Qnewproducts->bindInt(':parent_id', $current_category_id);
    $Qnewproducts->bindInt(':language_id', $osC_Language->getID());
    $Qnewproducts->bindInt(':max_display_new_products', MODULE_CONTENT_NEW_PRODUCTS_MAX_DISPLAY);
  }

  if (MODULE_CONTENT_NEW_PRODUCTS_CACHE > 0) {
    $Qnewproducts->setCache('new_products-' . $osC_Language->getCode() . '-' . $current_category_id, MODULE_CONTENT_NEW_PRODUCTS_CACHE);
  }

  $Qnewproducts->execute();

  if ($Qnewproducts->numberOfRows()) {
?>
<!-- new_products //-->
<div class="boxNew">
  <div class="boxTitle"><?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')); ?></div>

  <div class="boxContents">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    $col = 0;

    while ($Qnewproducts->next()) {
      if ($col === 0) {
        echo '      <tr>' . "\n";
      }

      $products_price = $osC_Currencies->displayPrice($Qnewproducts->valueDecimal('products_price'), $Qnewproducts->valueInt('products_tax_class_id'));

      if ($osC_Services->isStarted('specials') && $osC_Specials->isActive($Qnewproducts->valueInt('products_id'))) {
        $specials_price = $osC_Specials->getPrice($Qnewproducts->valueInt('products_id'));

        $products_price = '<s>' . $products_price . '</s>&nbsp;<span class="productSpecialPrice">' . $osC_Currencies->displayPrice($specials_price, $Qnewproducts->valueInt('products_tax_class_id')) . '</span>';
      }

      echo '        <td class="smallText" width="33%" align="center" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qnewproducts->value('products_keyword')) . '">' . tep_image(DIR_WS_IMAGES . $Qnewproducts->value('products_image'), $Qnewproducts->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br /><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qnewproducts->value('products_keyword')) . '">' . $Qnewproducts->value('products_name') . '</a><br />' . $products_price . '</td>' . "\n";

      $col++;

      if ($col > 2) {
        $col = 0;

        echo '      </tr>' . "\n";
      }
    }

    if ($col > 0) {
      echo '      </tr>' . "\n";
    }

    $Qnewproducts->freeResult();
?>
    </table>
  </div>
</div>
<!-- new_products_eof //-->
<?php
  }
?>
