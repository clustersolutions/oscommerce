<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  include('includes/templates/' . $_GET['template'] . '.php');

  $module = 'osC_Template_' . $_GET['template'];
  $module = new $module();
?>

<h1><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('info.png', IMAGE_INFO) . ' ' . $module->getTitle(); ?></div>
<div class="infoBoxContent">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td>Title:</td>
      <td><?php echo $module->getTitle(); ?></td>
    </tr>
    <tr>
      <td>Author:</td>
      <td><?php echo $module->getAuthorName(); ?> (<?php echo $module->getAuthorAddress(); ?>)</td>
    </tr>
    <tr>
      <td>Markup:</td>
      <td><?php echo $module->getMarkup(); ?></td>
    </tr>
    <tr>
      <td>CSS Based:</td>
      <td><?php echo ( $module->isCSSBased() ? 'Yes' : 'No' ); ?></td>
    </tr>
    <tr>
      <td>Presentation Medium:</td>
      <td><?php echo $module->getMedium(); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo '<input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule()) . '\';" class="operationButton" />'; ?></p>
</div>
