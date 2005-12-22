<?php
/*
  $Id:account_newsletters.php 187 2005-09-14 14:22:13 +0200 (Mi, 14 Sep 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<?php echo tep_image(DIR_WS_IMAGES . 'table_background_account.gif', $osC_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'class="pageIcon"'); ?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<form name="account_newsletter" action="<?php echo tep_href_link(FILENAME_ACCOUNT, 'newsletters=save', 'SSL'); ?>" method="post">

<div class="moduleBox">
  <div class="outsideHeading"><?php echo MY_NEWSLETTERS_TITLE; ?></div>

  <div class="content">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr class="moduleRow" onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="checkBox('newsletter_general');">
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
