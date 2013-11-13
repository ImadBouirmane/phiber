<?php

/**
	* DB function wrapper.
    * @version 	1.0
	* @author 	Hussein Guettaf <ghussein@coda-dz.com>
	* @package 	codup
	*/

class db extends Codup\main
{
    
    /*
     * Holds query text for debug purposes
     */
    public $query = array();
    
    /*
     * The actual link to the db (a mysqli object)
     */
    protected $con = null;
    /*
     * Instance of this class
     */
    protected static $link = null;

    /**
     * Constructor
     * 
     * @method __construct
     * @access protected
     * @param $conf config
     *            An instance of the configuration class (see getInstance());
     * @return void
     */
    
    function __construct ()
    {
        $config = $this->load('config');
        $this->con = new mysqli($config->_dbhost, $config->_dbuser, 
                $config->_dbpass, $config->_dbname);
        
        if (mysqli_connect_errno()) {
            throw new Exception(
                    "Error: " . mysqli_connect_errno() . " " .
                             mysqli_connect_error());
        }
    
    }



    /**
     * In case we need to create a new link to the DB
     * 
     * @method reset
     * @access public
     * @return void
     */
    
    static function reset ()
    {
        self::$link = null;
    }

    /**
     *
     * @method select
     * @access public
     * @param $data array
     *            table name, column names and any conditions or joins in one
     *            array
     * @return array boolean result of the query or false if the query fails
     */
    
    private function select ($data)
    {
        
        $this->query[] = "SELECT " . implode(",", $data[1]) . " FROM " . $data[0] .
                 " " . $data[2];
                
                if ($result = $this->con->query(
                        $this->query[count($this->query) - 1])) {
                    
                    while ($arr = $result->fetch_assoc()) {
                        
                        $res[] = $arr;
                    }
                    return $res;
                }
                
                return false;
            }

            /**
             *
             * @method insert
             * @access public
             * @param $data array
             *            table name, column names and data in one array
             * @return integer inserted id
             */
            private function insert ($data)
            {
                
                foreach ($data[1] as $col => $field) {
                    // some validation
                    if (! is_numeric($field)) {
                        $insertData[] = "'$field'";
                    } else {
                        $insertData[] = $field;
                    }
                    
                    $cols[] = "`$col`";
                
                }
                
                $this->query[] = "INSERT INTO " . $data[0] . " (" .
                         implode(",", $cols) . ") VALUES (" .
                         implode(",", $insertData) . ")";
                
                $this->con->query($this->query[count($this->query) - 1]);
                
                return $this->con->insert_id;
            }

            /**
             *
             * @method update
             * @access public
             * @param $data array
             *            table name, column names and any data or conditions in
             *            one array
             * @return boolean The returned value from $mysqli->query
             */
            private function update ($data)
            {
                foreach ($data[1] as $col => $val) {
                    if (! is_numeric($val)) {
                        $updateData[] = "$col = '$val'";
                    } else {
                        $updateData[] = "$col = $val";
                    }
                }
                
                $this->query[] = "UPDATE " . $data[0] . " SET " .
                         implode(",", $updateData) . " WHERE $data[2]";
                        
                        return $this->con->query(
                                $this->query[count($this->query) - 1]);
                    }

                    /**
                     *
                     * @method delete
                     * @access public
                     * @param $data array
                     *            table name, column names and any conditions in
                     *            one array
                     * @return boolean The returned value from $mysqli->query
                     */
                    private function delete ($data)
                    {
                        
                        $this->query[] = "DELETE FROM " . $data[0] . " WHERE " .
                                 implode(" AND ", $data[1]);
                                
                                return $this->con->query(
                                        $this->query[count($this->query) - 1]);
                            }

                            function __get ($var)
                            {
                                if (key_exists($var, get_class_vars(__CLASS__))) {
                                    return $this->{$var};
                                }
                            
                            }

                            function __call ($name, $param)
                            {
                                if (array_search($name, 
                                        get_class_methods(__CLASS__))) {
                                    // parent::stack(__class__." -->
                                    // $name(".implode(',',$param).")");
                                    call_user_func_array(array(__class__, 
                                            $name), $param);
                                    parent::stack(
                                            __class__ . ":" .
                                                     array_pop($this->query));
                                
                                }
                            
                            }

                            static function __callStatic ($name, $params)
                            {
                                if (array_search($name, 
                                        get_class_methods(__CLASS__))) {
                                    parent::stack(
                                            __class__ . " --> $name(" .
                                                     implode(',', $param) . ")");
                                    return call_user_func_array(
                                            __class__ . "::" . $name, $params);
                                }
                            
                            }
                        
                        }
                        
                        ?>
