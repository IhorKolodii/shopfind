<?php
/**
  * Service class to fill DB with test data.
  * 
  * @author  Igor Kolodiy <iigkey@gmail.com>
  */
class FillDb
{
    protected $db;
    protected $itemTableNameInConfig = 'db-itemtable';
    protected $brandTableNameInConfig = 'db-brandtable';
    protected $words = [];

    /**
     * Class constructor.
     * 
     * @param object $db Database object
     *
     * @return void
     */
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    /**
     * Checks DB for necessary tables
     *
     * @return array DB check result with fields: 'item_table_exists', 'brand_table_exists', 'item_table_count', 'brand_table_count'
     */
    public function checkDb()
    {
        $result = [
            'item_table_exists' => false,
            'brand_table_exists' => false,
            'item_table_count' => 0,
            'brand_table_count' => 0,
            ];
        $query = "SHOW TABLES LIKE '".Config::valS($this->itemTableNameInConfig)."'";
        $data = $this->db->getAll($query);
        if (count($data) === 1) {
            $result['item_table_exists'] = true;
            $query = "SELECT COUNT(*) AS num FROM `".Config::valS($this->itemTableNameInConfig)."`";
            $data = $this->db->getAll($query);
            $result['item_table_count'] = $data[0]['num'];
        } 
        
        $query = "SHOW TABLES LIKE '".Config::valS($this->brandTableNameInConfig)."'";
        $data = $this->db->getAll($query);
        if (count($data) === 1) {
            $result['brand_table_exists'] = true;
            $query = "SELECT COUNT(*) AS num FROM `".Config::valS($this->brandTableNameInConfig)."`";
            $data = $this->db->getAll($query);
            $result['brand_table_count'] = $data[0]['num'];
        } 
        
        return $result;
    }
    
    /**
     * Creates tables in DB
     *
     * @return void
     */
    public function createTables()
    {
        $status = $this->checkDb();
        if (!$status['item_table_exists']) {
            $query = "CREATE TABLE " . Config::valS($this->itemTableNameInConfig) . " (
            id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            brand INT NOT NULL,
            type INT NOT NULL,
            dress_size VARCHAR(255),
            shoes_size VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
            $this->db->query($query);
        }
        
        if (!$status['brand_table_exists']) {
            $query = "CREATE TABLE " . Config::valS($this->brandTableNameInConfig) . " (
            id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
            $this->db->query($query);
        }
    }
    
    /**
     * Drops tables in DB
     *
     * @return void
     */
    public function dropTables()
    {
        $status = $this->checkDb();
        if ($status['item_table_exists']) {
            $query = "DROP TABLE `" . Config::valS($this->itemTableNameInConfig) . "`";
            $this->db->query($query);
        }
        
        if ($status['brand_table_exists']) {
            $query = "DROP TABLE `" . Config::valS($this->brandTableNameInConfig) . "`";
            $this->db->query($query);
        }
    }
    
    /**
     * Fills tables with test data
     *
     * @return void
     */
    public function fillTables()
    {
        $status = $this->checkDb();
        
        if ($status['brand_table_count'] < 1) {
            $this->addBrands();
        }
        $status = $this->checkDb();
        if ($status['brand_table_count'] > 0) {
            $this->brandCount = $status['brand_table_count'];
            $this->addEntries();
        }
    }
    
    /**
     * Adds brand list to brands DB table
     *
     * @return void
     */
    protected function addBrands()
    {
        $file = fopen(Config::valS('includes-path').Config::valS('brands-list'),"r");
        $brands = [];
        while(!feof($file))
        {
            $name = trim(fgets($file));
            if (!empty($name)) {
                $name = str_replace("'", "\'", $name);
                $brands[] = $name;
            }
        }
        fclose($file);
        if (!empty($brands)) {
            $query = "INSERT INTO `" . Config::valS($this->brandTableNameInConfig) . "` (name) VALUES ('" . implode("'),('", $brands) . "');";
            $this->db->query($query);
        }
    }
    
