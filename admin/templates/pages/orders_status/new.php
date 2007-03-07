<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS; ?></div>
<div class="infoBoxContent">
  <form name="osNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

  <p><?php echo TEXT_INFO_INSERT_INTRO; ?></p>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_INFO_ORDERS_STATUS_NAME . '</b>'; ?></td>
      <td width="60%">

<?php
  foreach ( $osC_Language->getAll() as $l ) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name']) . '&nbsp;' . osc_draw_input_field('name[' . $l['id'] . ']') . '<br />';
  }
?>

      </td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_SET_DEFAULT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_checkbox_field('default'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
