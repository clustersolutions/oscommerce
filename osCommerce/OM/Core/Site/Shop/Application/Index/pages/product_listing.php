<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\OSCOM;
?>

<?php echo osc_image(DIR_WS_IMAGES . $OSCOM_Template->getPageImage(), $OSCOM_Template->getPageTitle(), HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 'id="pageIcon"'); ?>

<h1><?php echo $OSCOM_Template->getPageTitle(); ?></h1>

<?php
// optional Product List Filter
  if ( PRODUCT_LIST_FILTER > 0 ) {
    if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from :table_products p, :table_products_to_categories p2c, :table_categories c, :table_categories_description cd, :table_templates_boxes tb, :table_product_attributes pa where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$OSCOM_Language->getID() . "' and tb.code = 'Manufacturers' and tb.id = pa.id and pa.products_id = p.products_id and pa.value = '" . (int)$_GET['Manufacturers'] . "' order by cd.categories_name";
    } else {
      $filterlist_sql = "select distinct m.manufacturers_id as id, m.manufacturers_name as name from :table_products p, :table_products_to_categories p2c, :table_manufacturers m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . $OSCOM_Category->getID() . "' order by m.manufacturers_name";
    }

    $Qfilterlist = $OSCOM_Database->query($filterlist_sql);
    $Qfilterlist->execute();

    if ( $Qfilterlist->numberOfRows() > 1 ) {
      echo '<p><form name="filter" action="' . OSCOM::getLink() . '" method="get">' . $OSCOM_Language->get('filter_show') . '&nbsp;';

      if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
        echo osc_draw_hidden_field('Manufacturers', $_GET['Manufacturers']);

        $options = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_categories')));
      } else {
        echo osc_draw_hidden_field('cPath', $OSCOM_Category->getPath());

        $options = array(array('id' => '', 'text' => OSCOM::getDef('filter_all_manufacturers')));
      }

      if ( isset($_GET['sort']) ) {
        echo osc_draw_hidden_field('sort', $_GET['sort']);
      }

      while ( $Qfilterlist->next() ) {
        $options[] = array('id' => $Qfilterlist->valueInt('id'), 'text' => $Qfilterlist->value('name'));
      }

      echo osc_draw_pull_down_menu('filter', $options, (isset($_GET['filter']) ? $_GET['filter'] : null), 'onchange="this.form.submit()"') .
           osc_draw_hidden_session_id_field() . '</form></p>' . "\n";
    }
  }

  if ( isset($_GET['Manufacturers']) && !empty($_GET['Manufacturers']) ) {
    $OSCOM_Products->setManufacturer($_GET['Manufacturers']);
  }

  $Qlisting = $OSCOM_Products->execute();

  require('includes/modules/product_listing.php');
?>
