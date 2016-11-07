<?php
 /**
  * Configuration class. Used singletone pattern.
  * 
  * @author  Igor Kolodiy <iigkey@gmail.com>
  *
  * Use Config::getConfig() to get instance.
  */
class Config
{

    static private $instance = null;
    private $data = [];

    /**
     * Gets the config object. Static.
     *
     * @return object Config instance
     */
    static public function getConfig()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Gets value from settings array. Static.
     * 
     * @param string $index Key for the value
     *
     * @return mixed Value requested or null
     */
    static public function valS($index)
    {
        $instance = Config::getConfig();
        return $instance->val($index);
    }

    /**
     * Sets value in settings array. Static.
     * 
     * @param string $index Key for the value
     * @param mixed $value Value to store
     *
     * @return void
     */
    static public function setS($index, $value)
    {
        $instance = Config::getConfig();
        $instance->set($index, $value);
    }

    /**
     * Constructor. Private to avoid direct instantiating.
     *
     * @return void
     */
    private function __construct()
    {
        
    }

    /**
     * Clone magic method. Private to avoid direct instantiating.
     *
     * @return void
     */
    protected function __clone()
    {
        
    }
    
    /**
     * Unserialize magic method. Private to avoid direct instantiating.
     *
     * @return void
     */
    protected function __wakeup()
    {
        
    }

    /**
     * Init method. Sets data array with data from argument or reads settings file.
     *
     * @param array $data Settings array
     * 
     * @return void
     */
    public function init($data = null)
    {
        if ($data) {
            $this->data = $data;
        } else {
            $this->data = include 'includes/settings.php';
        }
    }

    /**
     * Sets value in settings array.
     * 
     * @param string $index Key for the value
     * @param mixed $value Value to store
     *
     * @return void
     */
    public function set($index, $value)
    {
        $this->data[$index] = $value;
    }

    /**
     * Gets value from settings array.
     * 
     * @param string $index Key for the value
     *
     * @return mixed Value requested or null
     */
    public function val($index)
    {
        return $this->data[$index];
    }

}
