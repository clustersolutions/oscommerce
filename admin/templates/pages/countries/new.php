<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_COUNTRY; ?></div>
<div class="infoBoxContent">
  <form name="cNew" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_NAME . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_name', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_2 . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_2', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_COUNTRY_CODE_3 . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('countries_iso_code_3', null, 'style="width: 100%;"'); ?></td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_ADDRESS_FORMAT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('address_format'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
