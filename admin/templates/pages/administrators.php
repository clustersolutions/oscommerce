<?php
/*
  $Id: manufacturers.php 241 2005-11-13 22:56:32Z hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo HEADING_TITLE; ?></h1>

<div id="infoBox_aDefault" <?php if (!empty($action)) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_ADMINISTRATORS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>
<?php
  $Qadmin = $osC_Database->query('select id, user_name from :table_administrators order by user_name');
  $Qadmin->bindTable(':table_administrators', TABLE_ADMINISTRATORS);
  $Qadmin->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
  $Qadmin->execute();

  while ($Qadmin->next()) {
    if (!isset($aInfo) && (!isset($_GET['aID']) || (isset($_GET['aID']) && ($_GET['aID'] == $Qadmin->value('id')))) && ($action != 'aNew')) {
      $aInfo = new objectInfo($Qadmin->toArray());
    }

    if (isset($aInfo) && ($Qadmin->valueInt('id') == $aInfo->id)) {
      echo '      <tr class="selected">' . "\n";
    } else {
      echo '      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id')) . '\';">' . "\n";
    }
?>
        <td><?php echo $Qadmin->value('user_name'); ?></td>
        <td align="right">
<?php
    if (isset($aInfo) && ($Qadmin->valueInt('id') == $aInfo->id)) {
      echo '<a href="#" onclick="toggleInfoBox(\'aEdit\');">' . osc_icon('configure.png', IMAGE_EDIT) . '</a>&nbsp;' .
           '<a href="#" onclick="toggleInfoBox(\'aDelete\');">' . osc_icon('trash.png', IMAGE_DELETE) . '</a>';
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=aEdit'), osc_icon('configure.png', IMAGE_EDIT)) . '&nbsp;' .
           osc_link_object(osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $Qadmin->valueInt('id') . '&action=aDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>
        </td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>

  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td class="smallText"><?php echo $Qadmin->displayBatchLinksTotal(TEXT_DISPLAY_NUMBER_OF_ADMINISTRATORS); ?></td>
      <td class="smallText" align="right"><?php echo $Qadmin->displayBatchLinksPullDown(); ?></td>
    </tr>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'aNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_aNew" <?php if ($action != 'aNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_HEADING_NEW_ADMINISTRATOR; ?></div>
  <div class="infoBoxContent">
    <form name="mNew" action="<?php echo osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_NEW_INTRO; ?></p>
    <p><?php echo TEXT_ADMINISTRATOR_USERNAME . '<br />' . osc_draw_input_field('user_name'); ?></p>
    <p><?php echo TEXT_ADMINISTRATOR_PASSWORD . '<br />' . osc_draw_password_field('user_password'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($aInfo)) {
?>

<div id="infoBox_aEdit" <?php if ($action != 'aEdit') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('configure.png', IMAGE_EDIT) . ' ' . $aInfo->user_name; ?></div>
  <div class="infoBoxContent">
    <form name="aEdit" action="<?php echo osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $aInfo->id . '&action=save'); ?>" method="post">

    <p><?php echo TEXT_EDIT_INTRO; ?></p>
    <p><?php echo TEXT_ADMINISTRATOR_USERNAME . '<br />' . osc_draw_input_field('user_name', $aInfo->user_name); ?></p>
    <p><?php echo TEXT_ADMINISTRATOR_PASSWORD . '<br />' . osc_draw_password_field('user_password'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<div id="infoBox_aDelete" <?php if ($action != 'aDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $aInfo->user_name; ?></div>
  <div class="infoBoxContent">
    <form name="aDelete" action="<?php echo osc_href_link_admin(FILENAME_ADMINISTRATORS, 'page=' . $_GET['page'] . '&aID=' . $aInfo->id . '&action=deleteconfirm'); ?>" method="post">

    <p><?php echo TEXT_DELETE_INTRO; ?></p>
    <p><?php echo '<b>' . $aInfo->user_name . '</b>'; ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'aDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
