<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin\Module\Template\Value\template_footer_file;

  use osCommerce\OM\Core\Registry;

  class Controller extends \osCommerce\OM\Core\Template\ValueAbstract {
    static public function execute() {
      $OSCOM_Template = Registry::get('Template');

      return $OSCOM_Template->getTemplateFile('footer.html');
    }
  }
?>
