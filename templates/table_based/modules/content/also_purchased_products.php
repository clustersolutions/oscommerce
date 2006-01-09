<?php
/*
  $Id: also_purchased_products.php 348 2005-12-19 07:04:41Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (isset($osC_Product)) {
    $Qorders = $osC_Database->query("select p.products_id, p.products_image, pd.products_keyword from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where opa.products_id = '" . (int)$osC_Product->getID() . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$osC_Product->getID() . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = :language_id group by p.products_id order by o.date_purchased desc limit " . MODULE_CONTENT_ALSO_PURCHASED_MAX_DISPLAY);
    $Qorders->bindInt(':language_id', $osC_Language->getID());

    if (MODULE_CONTENT_ALSO_PURCHASED_PRODUCTS_CACHE > 0) {
      $Qorders->setCache('also_purchased-' . (int)$osC_Product->getID(), MODULE_CONTENT_ALSO_PURCHASED_PRODUCTS_CACHE);
    }

    $Qorders->execute();

    $num_products_ordered = $Qorders->numberOfRows();

    if ($num_products_ordered >= MODULE_CONTENT_ALSO_PURCHASED_MIN_DISPLAY) {
?>
<!-- also_purchased_products //-->
<div class="boxNew">
  <div class="boxTitle"><?php echo TEXT_ALSO_PURCHASED_PRODUCTS; ?></div>

  <div class="boxContents">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
      $col = 0;

      while ($Qorders->next()) {
        if ($col === 0) {
          echo '      <tr>' . "\n";
        }

        $products_name = tep_get_products_name($Qorders->valueInt('products_id'));

        echo '      <td align="center" class="smallText" width="33%" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qorders->value('products_keyword')) . '">' . tep_image(DIR_WS_IMAGES . $Qorders->value('products_image'), $Qorders->value('products_name'), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br /><a href="' . tep_href_link(FILENAME_PRODUCTS, $Qorders->value('products_keyword')) . '">' . $products_name . '</a></td>' . "\n";

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
