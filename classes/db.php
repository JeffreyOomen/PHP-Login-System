<?php
/**
 * This class makes use of the singleton pattern which
 * prevents the occurrence of multiple connection with the
 * same database.
 * Created by Jeffrey Oomen on 12/08/2016.
 */
class DB {
    private static $_instance = null; // the singleton instance of this class
    private $_pdo, // the pdo connection
            $_query, // the last query being executed
            $_error = false, // indicates if there were error during execution
            $_results, // the last result of the last query being executed
            $_count = 0; // the amount of rows in the last result

    private function __construct() {
        try {
            $this->_pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Core function to create a singleton instance of this class
     * if not already exists. If do exist, the existing instance is returned.
     * @return DB|null the singleton instance of this class.
     */
    public static function getInstance() {
        if(!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    /**
     * This method does the actual querying on the database.
     * @param $sql the full sql statement as a string
     * @param array $params parameters for a possible where clause (?)
     * @return $this the singleton instance which holds the result as attribute
     */
    public function query($sql, $params = array()) {
        $this->_error = false; // need to be set on false in case it is true due to a previous attempt which failed

        if($this->_query = $this->_pdo->prepare($sql)) {
            $x = 1;
            if(count($params)) {
                foreach($params as $param) {
                    // Binds the parameters to the question marks in the query.
                    // The $x is used to point to the location of the question mark
                    // where the parameter should be inserted.
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            if($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ); // we want the data to be in a default object of php
                $this->_count = $this->_query->rowCount(); // sets the row count of the result
            } else {
                $this->_error = true;
            }
        }

        return $this;
    }

    /**
     * This method will build up a query statement based on the values
     * of the given parameters.
     * @param $action can be SELECT * or SELECT firstname, lastname (etc) or DELETE
     * @param $table the table which needs to be queried
     * @param array $where an array which contains three items: field, operator and value
     * @return $this|bool the singleton instance is returned if query was success, false otherwise
     */
    public function action($action, $table, $where = array()) {
        if(count($where) === 3) { // check if three array items are present
            $operators = array('=', '>', '<', '>=', '<=');

            $field = $where[0];
            $operator = $where[1];
            $value = $where[2];

            if(in_array($operator, $operators)) { // check if given operator in array exists
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";

                if(!$this->query($sql, array($value))->error()) { // if query has no errors
                    return $this;
                }
            }
        }

        return false;
    }

    /**
     * This method will construct an insert sql statement.
     * @param $table the table in which something needs to be inserted
     * @param array $fields an array with key value pairs with the key
     * representing the column name and the value representing the value.
     * @return bool true if query was success, false otherwise.
     */
    public function insert($table, $fields = array()) {
        $keys = array_keys($fields);
        $values = null;
        $x = 1;

        // Basically in the case of three fields this foreach loop will
        // construct this: ?, ?, ?
        foreach($fields as $field) {
            $values .= '?'; // to indicate where values needs to be inserted
            if ($x < count($fields)) {
                $values .= ', '; // to separate the values from each other
            }
            $x++;
        }

        $sql = "INSERT INTO {$table} (`" . implode('`, `', $keys) . "`) VALUES ({$values})";

        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    /**
     * This method will construct an update sql statement based
     * on the given parameters.
     * @param $table the table on which an update needs to take place
     * @param $id the id of the record which needs updating
     * @param $fields an array with key value pairs in which the key
     * represents the column and the value the value.
     * @return bool True if query was success, false otherwise.
     */
    public function update($table, $id, $fields) {
        $set = '';
        $x = 1;

        // Use 'as $name => $value' when you need both the key
        // and the value.
        foreach($fields as $name => $value) {
            $set .= "{$name} = ?";
            if($x < count ($fields)) {
                $set .= ', ';
            }
            $x++;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";

        if(!$this->query($sql, $fields)->error()) {
            return true;
        }

        return false;
    }

    /**
     * This method will delete a certain record based on the
     * given parameters.
     * @param $table the table from which a record needs to be deleted
     * @param $where an array with three items, field, operator and value
     * @return bool|DB the singleton instance when success, false otherwise.
     */
    public function delete($table, $where) {
        return $this->action('DELETE ', $table, $where);
    }

    /**
     * This method will get records from the database from
     * the given table and based on the where condition.
     * @param $table the table from which results needs to be fetched
     * @param $where an array with three items, field, operator and value
     * @return bool|DB the singleton instance when success, false otherwise.
     */
    public function get($table, $where) {
        return $this->action('SELECT *', $table, $where); // will return db object
    }

    public function results() {
        return $this->_results;
    }

    /**
     * This method will return the first item from the result set.
     * @return mixed a php object with the first result values in it.
     */
    public function first() {
        $data = $this->results();
        return $data[0];
    }

    /**
     * This method will return the attribute which holds the total
     * number of records from the last executed query.
     * @return int the amount of results
     */
    public function count() {
        return $this->_count;
    }

    /**
     * This method will give back the value of the $_error property
     * which contains false if the last query executed was success,
     * true otherwise.
     * @return bool false if success, true otherwise
     */
    public function error() {
        return $this->_error;
    }
}