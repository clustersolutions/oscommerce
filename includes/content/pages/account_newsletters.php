<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<div class="pageHeading">
  <span class="pageHeadingImage"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', HEADING_TITLE_NEWSLETTERS, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></span>

  <h1><?php echo HEADING_TITLE_NEWSLETTERS; ?></h1>
</div>

<form name="account_newsletter" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'newsletters=save', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_NEWSLETTERS_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="moduleRow" onMouseOver="rowOverEffect(this);" onMouseOut="rowOutEffect(this);" onClick="checkBox('newsletter_general');">
        <td width="30"><?php echo osc_draw_checkbox_field('newsletter_general', '1', $Qnewsletter->value('customers_newsletter'), 'onclick="checkBox(\'newsletter_general\')"'); ?></td>
        <td><b><?php echo MY_NEWSLETTERS_GENERAL_NEWSLETTER; ?></b></td>
      </tr>
      <tr>
        <td width="30">&nbsp;</td>
        <td><?php echo MY_NEWSLETTERS_GENERAL_NEWSLETTER_DESCRIPTION; ?></td>
      </tr>
    </table>
  </div>
</div>

<div class="submitFormButtons">
  <span style="float: right;"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></span>

  <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</div>

</form>
