<?php
class DB
{
    private $host = "localhost";
    private $username = 'root';
    private $password = '';
    private $database = 'movie_db';

    public $mysql;
    public $res;

    public function __construct()
    {
        try {
            if (!$this->mysql = new mysqli($this->host, $this->username, $this->password, $this->database)) {
                throw new Exception("Error connecting to the database");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    public function select($table, $row = "*", $where = null)
    {
        $sql = "SELECT {$row} FROM {$table}";
        if ($where != null) {
            $sql .= " WHERE {$where}";
        }
        $result = $this->mysql->query($sql);
        return $this->fetchSelect($result);
    }
    public function fetchSelect($result)
    {
        $records = array();
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        $this->res = $records;
        return $this->res = $records;
    }
    public function insert($table, $data)
    {
        $sql = "INSERT INTO {$table} (";
        $sql .= implode(",", array_keys($data)) . ') VALUES (';
        $sql .= "'" . implode("','", array_values($data)) . "')";
        $this->mysql->query($sql);
    }
    public function update($table, $data, $where = null)
    {
        $setValues = "";
        foreach ($data as $key => $value) {
            $setValues .= "$key = '$value', ";
        }
        $setValues = rtrim($setValues, ', ');

        $sql = "UPDATE {$table} SET {$setValues}";
        if ($where != null) {
            $sql .= " WHERE {$where}";
        }
        $this->mysql->query($sql);
    }
    public function delete($table, $where = null)
    {
        $sql = "DELETE FROM {$table}";
        if ($where != null) {
            $sql .= " WHERE {$where}";
        }
        $this->mysql->query($sql);
    }
    public function __destruct()
    {
        $this->mysql?->close();
    }
}
