<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Application\Configuration\Action;

  use osCommerce\OM\Core\ApplicationAbstract;
  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\Site\Admin\Application\Configuration\Configuration;

  class BatchSaveEntries {
    public static function execute(ApplicationAbstract $application) {
      if ( isset($_POST['batch']) && is_array($_POST['batch']) && !empty($_POST['batch']) ) {
        $OSCOM_Template = Registry::get('Template');

        $application->setPageContent('entries_batch_edit.html');

        $cfg_input_fields = array();

        foreach ( array_unique(array_filter($_POST['batch'], 'is_numeric')) as $cfg_id ) {
          $cfg = Configuration::getEntry($cfg_id);

          $rumpel = array();

          if ( strlen($cfg['set_function']) > 0 ) {
            $rumpel['input_field'] = Configuration::callUserFunc($cfg['set_function'], $cfg['configuration_value'], $cfg['configuration_key']);
          } else {
            $rumpel['input_field'] = HTML::inputField('configuration[' . $cfg['configuration_key'] . ']', $cfg['configuration_value']);
          }

          $rumpel['key'] = $cfg['configuration_key'];
          $rumpel['title'] = HTML::outputProtected($cfg['configuration_title']);
          $rumpel['description'] = $cfg['configuration_description'];

          $cfg_input_fields[] = $rumpel;
        }

        $OSCOM_Template->setValue('cfg_input_fields', $cfg_input_fields);
      }
    }
  }
?>
