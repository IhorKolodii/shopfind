<?php
 /**
  * Data manager class. Performing DB search by keywords and other data manipulation.
  * 
  * @author  Igor Kolodiy <iigkey@gmail.com>
  *
  */
class CatalogManager
{
    protected $db;
    protected $maxSearchIterations = 3;

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
     * Main method to find data in DB by keywords
     * 
     * @param array $data Search query data array, contain fields: 'find', 'brand', 'type', 'size'
     *
     * @return array Search result data
     */
    public function find($data)
    {
        $searchString = trim($data['find']);
        $searchString = substr($searchString, 0, 100);
        $keywords = explode(' ', $searchString);
        $keywords = array_slice($keywords, 0, 5);
        $data['keywords'] = $keywords;
        $result = $this->findItemsByKeywords($data);
        return $result;
    }

    /**
     * Gets brands list from DB
     *
     * @return array Brands list
     */
    public function getBrands()
    {
        $query = 'SELECT `id`, `name` FROM ' . Config::valS('db-brandtable');
        return array_merge([0 => ['id' => 0, 'name' => 'ALL']] ,$this->db->getAll($query));
    }

    /**
     * Method with find by keywords logic, uses keywords sets to make relevant search
     * 
     * @param array $data Search query data array, contain fields: 'find', 'brand', 'type', 'size', 'keywords'
     *
     * @return array Search result data
     */
    public function findItemsByKeywords($data)
    {
        $iterations = $this->maxSearchIterations;
        if (count($data['keywords']) < $this->maxSearchIterations) {
            $iterations = count($data['keywords']);
        }
        
        for ($i = 0; $i < $iterations; $i++) {
            $query = $this->buildQuery($data, $i);
            if ($query) {
                $result = $this->db->getAll($query['query'], $query['data']);
            } else {
                return false;
            }
            if (!empty($result)) {
                return $result;
            }
        }
        return [];
    }
    
    /**
     * Query builder for search
     * 
     * @param array $data Search query data array, contain fields: 'find', 'brand', 'type', 'size'
     * @param int $iteration Search iteration
     *
     * @return array With query and query data to use in prepared PDO query
     */
    protected function buildQuery($data, $iteration = 0)
    {
        $keywordSets = $this->buildKeywordSets($data, $iteration);
        if (!$keywordSets) {
            return false;
        }
        $queryData = [];
        $query = 'SELECT `id`, `name`, `brand`, `type`, ' . ($data['type'] == 1 ? '`dress_size`' : '`shoes_size`') . ' AS `size` FROM `' . Config::valS('db-itemtable') . '` WHERE ';
        
        $likeArray = [];
        $i = 0;
        foreach ($keywordSets as $keywordSet) {
            $likeArraySet = [];
            foreach ($keywordSet as $keyword) {
                $queryData[':like'.$i] = '%'.$keyword.'%';
                $likeArraySet[] = "`name` LIKE :like$i";
                $i++;
            }
            $likeArray[] = implode(' AND ', $likeArraySet);
        }
        $like = '';
        if (count($likeArray) > 1) {
            $like .= '((' . implode(') OR (', $likeArray) . '))';
        } else {
            $like = $likeArray[0];
        }
        $query .= $like;
        $queryData[':type'] = $data['type'];
        $query .= " AND `type` = :type";
        if ($data['brand'] != 0) {
            $queryData[':brand'] = $data['brand'];
            $query .= " AND `brand` = :brand";
        }
        if ($data['size'] != 'ALL') {
            $queryData[':size'] = $data['size'];
            $query .= " AND " . ($data['type'] == 1 ? '`dress_size`' : '`shoes_size`') . " = :size";
        }
        $query .= ';';
        return ['query' => $query, 'data' => $queryData];
    }
    
    /**
     * Keywords sets builder. Builds sets of keyword to use in search.
     * 
     * @param array $data Search query data array, contain fields: 'find', 'brand', 'type', 'size'
     * @param int $iteration Search iteration
     *
     * @return array Keywods sets
     */
    protected function buildKeywordSets($data, $iteration)
    {
        $sets = [];
        $keywords = $data['keywords'];
        if ($iteration === 0) {
            $sets[] = $keywords;
        } elseif ($iteration === 1) {
            for ($i = 0; $i < count($keywords); $i++) {
                $tempKeywords = $keywords;
                unset($tempKeywords[$i]);
                $sets[] = array_values($tempKeywords);
            }
        } elseif ($iteration === 2) {
            for ($i = 0; $i < count($keywords); $i++) {
                $tempKeywords = $keywords;
                unset($tempKeywords[$i]);
                $tempKeywords = array_values($tempKeywords);
                for ($j = $i; $j < count($keywords) - 1; $j++) {
                    $innerTemp = $tempKeywords;
                    unset($innerTemp[$j]);
                    $innerTemp = array_values($innerTemp);
                    $sets[] = $innerTemp;
                }
                
            }
        } 
        return $sets;
    }
}