<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

/**
 * @since v3.0.3
 */

  abstract class ProductAttributeModuleAbstract {
    protected $_title;

    abstract public function setFunction($value);

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/ProductAttribute/' . $this->getCode() . '.php');

      $this->_title = OSCOM::getDef('product_attribute_' . $this->getCode() . '_title');
    }

    public function getID() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qmodule = $OSCOM_PDO->prepare('select id from :table_templates_boxes where code = :code and modules_group = :modules_group');
      $Qmodule->bindValue(':code', $this->getCode());
      $Qmodule->bindValue(':modules_group', 'ProductAttribute');
      $Qmodule->execute();

      return ( $Qmodule->fetch() !== false ) ? $Qmodule->valueInt('id') : 0;
    }

    public function getCode() {
      $tmp = explode('\\', get_class($this));

      return end($tmp);
    }

    public function getTitle() {
      return $this->_title;
    }

    public function isInstalled() {
      return ($this->getID() > 0);
    }

    public function install() {
      $OSCOM_PDO = Registry::get('PDO');

      $Qinstall = $OSCOM_PDO->prepare('insert into :table_templates_boxes (title, code, author_name, author_www, modules_group) values (:title, :code, :author_name, :author_www, :modules_group)');
      $Qinstall->bindValue(':title', $this->getTitle());
      $Qinstall->bindValue(':code', $this->getCode());
      $Qinstall->bindValue(':author_name', '');
      $Qinstall->bindValue(':author_www', '');
      $Qinstall->bindValue(':modules_group', 'ProductAttribute');
      $Qinstall->execute();

      return ( $Qinstall->isError() === false );
    }

    public function uninstall() {
      $OSCOM_PDO = Registry::get('PDO');

      $error = false;

      $OSCOM_PDO->beginTransaction();

      $Qdelete = $OSCOM_PDO->prepare('delete from :table_product_attributes where id = :id');
      $Qdelete->bindInt(':id', $this->getID());
      $Qdelete->execute();

      if ( $Qdelete->isError() ) {
        $error = true;
      }

      if ( $error === false ) {
        $Quninstall = $OSCOM_PDO->prepare('delete from :table_templates_boxes where code = :code and modules_group = :modules_group');
        $Quninstall->bindValue(':code', $this->getCode());
        $Quninstall->bindValue(':modules_group', 'ProductAttribute');
        $Quninstall->execute();

        if ( $Quninstall->isError() ) {
          $error = true;
        }
      }

      if ( $error === false ) {
        $OSCOM_PDO->commit();
      } else {
        $OSCOM_PDO->rollBack();
      }

      return ( $error === false );
    }
  }
?>
