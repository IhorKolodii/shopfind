<?php
 /**
  * Main application class
  * @author  Igor Kolodiy <iigkey@gmail.com>
  *
  */
class App
{
    
    protected $defaultAction = 'indexAction';
    protected $db = null;
    protected $config = null;
    protected $title = 'Application';

    /**
     * Class constructor.
     * 
     * @param object $config Config object. If not provided will be loaded automatically.
     *
     * @return void
     */
    public function __construct(Config $config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = Config::getConfig();
            $this->config->init();
        }
        $this->init();
    }
    
    /**
     * Init method
     * 
     * Prepares DB. Called by constructor
     *
     * @return void
     */
    protected function init()
    {
        $this->db = new Database();
        $this->db->init();
        $this->config->set('db_object', $this->db); // TODO check if unused
    }
    
    /**
     * Main method to process request.
     *
     * @return void
     */
    public function run()
    {
        $action = $this->getAction();
        $this->$action();
    }
    
    /**
     * Action name recognizer.
     * 
     * Tries to get action from GETm if not - returns default action name
     *
     * @return string Action name
     */
    protected function getAction()
    {
        $actionName = filter_input(INPUT_GET, 'a');
        if ($actionName) {
            $action = $actionName.'Action';
            if (method_exists($this, $action)) {
                return $action;
            } else {
                throw new Exception('Action ' . $actionName . ' not found in application ' . get_class($this));
            }
        }
        return $this->defaultAction;
    }

    /**
     * Index action method
     *
     * @return void
     */
    protected function indexAction()
    {
        $this->setTitle('CodeIT test - Index');
        $findQuery = filter_input(INPUT_POST, 'find');
        $brandQuery = filter_input(INPUT_POST, 'brand');
        $typeQuery = filter_input(INPUT_POST, 'type');
        $sizeQuery = filter_input(INPUT_POST, 'size');
        if (!empty($findQuery) && !empty($typeQuery) && !empty($sizeQuery)) {
            $data = ['find' => $findQuery, 'brand' => $brandQuery, 'type' => $typeQuery, 'size' => $sizeQuery];
            $this->findAction($data);
            exit;
        }
        $dbTestDataFiller = new FillDb($this->db);
        $dbStatus = $dbTestDataFiller->checkDb();
        $baseAvailable = false;
        if ($dbStatus['item_table_count'] > 0 && $dbStatus['brand_table_count'] > 0) {
            $baseAvailable = true;
            $manager = new CatalogManager($this->db);
            $brands = $manager->getBrands();
            $dressSizes = $this->config->val('dress-sizes');
            $dressSizes = array_merge(['ALL'], $dressSizes);
            $shoesSizes = $this->config->val('shoes-sizes');
        }
        $error = false;
        $this->render('index', ['db_available' => $baseAvailable, 'brands' => $brands, 'dress-sizes' => $dressSizes, 'shoes-sizes' => $shoesSizes, 'error' => $error]);
    }
    
    /**
     * Find action method, performs search by keywords
     * 
     * @param array $data Data for search array, contains fields: 'find', 'brand', 'type', 'size'
     *
     * @return void
     */
    protected function findAction($data)
    {
        $baseAvailable = true;
        $manager = new CatalogManager($this->db);
        $findResults = $manager->find($data);
        $error = false;
        if ($findResults === false) {
            $error = 'Search error';
        } elseif (empty($findResults)) {
            $error = 'No items matching search request. Try to use another keywords or use simpler search request';
        }
        $brands = $manager->getBrands();
        $dressSizes = $this->config->val('dress-sizes');
        $shoesSizes = $this->config->val('shoes-sizes');
        $this->render('index', [
            'db_available' => $baseAvailable, 
            'brands' => $brands, 
            'dress-sizes' => $dressSizes, 
            'shoes-sizes' => $shoesSizes, 
            'find_results' => $findResults, 
            'error' => $error, 
            'find' => $data['find'],
                ]);
    }
    
    /**
     * Init DB page action method
     *
     * @return void
     */
    protected function initDbAction()
    {
        $dbTestDataFiller = new FillDb($this->db);
        $dbStatus = $dbTestDataFiller->checkDb();
        $this->setTitle('CodeIT test - test DB creation');
        $this->render('init_db', ['db_status' => $dbStatus]);
    }
    
    /**
     * Table creation action method
     *
     * @return void
     */
    protected function createTablesAction()
    {
        $dbTestDataFiller = new FillDb($this->db);
        $dbTestDataFiller->createTables();
        $this->redirect('initDb');
    }
    
    /**
     * Table drop action method
     *
     * @return void
     */
    protected function dropTablesAction()
    {
        $dbTestDataFiller = new FillDb($this->db);
        $dbTestDataFiller->dropTables();
        $this->redirect('initDb');
    }
    
    /**
     * Table test data filling action method
     *
     * @return void
     */
    protected function addTestDataAction()
    {
        $dbTestDataFiller = new FillDb($this->db);
        $dbTestDataFiller->fillTables();
        $this->redirect('initDb');
    }
    
    /**
     * Render method. Renders data in given template.
     * Closes application on finish.
     * 
     * @param string $template Template name
     * @param array $values Values which can be used by template while render
     *
     * @return void
     */
    protected function render($template, $values = null)
    {
        ob_start();
        require Config::valS('template-path') . $template . '.tpl.php';
        $actionOutput = ob_get_contents(); 
        ob_clean(); 
        
        ob_start();
        require Config::valS('template-path') . Config::valS('layout-template-name') . '.tpl.php';
        $finalOutput = ob_get_contents(); 
        ob_clean(); 
        
        echo $finalOutput;
        
        exit();
    }
    
    /**
     * Sets the page title.
     * 
     * @param string $title Page title string
     *
     * @return void
     */
    protected function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * Redirect method. Closes application.
     * Only inner redirect supported.
     * 
     * @param string $action Action to redirect name
     *
     * @return void
     */
    protected function redirect($action)
    {
        header("Location:?a=initDb");
        exit();
    }
    
}