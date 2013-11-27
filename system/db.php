<?php
/**
 * @Author: Kenyon Haliwell
 * @URL: http://khdev.net/
 * @Date Created: 3/4/11
 * @Date Modified: 11/27/13
 * @Purpose: Handle database connections
 * @Version: 2
 *
 * Database handling using PDO
 */

/**
 * @Purpose: Database class, used to execute queries using the PDO library.
 *
 * USAGE:
 *  initialize the router
 *      $system_di->db = new db($system_di);
 *
 * 	$results = Db::query('SELECT * FROM users WHERE username=:user AND email=:email', array('user' => $user, 'email' => $email));
 *
 * Any PDO query format is acceptable (http://php.net/pdo.prepare)
 */
class db
{
    /**
    * @Var: Object
    * @Access: Public
    */
    public $system_di;

    /**
     * @Var Object
     * @Access: Private
     */
    private $_storeDB;

    /**
     * @Purpose: Load dependencyInjector into scope; Only allow 1 instance
     * @Param: object $system_di
     * @Access: Public
     * @Final
     */
    final public function __construct($system_di)
    {
        $this->system_di = $system_di;
        $this->initialize();
    }//End __construct

    /**
     * @Purpose: Disallow cloning of db
     * @Access: Private
     * @Final
     */
    final private function __clone() {}

    /**
    * @Purpose: Used to initialize the db class using PDO
    * @Access: Public
    * @Return: Return the initialized PDO class as variable $_storeDB
    */
    public function initialize()
    {
        if (NULL === $this->_storeDB) {
            try {
                $this->_storeDB = new PDO('mysql:host=' . $this->system_di->config->mysql_host . ';port=' . $this->system_di->config->mysql_port . ';dbname=' . $this->system_di->config->mysql_database, $this->system_di->config->mysql_username, $this->system_di->config->mysql_password);
                $this->_storeDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            } catch(PDOException $e) {
                $this->system_di->error->trigger_error($e->getMessage(), 'Database');
            }
        }

        return $this->_storeDB;
    }//End initialize

    /**
    * @Purpose: Used to build and execute the query
    * @Param: string $query
    * @Param: array $bindings
    * @Access: Public
    * @Return: Returns the executed query
    */
    public function query($query, $bindings = NULL)
    {
        try {
            if (is_array($bindings)) {
                $prepared_statement = $this->_storeDB->prepare($query);

                foreach ($bindings as $binding => $value) {
                    if (is_array($value)) {
                        switch (count($value)) {
                        case 1:
                            $prepared_statement->bindValue($binding, $value['value']);
                            break;
                        case 2:
                            $prepared_statement->bindValue($binding, $value['value'], $value['dataType']);
                            break;
                        case 3:
                            $prepared_statement->bindValue($binding, $value['value'], $value['dataType'], (int)$value['length']);
                            break;
                        default:
                            $this->system_di->error->trigger_error('There was an error with the query bindings', 'Database');
                            break;
                        }
                    } else {
                        $prepared_statement->bindValue($binding, $value);
                    }
                }

                $prepared_statement->execute();
                if (strtolower(substr($query, 0, 6)) === 'select') {
                    return $prepared_statement->fetchAll();
                }
            } elseif(strtolower(substr($query, 0, 6)) === 'select') {
                $fetch_rows = $this->_storeDB->query($query);
                return $fetch_rows->fetchAll();
            } else {
                return $this->_storeDB->exec($query);
            }
        } catch (PDOException $e) {
            $this->system_di->error->trigger_error($e->getMessage(), 'Database');
        }
    }//End query

    /**
    * @Purpose: Used to de-initialize the db class
    * @Access: Public
    */
    public function close()
    {
        $this->_storeDB = NULL;
    }//End close

}//End db
//End file
