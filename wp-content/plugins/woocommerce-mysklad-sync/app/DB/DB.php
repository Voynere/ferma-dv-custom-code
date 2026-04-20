<?php


namespace WCSTORES\WC\MS\DB;


use WCSTORES\WC\MS\Kernel\Singleton;
use WCSTORES\WC\MS\Wordpress\Actions\Actions;

/**
 * Class DB
 * @package WCSTORES\WC\MS\DB
 */
class DB extends Singleton
{

    /**
     * @var \wpdb
     */
    protected $wpdb;

    /**
     * @var
     */
    protected $sql;

    /**
     * @var
     */
    protected $where;
    /**
     * @var array|mixed|null
     */
    protected $wherePrepare = [];
    /**
     * @var array|mixed|string|null
     */
    protected $limit = 5;
    /**
     * @var array|mixed|string|null
     */
    protected $offset = 0;
    /**
     * @var array|mixed|string|null
     */
    protected $groupBy;
    /**
     * @var array|mixed|null
     */
    protected $orderBy;


    /**
     * DB constructor.
     */
    protected function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this|array|mixed|null
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'get') === false) {
            call_user_func_array(array($this, $method), $parameters);
            return $this;
        }

        return call_user_func_array(array($this, $method), $parameters);

    }

    /**
     * @return \wpdb
     */
    public function wpdb()
    {
        return $this->wpdb;
    }

    /**
     * @return string
     */
    public function prefix()
    {
        return $this->wpdb->prefix;
    }

    /**
     * @return string
     */
    public function collate()
    {
        $collate = '';

        if ($this->wpdb()->has_cap('collation')) {
            $collate = $this->wpdb()->get_charset_collate();
        }

        return $collate;
    }


    /**
     * @param $tableName
     * @param $schema
     * @return array
     */
    public function createTable($tableName, $schema)
    {
        if (!$this->isTableExists($tableName)) {
            \WmsLogs::set_logs('Создание таблицы в бд ' . $tableName, true);
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $dbDelta = dbDelta($schema);
            \WmsLogs::set_logs($dbDelta, true);

            return $dbDelta;

        }
    }

    /**
     * @param $tableName
     * @return string|null
     */
    public function isTableExists($tableName)
    {
        return $this->wpdb()->get_var("SHOW TABLES LIKE '%{$tableName}%'");
    }

    /**
     * @param $tableName
     * @param $key
     * @param $value
     * @return bool|int|\mysqli_result|resource
     * @throws \Exception
     */
    public function deleteById($tableName, $key, $value)
    {
        return $this->delete($tableName, [$key => $value]);
    }

    /**
     * @param $tableName
     * @param $where
     * @return bool|int|\mysqli_result|resource
     */
    public function delete($tableName, $where)
    {
        if ($this->isTableExists($tableName)) {
            return $this->wpdb()->delete($this->prefix() . $tableName, $where);
        }
    }

    /**
     * @param $tableName
     * @return bool|int|\mysqli_result|resource
     */
    public function clearTable($tableName)
    {
        if ($this->isTableExists($tableName)) {
            return $this->wpdb()->query("DELETE FROM " . $this->prefix() . $tableName);
        }
    }

    /**
     * @param $tableName
     * @param $where
     * @return bool|int|\mysqli_result
     * @throws \Exception
     */
    public function clearTableWithWhere($tableName, $where)
    {
        if ($this->isTableExists($tableName)) {

            $sql = $this->getPreparationSql();

            if ($this->wherePrepare) {
                $sql = $this->wpdb()->prepare(
                    $sql,
                    $this->wherePrepare
                );
            }

            return $this->wpdb()->query("DELETE FROM " . $this->prefix() . $tableName);
        }
    }


    /**
     * @param $tableName
     * @throws \Exception
     */
    public function isTableChecking($tableName)
    {
        if (!$this->isTableExists($tableName)) {
            Actions::do('is_table_checking_not', $tableName);
            throw new \Exception('Таблицы не существует ' . $tableName);
        }
    }

    /**
     * @param $tableName
     * @param $data
     * @param array $prepare
     * @return bool|int
     * @throws \Exception
     */
    public function insert($tableName, $data, $prepare = [])
    {
        $this->isTableChecking($tableName);
        return $this->wpdb()->insert($this->prefix() . $tableName, $this->prepareData($data), $prepare);
    }


    /**
     * @param $tableName
     * @param $data
     * @param $where
     * @param array $prepare
     * @return bool|int
     * @throws \Exception
     */
    public function update($tableName, $data, $where, $prepare = [])
    {
        $this->isTableChecking($tableName);
        return $this->wpdb()->update($this->prefix() . $tableName, $this->prepareData($data), $where, $prepare);
    }

    /**
     * @param $data
     * @return array|array[]|object[]|string[]
     */
    public function prepareData($data)
    {
        return array_map(function ($data) {
            return maybe_serialize($data);
        }, $data);
    }

    /**
     * @param string $tableName
     * @return array|object|null
     * @throws \Exception
     */
    public function get($tableName = '')
    {
        $this->isTableChecking($tableName);

        $sql = $this->getPreparationSql();

        if ($this->wherePrepare) {
            $sql = $this->wpdb()->prepare(
                $sql,
                $this->wherePrepare
            );
        }
        return $this->wpdb()->get_results($sql);

    }

    /**
     * @param $sql
     * @return bool|int|\mysqli_result
     */
    public function query($sql)
    {
        return $this->wpdb()->query($sql);
    }

    /**
     * @param $tableName
     * @param $where
     * @return string|null
     * @throws \Exception
     */
    public function getCount($tableName, $where)
    {
        $this->isTableChecking($tableName);

        return $this->wpdb()->get_var("SELECT COUNT(*) FROM " . $this->prefix() . $tableName . $this->whereToSql($where));
    }

    /**
     * @return string
     */
    protected function select()
    {
        $this->where = [];
        $this->wherePrepare = [];

        return $this->sql = "SELECT * ";
    }

    /**
     * @param $tableName
     * @return string
     */
    protected function from($tableName)
    {
        return $this->sql .= " FROM  " . $this->prefix() . $tableName . " AS $tableName";
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getPreparationSql()
    {
        if ($this->where) {
            $this->sql .= $this->whereToSql($this->where, true);
        }

        $this->sql .= " LIMIT $this->limit ";
        $this->sql .= " OFFSET $this->offset";

        return $this->sql;

    }

    /**
     * @param $where
     * @param false $isWherePrepare
     * @return string
     * @throws \Exception
     */
    public function whereToSql($where, $isWherePrepare = false)
    {
        if (!is_array($where)) {
            throw new \Exception('whereToSql: $where not array');
        }

        $sql = " WHERE ";

        foreach ($where as $i => $where) {

            $sql .= $where['field'];
            $sql .= ' ' . $where['operator'] . ' ';

            if (($where['operator'] == 'IN' or $where['operator'] == 'NOT IN')) {

                $sql .= $this->whereIn($where['value']);

            } else {

                if ($isWherePrepare) {
                    $sql .= ' %s ';
                    $this->wherePrepare[] = $where['value'];
                } else {
                    $sql .= " '{$where['value']}' ";
                }

            }

            if (isset($where[$i + 1])) {
                $sql .= " AND ";
            }

        }

        return $sql;

    }

    /**
     * @param mixed ...$args
     * @return mixed
     */
    protected function where(...$args)
    {
        $field = $args[0];
        $operator = (isset($args[2])) ? $args[1] : '=';
        $value = (isset($args[2])) ? $args[2] : $args[1];

        $this->where[] = ['field' => $field, 'operator' => $operator, 'value' => $value];

        return $this->where;

    }


    /**
     * @param $values
     * @return string
     */
    protected function whereIn($values)
    {
        $prepare = [];

        foreach ($values as $value) {
            $prepare[] = " %s ";
            $this->wherePrepare[] = $value;
        }

        return '(' . implode(',', $prepare) . ')';
    }

    /**
     * @param $limit
     * @return mixed
     */
    protected function limit($limit)
    {
        return $this->limit = $limit;
    }


    /**
     * @param $offset
     * @return mixed
     */
    protected function offset($offset)
    {
        return $this->offset = $offset;
    }


    /**
     * @param $groupBy
     * @return mixed
     */
    protected function groupBy($groupBy)
    {
        return $this->groupBy = $groupBy;
    }


    /**
     * @param $orderBy
     * @return mixed
     */
    protected function orderBy($orderBy)
    {
        return $this->orderBy = $orderBy;
    }

    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }
}