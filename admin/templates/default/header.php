<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<script language="javascript" src="external/jscookmenu/JSCookMenu.js"></script>
<link rel="stylesheet" href="external/jscookmenu/ThemeOffice/theme.css" type="text/css">
<script language="javascript" src="external/jscookmenu/ThemeOffice/theme.js"></script>

<script type="text/javascript">
<!--

<?php
  echo 'var administrationMenu =' . "\n" .
       '[' . "\n";

  if (isset($_SESSION['admin'])) {
    echo '    [null, \'' . addslashes(BOX_HEADING_CONFIGURATION) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('people.png') . '\', \'' . addslashes(BOX_CONFIGURATION_ADMINISTRATORS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'administrators') . '\', null, null],' . "\n";

    $Qgroups = $osC_Database->query('select configuration_group_id, configuration_group_title from :table_configuration_group where visible = 1 order by sort_order');
    $Qgroups->bindTable(':table_configuration_group', TABLE_CONFIGURATION_GROUP);
    $Qgroups->execute();

    while ($Qgroups->next()) {
      echo '        [\'' . osc_icon('configure.png') . '\', \'' . addslashes($Qgroups->value('configuration_group_title')) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'configuration&gID=' . $Qgroups->valueInt('configuration_group_id')) . '\', null, null],' . "\n";
    }

    echo '        [\'' . osc_icon('services.png') . '\', \'' . addslashes(BOX_CONFIGURATION_SERVICES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'services') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('wallet.png') . '\', \'' . addslashes(BOX_CONFIGURATION_CREDIT_CARD_TYPES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'credit_cards') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_CATALOG) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('folder_red.png') . '\', \'' . addslashes(BOX_CATALOG_CATEGORIES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'categories') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('products.png') . '\', \'' . addslashes(BOX_CATALOG_PRODUCTS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'products') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('attributes.png') . '\', \'' . addslashes(BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'products_attributes') . '\', \'\', null],' . "\n" .
         '        [\'' . osc_icon('run.png') . '\', \'' . addslashes(BOX_CATALOG_MANUFACTURERS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'manufacturers') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('write.png') . '\', \'' . addslashes(BOX_CATALOG_REVIEWS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'reviews') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('specials.png') . '\', \'' . addslashes(BOX_CATALOG_SPECIALS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'specials') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('date.png') . '\', \'' . addslashes(BOX_CATALOG_PRODUCTS_EXPECTED) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'products_expected') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_MODULES) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('payment.png') . '\', \'' . addslashes(BOX_MODULES_PAYMENT) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'modules&set=payment') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('install.png') . '\', \'' . addslashes(BOX_MODULES_SHIPPING) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'modules&set=shipping') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('calculator.png') . '\', \'' . addslashes(BOX_MODULES_ORDER_TOTAL) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'modules&set=ordertotal') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_CUSTOMERS) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('people.png') . '\', \'' . addslashes(BOX_CUSTOMERS_CUSTOMERS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'customers') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('orders.png') . '\', \'' . addslashes(BOX_CUSTOMERS_ORDERS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'orders') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_LOCATION_AND_TAXES) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('world.png') . '\', \'' . addslashes(BOX_TAXES_COUNTRIES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'countries') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('remote.png') . '\', \'' . addslashes(BOX_TAXES_ZONES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'zones') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('relationships.png') . '\', \'' . addslashes(BOX_TAXES_ZONE_GROUPS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'zone_groups') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('classes.png') . '\', \'' . addslashes(BOX_TAXES_TAX_CLASSES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'tax_classes') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_LOCALIZATION) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('currencies.png') . '\', \'' . addslashes(BOX_LOCALIZATION_CURRENCIES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'currencies') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('locale.png') . '\', \'' . addslashes(BOX_LOCALIZATION_LANGUAGES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'languages') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('status.png') . '\', \'' . addslashes(BOX_LOCALIZATION_ORDERS_STATUS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'orders_status') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('weight.png') . '\', \'' . addslashes(BOX_LOCALIZATION_WEIGHT_CLASSES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'weight_classes') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('status.png') . '\', \'' . addslashes(BOX_LOCALIZATION_IMAGE_GROUPS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'image_groups') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes('Templates') . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('file.png') . '\', \'' . addslashes('Templates') . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'templates') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('modules.png') . '\', \'' . addslashes('Boxes') . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'templates_modules&set=boxes') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('windows.png') . '\', \'' . addslashes('Modules') . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'templates_modules&set=content') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('file.png') . '\', \'' . addslashes('Layouts') . '\', null, null, null,' . "\n" .
         '            [\'' . osc_icon('modules.png') . '\', \'' . addslashes('Boxes') . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'templates_modules_layout&set=boxes') . '\', null, null],' . "\n" .
         '            [\'' . osc_icon('windows.png') . '\', \'' . addslashes('Modules') . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'templates_modules_layout&set=content') . '\', null, null],' . "\n" .
         '        ],' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_TOOLS) . '\', null, null, null,' . "\n" .
         '        [\'' . osc_icon('tape.png') . '\', \'' . addslashes(BOX_TOOLS_BACKUP) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'backup') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('windows.png') . '\', \'' . addslashes(BOX_TOOLS_BANNER_MANAGER) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'banner_manager') . '\', \'\', null],' . "\n" .
         '        [\'' . osc_icon('log.png') . '\', \'' . addslashes(BOX_TOOLS_CACHE) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'cache') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('weight.png') . '\', \'' . addslashes(BOX_TOOLS_IMAGES) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'images') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('file_manager.png') . '\', \'' . addslashes(BOX_TOOLS_FILE_MANAGER) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'file_manager') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('email_send.png') . '\', \'' . addslashes(BOX_TOOLS_NEWSLETTER_MANAGER) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'newsletters') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('server_info.png') . '\', \'' . addslashes(BOX_TOOLS_SERVER_INFO) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'server_info') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('statistics.png') . '\', \'' . addslashes(BOX_REPORTS_STATISTICS) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'statistics') . '\', null, null],' . "\n" .
         '        [\'' . osc_icon('people.png') . '\', \'' . addslashes(BOX_TOOLS_WHOS_ONLINE) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'whos_online') . '\', null, null]' . "\n" .
         '    ],' . "\n" .
         '    _cmSplit,' . "\n";
  }

  echo '    [null, \'' . addslashes(HEADER_TITLE_HELP) . '\', null, null, null,' . "\n" .
       '        [\'' . osc_icon('oscommerce.png') . '\', \'' . addslashes(HEADER_TITLE_OSCOMMERCE_SUPPORT_SITE) . '\', null, null, null,' . "\n" .
       '            [\'' . osc_icon('oscommerce.png') . '\', \'Support Site\', \'http://www.oscommerce.com\', \'_blank\', null],' . "\n" .
       '            [\'' . osc_icon('log.png') . '\', \'Knowledge Base\', \'http://www.oscommerce.info\', \'_blank\', null],' . "\n" .
       '            [\'' . osc_icon('people.png') . '\', \'Community Forums\', \'http://forums.oscommerce.com\', \'_blank\', null],' . "\n" .
       '            [\'' . osc_icon('run.png') . '\', \'Contributions\', \'http://www.oscommerce.com/community/contributions\', \'_blank\', null],' . "\n" .
       '            [\'' . osc_icon('configure.png') . '\', \'Bug Reporter\', \'http://www.oscommerce.com/community/bugs\', \'_blank\', null]' . "\n" .
       '        ],' . "\n" .
       '        [\'' . osc_icon('locale.png') . '\', \'' . addslashes(HEADER_TITLE_LANGUAGES) . '\', null, null, null,' . "\n";

  foreach ($osC_Language->getAll() as $l) {
    echo '            [\'' . osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image']) . '\', \'' . addslashes($l['name']) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'language=' . $l['code']) . '\', null, null],' . "\n";
  }

  echo '        ],' . "\n" .
       '        [\'' . osc_icon('home.png') . '\', \'' . addslashes(HEADER_TITLE_ONLINE_CATALOG) . '\', \'' . osc_href_link(null, null, 'NONSSL', false, false, true) . '\', \'_blank\', null],' . "\n" .
       '    ]' . "\n";

  if (isset($_SESSION['admin'])) {
    echo ',    _cmSplit,' . "\n" .
         '    [null, \'' . addslashes(BOX_HEADING_LOGOFF) . '\', \'' . osc_href_link_admin(FILENAME_DEFAULT, 'login&action=logoff') . '\', \'\', null]' . "\n";
  }

  echo '];' . "\n";
?>

//-->
</script>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT), osc_image('images/oscommerce.jpg', 'osCommerce, 3.0 Alpha 3 "Spekulatius"')); ?></td>
    <td width="150" align="right" class="smallText">
<?php
  if ($request_type == 'SSL') {
    echo sprintf(BOX_CONNECTION_PROTECTED, (isset($_SERVER['SSL_CIPHER_ALGKEYSIZE']) ? $_SERVER['SSL_CIPHER_ALGKEYSIZE'] . '-bit' : '<i>' . BOX_CONNECTION_UNKNOWN . '</i>')) . osc_icon('locked.png', ICON_LOCKED);
  } else {
    echo BOX_CONNECTION_UNPROTECTED . ' ' . osc_icon('unlocked.png', ICON_UNLOCKED);
  }
?>
    </td>
  </tr>
</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2" class="ThemeOfficeMainItem">
  <tr>
    <td id="administrationMenuID"></td>
  </tr>
</table>

<script type="text/javascript"><!--
  cmDraw('administrationMenuID', administrationMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
--></script>

<?php
  if ($osC_MessageStack->size('header') > 0) {
    echo $osC_MessageStack->output('header');
  }
?>
