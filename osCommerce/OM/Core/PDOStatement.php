<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright (c) 2015 osCommerce; http://www.oscommerce.com
 * @license BSD; http://www.oscommerce.com/bsdlicense.txt
 */

namespace osCommerce\OM\Core;

use osCommerce\OM\Core\{
    HTML,
    Registry
};

/**
 * Represents a prepared statement and, after the statement is executed, an
 * associated result set.
 */

class PDOStatement extends \PDOStatement
{
    protected $_is_error = false;
    protected $_binded_params = [];
    protected $_cache_key;
    protected $_cache_expire;
    protected $_cache_data;
    protected $_cache_read = false;
    protected $_cache_empty = false;
    protected $_query_call;

    public function bindValue($parameter, $value, $data_type = \PDO::PARAM_STR): bool
    {
        $this->_binded_params[$parameter] = [
            'value' => $value,
            'data_type' => $data_type
        ];

        return parent::bindValue($parameter, $value, $data_type);
    }

    public function bindInt(string $parameter, int $value): bool
    {
        return $this->bindValue($parameter, $value, \PDO::PARAM_INT);
    }

    public function bindBool(string $parameter, bool $value): bool
    {
        return $this->bindValue($parameter, $value, \PDO::PARAM_BOOL);
    }

    public function bindNull(string $parameter): bool
    {
        return $this->bindValue($parameter, null, \PDO::PARAM_NULL);
    }

    public function execute($input_parameters = []): bool
    {
        if (isset($this->_cache_key)) {
            $OSCOM_Cache = Registry::get('Cache');

            if ($OSCOM_Cache->read($this->_cache_key, $this->_cache_expire)) {
                $this->_cache_data = $OSCOM_Cache->getCache();

                $this->_cache_read = true;
            }
        }

        if ($this->_cache_read === false) {
            if (empty($input_parameters)) {
                $input_parameters = null;
            }

            $this->_is_error = !parent::execute($input_parameters);

            if ($this->_is_error === true) {
                trigger_error($this->queryString);
            }
        }

        return !$this->_is_error;
    }

    public function fetch($fetch_style = \PDO::FETCH_ASSOC, $cursor_orientation = \PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
    {
        if ($this->_cache_read === true) {
            list(, $this->result) = each($this->_cache_data);
        } else {
            $this->result = parent::fetch($fetch_style, $cursor_orientation, $cursor_offset);

            if (isset($this->_cache_key) && ($this->result !== false)) {
                if (!isset($this->_cache_data)) {
                    $this->_cache_data = [];
                }

                $this->_cache_data[] = $this->result;
            }
        }

        return $this->result;
    }

    public function fetchAll($fetch_style = \PDO::FETCH_ASSOC, $fetch_argument = null, $ctor_args = []): array
    {
        if ($this->_cache_read === true) {
            $this->result = $this->_cache_data;
        } else {
// fetchAll() fails if second argument is passed in a fetch style that does not
// use the optional argument
            if (in_array($fetch_style, [
                \PDO::FETCH_COLUMN,
                \PDO::FETCH_CLASS,
                \PDO::FETCH_FUNC
            ])) {
                $this->result = parent::fetchAll($fetch_style, $fetch_argument, $ctor_args);
            } else {
                $this->result = parent::fetchAll($fetch_style);
            }

            if (isset($this->_cache_key) && ($this->result !== false)) {
                $this->_cache_data = $this->result;
            }
        }

        return $this->result;
    }

    public function toArray()
    {
        if (!isset($this->result)) {
            $this->fetch();
        }

        return $this->result;
    }

/**
 * @param string $key The key name for the cache data
 * @param int $expire The amount of minutes the cach data is active for
 * @param bool $cache_empty Save empty cache data (@since v3.0.3)
 * @access public
 */

    public function setCache(string $key, int $expire = 0, bool $cache_empty = false)
    {
        $this->_cache_key = basename($key);
        $this->_cache_expire = $expire;
        $this->_cache_empty = $cache_empty;

        if ($this->_query_call != 'prepare') {
            trigger_error('osCommerce\\OM\\Core\\PDOStatement::setCache(): Cannot set cache (\'' . $this->_cache_key . '\') on a non-prepare query. Please change the query to a prepare() query.');
        }
    }

    protected function valueMixed(string $column, string $type = 'string')
    {
        if (!isset($this->result)) {
            $this->fetch();
        }

        switch ($type) {
            case 'protected':
                return HTML::outputProtected($this->result[$column]);
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

    public function value(string $column): string
    {
        return $this->valueMixed($column, 'string');
    }

    public function valueProtected(string $column): string
    {
        return $this->valueMixed($column, 'protected');
    }

    public function valueInt(string $column): int
    {
        return $this->valueMixed($column, 'int');
    }

    public function valueDecimal(string $column): float
    {
        return $this->valueMixed($column, 'decimal');
    }

    public function isError(): bool
    {
        return $this->_is_error;
    }

/**
 * Return the query string
 */

    public function getQuery(): string
    {
        return $this->queryString;
    }

    public function setQueryCall(string $type)
    {
        $this->_query_call = $type;
    }

    public function getQueryCall(): string
    {
        return $this->_query_call;
    }

    public function __destruct()
    {
        if (($this->_cache_read === false) && isset($this->_cache_key) && is_array($this->_cache_data)) {
            if ($this->_cache_empty || ($this->_cache_data[0] !== false)) {
                Registry::get('Cache')->write($this->_cache_data, $this->_cache_key);
            }
        }
    }
}
