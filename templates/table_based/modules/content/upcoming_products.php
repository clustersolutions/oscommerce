<?php
/*
  $Id: upcoming_products.php 348 2005-12-19 07:04:41Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  $Qupcoming = $osC_Database->query('select p.products_id, pd.products_name, p.products_date_available as date_expected from :table_products p, :table_products_description pd where to_days(p.products_date_available) >= to_days(now()) and p.products_status = :products_status and p.products_id = pd.products_id and pd.language_id = :language_id order by :expected_products_field :expected_products_sort limit :max_display_upcoming_products');
  $Qupcoming->bindRaw(':table_products', TABLE_PRODUCTS);
  $Qupcoming->bindRaw(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
  $Qupcoming->bindInt(':products_status', 1);
  $Qupcoming->bindInt(':language_id', $osC_Language->getID());
  $Qupcoming->bindRaw(':expected_products_field', EXPECTED_PRODUCTS_FIELD);
  $Qupcoming->bindRaw(':expected_products_sort', EXPECTED_PRODUCTS_SORT);
  $Qupcoming->bindInt(':max_display_upcoming_products', MODULE_CONTENT_UPCOMING_PRODUCTS_MAX_DISPLAY);
  $Qupcoming->execute();

  if ($Qupcoming->numberOfRows() > 0) {
?>
<!-- upcoming_products //-->
          <tr>
            <td><br /><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="tableHeading">&nbsp;<?php echo TABLE_HEADING_UPCOMING_PRODUCTS; ?>&nbsp;</td>
                <td align="right" class="tableHeading">&nbsp;<?php echo TABLE_HEADING_DATE_EXPECTED; ?>&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator(); ?></td>
              </tr>
<?php
    $row = 0;
    while ($Qupcoming->next()) {
      $row++;
      if (($row / 2) == floor($row / 2)) {
        echo '              <tr class="upcomingProducts-even">' . "\n";
      } else {
        echo '              <tr class="upcomingProducts-odd">' . "\n";
      }

      echo '                <td class="smallText">&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCTS, $Qupcoming->valueInt('products_id')) . '">' . $Qupcoming->value('products_name') . '</a>&nbsp;</td>' . "\n" .
           '                <td align="right" class="smallText">&nbsp;' . tep_date_short($Qupcoming->value('date_expected')) . '&nbsp;</td>' . "\n" .
           '              </tr>' . "\n";
    }
?>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator(); ?></td>
              </tr>
            </table></td>
          </tr>
<!-- upcoming_products_eof //-->
<?php

    $Qupcoming->freeResult();
  }
?>
