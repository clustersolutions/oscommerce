<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr class="header">
    <td valign="middle"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image(DIR_WS_IMAGES . 'oscommerce.gif', 'osCommerce') . '</a>'; ?></td>
    <td align="right" valign="bottom"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_account.gif', HEADER_TITLE_MY_ACCOUNT) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_cart.gif', HEADER_TITLE_CART_CONTENTS) . '</a>&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '">' . tep_image(DIR_WS_IMAGES . 'header_checkout.gif', HEADER_TITLE_CHECKOUT) . '</a>'; ?>&nbsp;&nbsp;</td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="headerNavigation">
<?php
  if ($osC_Services->isStarted('breadcrumb')) {
    echo '    <td class="headerNavigation">&nbsp;&nbsp;' . $breadcrumb->trail(' &raquo; ') . '</td>' . "\n";
  }
?>
    <td align="right" class="headerNavigation"><?php if ($osC_Customer->isLoggedOn()) { echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_LOGOFF . '</a> &nbsp;|&nbsp; '; } echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_MY_ACCOUNT . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, '', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CART_CONTENTS . '</a> &nbsp;|&nbsp; <a href="' . tep_href_link(FILENAME_CHECKOUT, 'shipping', 'SSL') . '" class="headerNavigation">' . HEADER_TITLE_CHECKOUT . '</a>'; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