    /**
     * Adds records to item DB table.
     * Adds 10000 drees type records and 10000 shoes type records
     *
     * @return void
     */
    protected function addEntries()
    {
        $file = fopen(Config::valS('includes-path').Config::valS('words-list'),"r");
        $words = [];
        $mode = 'unknown';
        $modes = ['colours', 'shoes', 'dress', 'adjectives', 'dress_about', 'shoes_about'];
        while(!feof($file))
        {
            $name = trim(fgets($file));
            if (preg_match('/^\[.+\]$/', $name) && in_array(trim($name, '[]'), $modes)) {
                $mode = trim($name, '[]');
                continue;
            }
            if (!empty($name)) {
                $name = str_replace("'", "\'", $name);
                $words[$mode][] = $name;
            }
        }
        fclose($file);
        
        $this->words = $words;
        
        $this->addDress(10000);
        $this->addShoes(10000);

    }
    
    /**
     * Adds dress records to DB with generated names
     * 
     * @param int $num Number of records to add
     *
     * @return void
     */
    protected function addDress($num)
    {
        $names = [];
        for ($i = 0; $i < $num; $i++) {
            $names[] = $this->constructDressName();
        }
        $sizes = ['S', 'M', 'L'];
        $query = "INSERT INTO `" . Config::valS($this->itemTableNameInConfig) . "` (name, brand, type, dress_size) VALUES ";
        $first = true;
        foreach ($names as $name) {
            if (!$first) {
                $query .= ",";
            }
            $query .= " ('" . $name . "', '" . rand(1, $this->brandCount) . "', '1', '" . $sizes[rand(0, 2)] . "')";
            $first = false;
        }
        
        $query .= ";";
        $this->db->query($query);

    }
    
    /**
     * Adds shoes records to DB with generated names
     * 
     * @param int $num Number of records to add
     *
     * @return void
     */
    protected function addShoes($num)
    {
        $names = [];
        for ($i = 0; $i < $num; $i++) {
            $names[] = $this->constructShoesName();
        }
        $query = "INSERT INTO `" . Config::valS($this->itemTableNameInConfig) . "` (name, brand, type, shoes_size) VALUES ";
        $first = true;
        foreach ($names as $name) {
            if (!$first) {
                $query .= ",";
            }
            $query .= " ('" . $name . "', '" . rand(1, $this->brandCount) . "', '2', '" . rand(35, 45) . "')";
            $first = false;
        }
        
        $query .= ";";
        $this->db->query($query);
    }
    
    /**
     * Constructs dress name using words from dictionary
     *
     * @return string Dress pseudo-random generated name
     */
    protected function constructDressName()
    {
        $aboutCount = rand(0, 2);
        $adjectivesCount = rand(0, 3);
        $coloursCount = rand(0, 2);
        $availableNames = count($this->words['dress']);
        $availableAdjectives = count($this->words['adjectives']);
        $availableAbout = count($this->words['dress_about']);
        $availableColours = count($this->words['colours']);
        $name = $this->words['dress'][rand(0, $availableNames-1)];
       
        for ($i = 0; $i < $adjectivesCount; $i++) {
            $name .= ' ' . $this->words['adjectives'][rand(0, $availableAdjectives-1)];
        }
        
        for ($i = 0; $i < $coloursCount; $i++) {
            $name .= ' ' . $this->words['colours'][rand(0, $availableColours-1)];
        }
         
        for ($i = 0; $i < $aboutCount; $i++) {
            $name .= ' ' . $this->words['dress_about'][rand(0, $availableAbout-1)];
        }
        return $name;
    }
    
    /**
     * Constructs shoes name using words from dictionary
     *
     * @return string Shoes pseudo-random generated name
     */
    protected function constructShoesName()
    {
        $aboutCount = rand(0, 2);
        $adjectivesCount = rand(0, 3);
        $coloursCount = rand(0, 2);
        $availableNames = count($this->words['shoes']);
        $availableAdjectives = count($this->words['adjectives']);
        $availableAbout = count($this->words['shoes_about']);
        $availableColours = count($this->words['colours']);
        $name = $this->words['shoes'][rand(0, $availableNames-1)];
        for ($i = 0; $i < $adjectivesCount; $i++) {
            $name .= ' ' . $this->words['adjectives'][rand(0, $availableAdjectives-1)];
        }
        for ($i = 0; $i < $coloursCount; $i++) {
            $name .= ' ' . $this->words['colours'][rand(0, $availableColours-1)];
        }
        for ($i = 0; $i < $aboutCount; $i++) {
            $name .= ' ' . $this->words['shoes_about'][rand(0, $availableAbout-1)];
        }
        return $name;
    }
}