<?php
/*
  $Id: newsletters_edit.php,v 1.2 2004/08/17 23:35:19 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/

  if (isset($_GET['nmID']) && is_numeric($_GET['nmID'])) {
    $Qnewsletter = $osC_Database->query('select title, content, module from :table_newsletters where newsletters_id = :newsletters_id');
    $Qnewsletter->bindTable(':table_newsletters', TABLE_NEWSLETTERS);
    $Qnewsletter->bindInt(':newsletters_id', $_GET['nmID']);
    $Qnewsletter->execute();

    $nmInfo = new objectInfo($Qnewsletter->toArray());
  }

  require('includes/classes/directory_listing.php');

  $osC_DirectoryListing = new osC_DirectoryListing('includes/modules/newsletters');
  $osC_DirectoryListing->setIncludeDirectories(false);
  $files = $osC_DirectoryListing->getFiles();

  $modules_array = array();
  for ($i=0, $n=sizeof($files); $i<$n; $i++) {
    $module = substr($files[$i]['name'], 0, strrpos($files[$i]['name'], '.'));

    include('includes/languages/' . $osC_Session->value('language') . '/modules/newsletters/' . $files[$i]['name']);
    include('includes/modules/newsletters/' . $files[$i]['name']);

    $newsletter_module_class = 'osC_Newsletter_' . $module;
    $osC_NewsletterModule = new $newsletter_module_class();

    $modules_array[] = array('id' => $module, 'text' => $osC_NewsletterModule->getTitle());
  }
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<?php echo tep_draw_form('newsletter', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&' . (isset($_GET['nmID']) ? 'nmID=' . $_GET['nmID'] . '&' : '') . 'action=save'); ?>

<table border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td class="main"><?php echo TEXT_NEWSLETTER_MODULE; ?></td>
    <td class="main"><?php echo osc_draw_pull_down_menu('module', $modules_array, (isset($nmInfo) ? $nmInfo->module : '')); ?></td>
  </tr>
  <tr>
    <td class="main" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_NEWSLETTER_TITLE; ?></td>
    <td class="main"><?php echo osc_draw_input_field('title', (isset($nmInfo) ? $nmInfo->title : ''), '', true); ?></td>
  </tr>
  <tr>
    <td class="main" colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td class="main" valign="top"><?php echo TEXT_NEWSLETTER_CONTENT; ?></td>
    <td class="main"><?php echo osc_draw_textarea_field('content', (isset($nmInfo) ? $nmInfo->content : ''), '100%', '20'); ?></td>
  </tr>
</table>

<p align="right"><?php echo '<input type="submit" value="' . BUTTON_SAVE . '" class="operationButton">&nbsp;<input type="button" value="' . BUTTON_CANCEL . '" onClick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&' . (isset($_GET['nmID']) ? 'nmID=' . $_GET['nmID'] : '')) . '\';" class="operationButton">'; ?></p>

</form>
