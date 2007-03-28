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
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png') . ' ' . $osC_Language->get('action_heading_batch_delete_tax_classes'); ?></div>
<div class="infoBoxContent">
  <form name="tcDeleteBatch" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo $osC_Language->get('introduction_batch_delete_tax_classes'); ?></p>

<?php
  $check_tax_classes_flag = array();

  $Qclasses = $osC_Database->query('select tax_class_id, tax_class_title from :table_tax_class where tax_class_id in (":tax_class_id") order by tax_class_title');
  $Qclasses->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qclasses->bindRaw(':tax_class_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qclasses->execute();

  $names_string = '';

  while ($Qclasses->next()) {
    $Qcheck = $osC_Database->query('select products_id from :table_products where products_tax_class_id = :products_tax_class_id limit 1');
    $Qcheck->bindTable(':table_products', TABLE_PRODUCTS);
    $Qcheck->bindInt(':products_tax_class_id', $Qclasses->valueInt('tax_class_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_tax_classes_flag[] = $Qclasses->value('tax_class_title');
    }

    $Qrates = $osC_Database->query('select count(*) as total_tax_rates from :table_tax_rates where tax_class_id = :tax_class_id');
    $Qrates->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qrates->bindInt(':tax_class_id', $Qclasses->valueInt('tax_class_id'));
    $Qrates->execute();

    $tax_class_name = $Qclasses->value('tax_class_title');

    if ( $Qrates->valueInt('total_tax_rates') > 0 ) {
      $tax_class_name .= ' (' . sprintf($osC_Language->get('total_entries'), $Qrates->valueInt('total_tax_rates')) . ')';
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qclasses->valueInt('tax_class_id')) . '<b>' . $tax_class_name . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_classes_flag) ) {
    echo '<p align="center"><input type="submit" value="' . $osC_Language->get('button_delete') . '" class="operationButton" /> <input type="button" value="' . $osC_Language->get('button_cancel') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p><b>' . $osC_Language->get('batch_delete_warning_tax_class_in_use') . '</b></p>' .
         '<p>' . implode(', ', $check_tax_classes_flag) . '</p>';

    echo '<p align="center"><input type="button" value="' . $osC_Language->get('button_back') . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>
