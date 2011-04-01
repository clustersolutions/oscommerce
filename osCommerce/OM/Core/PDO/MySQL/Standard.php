<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2011 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\PDO\MySQL;

  use \PDO;

  use osCommerce\OM\Core\OSCOM;

  class Standard extends \osCommerce\OM\Core\PDO {
    protected $_has_native_fk = false;
    protected $_fkeys = array();

    public function __construct($server, $username, $password, $database, $port, $driver_options) {
      $this->_server = $server;
      $this->_username = $username;
      $this->_password = $password;
      $this->_database = $database;
      $this->_port = $port;
      $this->_driver_options = $driver_options;

// Override ATTR_STATEMENT_CLASS to automatically handle foreign key constraints
      if ( $this->_has_native_fk === false ) {
        $this->_driver_options[PDO::ATTR_STATEMENT_CLASS] = array('osCommerce\\OM\\Core\\PDO\\MySQL\\Standard\\PDOStatement', array($this));
      }

// Only one init command can be issued (see http://bugs.php.net/bug.php?id=48859)
      $this->_driver_options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'set session sql_mode="STRICT_ALL_TABLES", names utf8';

      return $this->connect();
    }

    public function connect() {
      $dsn = 'mysql:dbname=' . $this->_database . ';host=' . $this->_server;

      if ( !empty($this->_port) ) {
        $dsn .= ';port=' . $this->_port;
      }

      $this->_connected = true;

      $dbh = parent::__construct($dsn, $this->_username, $this->_password, $this->_driver_options);

      if ( (OSCOM::getSite() != 'Setup') && $this->_has_native_fk === false ) {
        $this->setupForeignKeys();
      }

      return $dbh;
    }

    public function getForeignKeys($table = null) {
      if ( isset($table) ) {
        return $this->_fkeys[$table];
      }

      return $this->_fkeys;
    }

    public function setupForeignKeys() {
      $Qfk = $this->query('select * from :table_fk_relationships');
      $Qfk->setCache('fk_relationships');
      $Qfk->execute();

      while ( $Qfk->fetch() ) {
        $this->_fkeys[$Qfk->value('to_table')][] = array('from_table' => $Qfk->value('from_table'),
                                                         'from_field' => $Qfk->value('from_field'),
                                                         'to_field' => $Qfk->value('to_field'),
                                                         'on_update' => $Qfk->value('on_update'),
                                                         'on_delete' => $Qfk->value('on_delete'));
      }
    }

    public function hasForeignKey($table) {
      return isset($this->_fkeys[$table]);
    }
  }
?>
