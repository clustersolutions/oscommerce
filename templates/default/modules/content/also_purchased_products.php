<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  if (isset($osC_Product)) {
    $Qorders = $osC_Database->query('select p.products_id, pd.products_keyword, i.image from :table_orders_products opa, :table_orders_products opb, :table_orders o, :table_products p left join :table_products_images i on (p.products_id = i.products_id and i.default_flag = :default_flag), :table_products_description pd where opa.products_id = :products_id and opa.orders_id = opb.orders_id and opb.products_id != :products_id and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = 1 and p.products_id = pd.products_id and pd.language_id = :language_id group by p.products_id order by o.date_purchased desc limit :limit');
    $Qorders->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
    $Qorders->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindTable(':table_products', TABLE_PRODUCTS);
    $Qorders->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
    $Qorders->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qorders->bindInt(':default_flag', 1);
    $Qorders->bindInt(':products_id', $osC_Product->getID());
    $Qorders->bindInt(':products_id', $osC_Product->getID());
    $Qorders->bindInt(':language_id', $osC_Language->getID());
    $Qorders->bindInt(':limit', MODULE_CONTENT_ALSO_PURCHASED_MAX_DISPLAY);

    if (MODULE_CONTENT_ALSO_PURCHASED_PRODUCTS_CACHE > 0) {
      $Qorders->setCache('also_purchased-' . (int)$osC_Product->getID(), MODULE_CONTENT_ALSO_PURCHASED_PRODUCTS_CACHE);
    }

    $Qorders->execute();

    $num_products_ordered = $Qorders->numberOfRows();

    if ($num_products_ordered >= MODULE_CONTENT_ALSO_PURCHASED_MIN_DISPLAY) {
?>
<!-- also_purchased_products //-->
<div class="moduleBox">
  <h6><?php echo $osC_Language->get('customers_also_purchased_title'); ?></h6>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
      $col = 0;

      while ($Qorders->next()) {
        if ($col === 0) {
          echo '      <tr>' . "\n";
        }

        $products_name = tep_get_products_name($Qorders->valueInt('products_id'));

        echo '      <td align="center" width="33%" valign="top">';

        if (osc_empty($Qorders->value('image')) === false) {
          echo osc_link_object(tep_href_link(FILENAME_PRODUCTS, $Qorders->value('products_keyword')), $osC_Image->show($Qorders->value('image'), $products_name)) . '<br />';
        }

        echo osc_link_object(tep_href_link(FILENAME_PRODUCTS, $Qorders->value('products_keyword')), $products_name) . '</td>' . "\n";

        $col++;

        if ($col > 2) {
          $col = 0;

          echo '      </tr>' . "\n";
        }
      }

      if ($col > 0) {
        echo '      </tr>' . "\n";
      }

      $Qorders->freeResult();
?>

    </table>
  </div>
</div>
<!-- also_purchased_products_eof //-->

<?php
    }
  }
?>
