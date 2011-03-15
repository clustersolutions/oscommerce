<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  use \PDO;

  class DatabasePDOStatement extends \PDOStatement {
    protected $_is_error = false;
    protected $_binded_params = array();

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR) {
      $this->_binded_params[$parameter] = array('value' => $value,
                                                'data_type' => $data_type);

      return parent::bindValue($parameter, $value, $data_type);
    }

    public function bindInt($parameter, $value) {
// force type to int (see http://bugs.php.net/bug.php?id=44639)
      return $this->bindValue($parameter, (int)$value, PDO::PARAM_INT);
    }

    public function bindBool($parameter, $value) {
// force type to bool (see http://bugs.php.net/bug.php?id=44639)
      return $this->bindValue($parameter, (bool)$value, PDO::PARAM_BOOL);
    }

    public function bindNull($parameter) {
      return $this->bindValue($parameter, null, PDO::PARAM_NULL);
    }

    public function execute($input_parameters = array()) {
      if ( empty($input_parameters) ) {
        $input_parameters = null;
      }

      $this->_is_error = !parent::execute($input_parameters);

      return $this->_is_error;
    }

    public function fetch($fetch_style = PDO::FETCH_ASSOC, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0) {
/*
      if ($this->cache_read === true) {
        list(, $this->result) = each($this->cache_data);
      } else {
*/

        $this->result = parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);

/*
        if (isset($this->cache_key)) {
          $this->cache_data[] = $this->result;
        }
      }
*/

      return $this->result;
    }

    public function next() {
      return $this->fetch();
    }

    protected function valueMixed($column, $type = 'string') {
      if ( !isset($this->result) ) {
        $this->fetch();
      }

      switch ($type) {
        case 'protected':
          return osc_output_string_protected($this->result[$column]);
          break;
        case 'int':
          return (int)$this->result[$column];
          break;
        case 'decimal':
          return (float)$this->result[$column];
          break;
        case 'string':
        default:
          return $this->result[$column];
      }
    }

    public function value($column) {
      return $this->valueMixed($column, 'string');
    }

    public function valueProtected($column) {
      return $this->valueMixed($column, 'protected');
    }

    public function valueInt($column) {
      return $this->valueMixed($column, 'int');
    }

    public function valueDecimal($column) {
      return $this->valueMixed($column, 'decimal');
    }

    public function isError() {
      return $this->_is_error;
    }
  }
?>
