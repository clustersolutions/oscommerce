<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;
  use osCommerce\OM\Core\Site\Admin\CategoryTree;
?>

<div id="sectionMenu_categories">
  <div class="infoBox">

<?php
  if ( $new_product ) {
    echo '<h3>' . HTML::icon('new.png') . ' ' . OSCOM::getDef('action_heading_new_product') . '</h3>';
  } else {
    echo '<h3>' . HTML::icon('edit.png') . ' ' . $OSCOM_ObjectInfo->getProtected('products_name') . '</h3>';
  }
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable" id="productCategoriesDataTable">
      <thead>
        <tr>
          <th colspan="2"><?php echo OSCOM::getDef('table_heading_categories'); ?></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th colspan="2">&nbsp;</th>
        </tr>
      </tfoot>
      <tbody>

<?php
  $product_categories_array = array();

  if ( !$new_product ) {
    $Qcategories = $OSCOM_PDO->prepare('select categories_id from :table_products_to_categories where products_id = :products_id');
    $Qcategories->bindInt(':products_id', $OSCOM_ObjectInfo->getInt('products_id'));
    $Qcategories->execute();

    while ( $Qcategories->fetch() ) {
      $product_categories_array[] = $Qcategories->valueInt('categories_id');
    }
  }

  $assignedCategoryTree = new CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5);

  foreach ($assignedCategoryTree->getArray() as $value) {
    echo '      <tr>' . "\n" .
         '        <td width="50" align="center">' . HTML::checkboxField('categories[]', $value['id'], in_array($value['id'], $product_categories_array), 'id="categories_' . $value['id'] . '"') . '</td>' . "\n" .
         '        <td><a href="#" onclick="document.product.categories_' . $value['id'] . '.checked=!document.product.categories_' . $value['id'] . '.checked;">' . $value['title'] . '</a></td>' . "\n" .
         '      </tr>' . "\n";
  }
?>

      </tbody>
    </table>

<script>
$('#productCategoriesDataTable tbody tr:odd').addClass('alt');

$('#productCategoriesDataTable tbody tr').each(function(i) {
  $(this).hover( function() { $(this).addClass('mouseOver'); }, function() { $(this).removeClass('mouseOver'); });
});
</script>

  </div>
</div>
