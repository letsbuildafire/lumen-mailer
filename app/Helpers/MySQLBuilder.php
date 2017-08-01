<?php 

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Log;
use DB;

class MySQLBuilder
{

    protected $table;
    protected $pdoPlaceholderLimit;
    protected $columns;
    protected $columnString;
    protected $updateColumns;
    protected $updateString;

    public function __construct($table, $pdoPlaceholderLimit = '60000')
    {
        $this->table = $table;
        $this->pdoPlaceholderLimit = $pdoPlaceholderLimit;
    }

    public function chunk(array $valueCollection)
    {
        $valueList = head($valueCollection);
        return array_chunk($valueCollection, $this->getMaxChunkSize($valueList), true);
    }

    /**
     * Must call this before calling insertOrUpdate
     * @param array $columns
     */
    public function setColumns(array $columns)
    {
        $this->columns = $columns;
        $this->updateColumns = $columns;
        $this->columnString = $this->buildColumnString($columns);
        $this->updateString = $this->buildUpdateString($columns);
    }

    /**
     * Call this before calling insertOrUpdate to modify the columns to update.
     * @param array $columns
     */
    public function setUpdateColumns(array $columns)
    {
        $this->updateColumns = $columns;
        $this->updateString = $this->buildUpdateString($columns);
    }

    /**
     * Execute the INSERT ON DUPLICATE KEY UPDATE mysql statement
     * @param array $valueChunk
     * @return int
     */
    public function insertOrUpdate(array $valueChunk)
    {
        $valuePlaceholders = $this->buildValuePlaceholderString($valueChunk);
        $bindings = array_flatten($valueChunk);
        $query = "INSERT INTO {$this->table} ({$this->columnString}) VALUES $valuePlaceholders ON DUPLICATE KEY UPDATE {$this->updateString}";
        return DB::affectingStatement($query, $bindings);
    }

    /**
     * @return string
     */
    protected function buildColumnString(array $columns)
    {
        return '`' . implode('`,`', $columns) . '`';
    }

    /**
     * @return string
     */
    protected function buildUpdateString(array $columns)
    {
        $updates = '';
        foreach($columns as $column)
        {
            $updates .= "`{$column}`=VALUES(`{$column}`),";
        }
        return rtrim($updates, ',');
    }

    /**
     * @param array $valueCollection  a chunk of values to build placeholders for
     * @return string
     */
    protected function buildValuePlaceholderString(array $valueCollection)
    {
        $placeholder = '';
        foreach ($valueCollection as $attributes)
        {
            $placeholder .= '(' . str_repeat("TRIM(?),", count($attributes));
            $placeholder = rtrim($placeholder, ',');
            $placeholder .= '),';
        }
        return rtrim($placeholder, ',');
    }

    protected function getMaxChunkSize(array $valueList)
    {
        // round the number down by casting to an int
        return (int) ($this->pdoPlaceholderLimit / count($valueList));
    }

}
