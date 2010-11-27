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
  use \PDOStatement;

  class DatabasePDOStatement extends PDOStatement {
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
      $OSCOM_Database = Registry::get('PDO');

      if ( $OSCOM_Database->hasNativeForeignKeys() === false ) {
        $query_action = strtolower(substr($this->queryString, 0, strpos($this->queryString, ' ')));

        if ( ($query_action == 'delete') || ($query_action == 'update') ) {
          $OSCOM_Database->setupForeignKeys();
        }

        if ($query_action == 'delete') {
          $query_data = explode(' ', $this->queryString, 4);
          $query_table = substr($query_data[2], strlen(DB_TABLE_PREFIX));

          if ( $OSCOM_Database->hasForeignKey($query_table) ) {
// check for RESTRICT constraints first
            foreach ( $OSCOM_Database->getForeignKeys($query_table) as $fk ) {
              if ( $fk['on_delete'] == 'restrict' ) {
                $Qchild = $OSCOM_Database->prepare('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);

                foreach ( $this->_binded_params as $key => $value ) {
                  $Qchild->bindValue($key, $value['value'], $value['data_type']);
                }

                $Qchild->execute();

                while ( $Qchild->next() ) {
                  $Qcheck = $OSCOM_Database->prepare('select ' . $fk['from_field'] . ' from ' . DB_TABLE_PREFIX .  $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $Qchild->value($fk['to_field']) . '" limit 1');
                  $Qcheck->execute();

                  if ( count($Qcheck->fetchAll()) === 1 ) {
//                    $this->db_class->setError('RESTRICT constraint condition from table ' . DB_TABLE_PREFIX .  $fk['from_table'], null, $this->sql_query);

                    return false;
                  }
                }
              }
            }

            foreach ( $OSCOM_Database->getForeignKeys($query_table) as $fk ) {
              $Qparent = $OSCOM_Database->prepare('select * from ' . $query_data[2] . ' ' . $query_data[3]);

              foreach ( $this->_binded_params as $key => $value ) {
                $Qparent->bindValue($key, $value['value'], $value['data_type']);
              }

              $Qparent->execute();

              while ( $Qparent->next() ) {
                if ( $fk['on_delete'] == 'cascade' ) {
                  $Qdel = $OSCOM_Database->prepare('delete from ' . DB_TABLE_PREFIX . $fk['from_table'] . ' where ' . $fk['from_field'] . ' = :' . $fk['from_field']);
                  $Qdel->bindValue(':' . $fk['from_field'], $Qparent->value($fk['to_field']));
                  $Qdel->execute();
                } elseif ( $fk['on_delete'] == 'set_null' ) {
                  $Qupdate = $OSCOM_Database->prepare('update ' . DB_TABLE_PREFIX . $fk['from_table'] . ' set ' . $fk['from_field'] . ' = null where ' . $fk['from_field'] . ' = :' . $fk['from_field']);
                  $Qupdate->bindValue(':' . $fk['from_field'], $Qparent->value($fk['to_field']));
                  $Qupdate->execute();
                }
              }
            }
          }
        } elseif ($query_action == 'update') {
          $query_data = explode(' ', $this->queryString, 3);
          $query_table = substr($query_data[1], strlen(DB_TABLE_PREFIX));

          if ( $OSCOM_Database->hasForeignKey($query_table) ) {
// check for RESTRICT constraints first
            foreach ( $OSCOM_Database->getForeignKeys($query_table) as $fk ) {
              if ( $fk['on_update'] == 'restrict' ) {
                $Qchild = $OSCOM_Database->prepare('select ' . $fk['to_field'] . ' from ' . $query_data[2] . ' ' . $query_data[3]);

                foreach ( $this->_binded_params as $key => $value ) {
                  $Qchild->bindValue($key, $value['value'], $value['data_type']);
                }

                $Qchild->execute();

                while ( $Qchild->next() ) {
                  $Qcheck = $OSCOM_Database->prepare('select ' . $fk['from_field'] . ' from ' . DB_TABLE_PREFIX .  $fk['from_table'] . ' where ' . $fk['from_field'] . ' = "' . $Qchild->value($fk['to_field']) . '" limit 1');
                  $Qcheck->execute();

                  if ( count($Qcheck->fetchAll()) === 1 ) {
//                    $this->db_class->setError('RESTRICT constraint condition from table ' . DB_TABLE_PREFIX .  $fk['from_table'], null, $this->sql_query);

                    return false;
                  }
                }
              }
            }

            foreach ( $OSCOM_Database->getForeignKeys($query_table) as $fk ) {
// check to see if foreign key column value is being changed
              if ( strpos(substr($this->queryString, strpos($this->queryString, ' set ')+4, strpos($this->queryString, ' where ') - strpos($this->queryString, ' set ') - 4), ' ' . $fk['to_field'] . ' ') !== false ) {
                $Qparent = $OSCOM_Database->prepare('select * from ' . $query_data[1] . substr($this->queryString, strrpos($this->queryString, ' where ')));

                foreach ( $this->_binded_params as $key => $value ) {
                  if ( preg_match('/:\b' . substr($key, 1) . '\b/', $Qparent->queryString) ) {
                    $Qparent->bindValue($key, $value['value'], $value['data_type']);
                  }
                }

                $Qparent->execute();

                while ( $Qparent->next() ) {
                  if ( ($fk['on_update'] == 'cascade') || ($fk['on_update'] == 'set_null') ) {
                    $on_update_value = '';

                    if ( $fk['on_update'] == 'cascade' ) {
                      $on_update_value = $this->_binded_params[':' . $fk['to_field']]['value'];
                    }

                    $Qupdate = $OSCOM_Database->prepare('update ' . DB_TABLE_PREFIX . $fk['from_table'] . ' set ' . $fk['from_field'] . ' = :' . $fk['from_field'] . ' where ' . $fk['from_field'] . ' = :' . $fk['from_field'] . '_orig');

                    if ( empty($on_update_value) ) {
                      $Qupdate->bindNull(':' . $fk['from_field']);
                    } else {
                      $Qupdate->bindValue(':' . $fk['from_field'], $on_update_value);
                    }

                    $Qupdate->bindValue(':' . $fk['from_field'] . '_orig', $Qparent->value($fk['to_field']));
                    $Qupdate->execute();
                  }
                }
              }
            }
          }
        }
      }

      if ( empty($input_parameters) ) {
        $input_parameters = null;
      }

      return parent::execute($input_parameters);
    }

    public function next() {
/*
      if ($this->cache_read === true) {
        list(, $this->result) = each($this->cache_data);
      } else {
*/

        $this->result = $this->fetch();

/*
        if (isset($this->cache_key)) {
          $this->cache_data[] = $this->result;
        }
      }
*/

      return $this->result;
    }

    protected function valueMixed($column, $type = 'string') {
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
  }
?>
