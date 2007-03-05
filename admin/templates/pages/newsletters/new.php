<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2007 osCommerce

  Released under the GNU General Public License
*/

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/newsletters');
  $osC_DirectoryListing->setIncludeDirectories(false);

  $modules_array = array();

  foreach ( $osC_DirectoryListing->getFiles() as $file ) {
    $module = substr($file['name'], 0, strrpos($file['name'], '.'));

    $osC_Language->loadConstants('modules/newsletters/' . $file['name']);
    include('includes/modules/newsletters/' . $file['name']);

    $newsletter_module_class = 'osC_Newsletter_' . $module;
    $osC_NewsletterModule = new $newsletter_module_class();

    $modules_array[] = array('id' => $module,
                             'text' => $osC_NewsletterModule->getTitle());
  }
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ( $osC_MessageStack->size($osC_Template->getModule()) > 0 ) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_HEADING_NEW_EMAIL; ?></div>
<div class="infoBoxContent">
  <form name="newsletter" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=save'); ?>" method="post">

  <table border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_NEWSLETTER_MODULE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_pull_down_menu('module', $modules_array); ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="40%"><?php echo '<b>' . TEXT_NEWSLETTER_TITLE . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_input_field('title'); ?></td>
    </tr>
    <tr>
      <td width="40%" valign="top"><?php echo '<b>' . TEXT_NEWSLETTER_CONTENT . '</b>'; ?></td>
      <td width="60%"><?php echo osc_draw_textarea_field('content', null, 60, 20, 'style="width: 100%;"'); ?></td>
    </tr>
  </table>

  <p align="center"><?php echo osc_draw_hidden_field('subaction', 'confirm') . '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" />'; ?></p>

  </form>
</div>
