<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_address_book.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<?php
  if ($messageStack->size('address_book') > 0) {
    echo $messageStack->output('address_book');
  }
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo PRIMARY_ADDRESS_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td valign="top"><?php echo PRIMARY_ADDRESS_DESCRIPTION; ?></td>
        <td valign="top" align="center"><?php echo '<b>' . PRIMARY_ADDRESS_TITLE . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
        <td valign="top"><?php echo tep_address_label($osC_Customer->id, $osC_Customer->default_address_id, true, ' ', '<br>'); ?></td>
      </tr>
    </table>
  </div>
</div>

<?php
  $Qaddresses = $osC_Database->query('select address_book_id, entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from :table_address_book where customers_id = :customers_id order by firstname, lastname');
  $Qaddresses->bindTable(':table_address_book', TABLE_ADDRESS_BOOK);
  $Qaddresses->bindInt(':customers_id', $osC_Customer->id);
  $Qaddresses->execute();
?>

<div class="moduleBox">
  <div class="outsideHeading"><?php echo ADDRESS_BOOK_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">

<?php
  while ($Qaddresses->next()) {
    $format_id = tep_get_address_format_id($Qaddresses->valueInt('country_id'));
?>

      <tr class="moduleRow" onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);">
        <td>
          <b><?php echo $Qaddresses->valueProtected('firstname') . ' ' . $Qaddresses->valueProtected('lastname'); ?></b>

<?php
    if ($Qaddresses->valueInt('address_book_id') == $osC_Customer->default_address_id) {
      echo '&nbsp;<small><i>' . PRIMARY_ADDRESS . '</i></small>';
    }
?>

        </td>
        <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book=' . $Qaddresses->valueInt('address_book_id') . '&edit', 'SSL') . '">' . tep_image_button('small_edit.gif', SMALL_IMAGE_BUTTON_EDIT) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book=' . $Qaddresses->valueInt('address_book_id') . '&delete', 'SSL') . '">' . tep_image_button('small_delete.gif', SMALL_IMAGE_BUTTON_DELETE) . '</a>'; ?></td>
      </tr>
      <tr>
        <td colspan="2" style="padding: 0px 0px 10px 10px;"><?php echo tep_address_format($format_id, $Qaddresses->toArray(), true, ' ', '<br>'); ?></td>
      </tr>

<?php
  }
?>

    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;">
<?php
  if ($Qaddresses->numberOfRows() < MAX_ADDRESS_BOOK_ENTRIES) {
    echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, 'address_book&new', 'SSL') . '">' . tep_image_button('button_add_address.gif', IMAGE_BUTTON_ADD_ADDRESS) . '</a>';
  } else {
    echo sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES);
  }
?>
  </span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>
