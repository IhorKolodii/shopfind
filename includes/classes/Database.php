<?php
 /**
  * Database connection class.
  * 
  * @author  Igor Kolodiy <iigkey@gmail.com>
  *
  * Settings stored in includes/settings.php
  */
class Database
{
    protected $dbh;

    /**
     * Class constructor
     * 
     * @return void
     */
    public function __construct()
    {
        
    }
    
    /**
     * Init method. Gets settings from param or reads from config.
     * Creates DB connection. PDO used.
     * 
     * @param array $param DB settings array
     *
     * @return void
     */
    public function init($param = null)
    {
        if (empty($param)) {
            $driver = Config::valS('db-driver');
            $host = Config::valS('db-host');
            $dbname = Config::valS('db-dbname');
            $user = Config::valS('db-user');
            $pass = Config::valS('db-pass');
        } else {
            $driver = $param['db-driver'];
            $host = $param['db-host'];
            $dbname = $param['db-dbname'];
            $user = $param['db-user'];
            $pass = $param['db-pass'];
        }
        
        $this->dbh = new PDO($driver . ':host=' . $host . ';dbname=' . $dbname, $user, $pass, array(PDO::ATTR_PERSISTENT => true));
    }
    
    /**
     * DB data fetch method. Can use simple query or query with prepared data.
     * 
     * @param string $query DB query
     * @param array $data Data to fill prepared query
     *
     * @return array Data fetched from DB
     */
    public function getAll($query, $data = null) 
    {
        if (strpos(trim(mb_strtolower($query)), 'select') !== 0 && strpos(trim(mb_strtolower($query)), 'show') !== 0) {
            throw new Exception('Database::getAll method call with no select query!');
            return false;
        }
        if (!is_null($data)) {
            $sth = $this->dbh->prepare($query);
            $sth->execute($data);
            $result = $sth->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
        return $this->dbh->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Direct query method.
     * 
     * @param string $query DB query
     *
     * @return object Data fetched from DB
     */
    public function query($query) 
    {
        return $this->dbh->query($query);
    }
}