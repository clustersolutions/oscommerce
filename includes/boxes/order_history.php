<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/

  if ($osC_Customer->isLoggedOn()) {
// retreive the last x products purchased
    $Qorders = $osC_Database->query('select distinct op.products_id from :table_orders o, :table_orders_products op, :table_products p where o.customers_id = :customers_id and o.orders_id = op.orders_id and op.products_id = p.products_id and p.products_status = :products_status group by products_id order by o.date_purchased desc limit :limit');
    $Qorders->bindTable(':table_orders', TABLE_ORDERS);
    $Qorders->bindTable(':table_orders_products', TABLE_ORDERS_PRODUCTS);
    $Qorders->bindTable(':table_products', TABLE_PRODUCTS);
    $Qorders->bindInt(':customers_id', $osC_Customer->id);
    $Qorders->bindInt(':products_status', 1);
    $Qorders->bindInt(':limit', MAX_DISPLAY_PRODUCTS_IN_ORDER_HISTORY_BOX);
    $Qorders->execute();

    if ($Qorders->numberOfRows()) {
?>
<!-- customer_orders //-->
          <tr>
            <td>
<?php
      $info_box_contents = array();
      $info_box_contents[] = array('text' => BOX_HEADING_CUSTOMER_ORDERS);

      new infoBoxHeading($info_box_contents, false, false);

      $product_ids = '';
      while ($Qorders->next()) {
        $product_ids .= $Qorders->valueInt('products_id') . ',';
      }
      $product_ids = substr($product_ids, 0, -1);

      $customer_orders_string = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';

      $Qproducts = $osC_Database->query('select products_id, products_name from :table_products_description where products_id in (:products_id) and language_id = :language_id order by products_name');
      $Qproducts->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
      $Qproducts->bindRaw(':products_id', $product_ids);
      $Qproducts->bindInt(':language_id', $osC_Session->value('languages_id'));
      $Qproducts->execute();

      while ($Qproducts->next()) {
        $customer_orders_string .= '  <tr>' .
                                   '    <td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $Qproducts->valueInt('products_id')) . '">' . $Qproducts->value('products_name') . '</a></td>' .
                                   '    <td class="infoBoxContents" align="right" valign="top"><a href="' . tep_href_link(basename($_SERVER['PHP_SELF']), tep_get_all_get_params(array('action')) . 'action=cust_order&pid=' . $Qproducts->valueInt('products_id')) . '">' . tep_image(DIR_WS_ICONS . 'cart.gif', ICON_CART) . '</a></td>' .
                                   '  </tr>';
      }
      $customer_orders_string .= '</table>';

      $info_box_contents = array();
      $info_box_contents[] = array('text' => $customer_orders_string);

      new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- customer_orders_eof //-->
<?php
    }
  }
?>
