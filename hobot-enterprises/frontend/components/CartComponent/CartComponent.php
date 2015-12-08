<?php
namespace frontend\components\CartComponent;
 
use Yii;
use yii\base\Component;


class CartComponent extends Component{
    public $session;
    public $column_names = ['Назва','Кількість','Розмір'];

    const COL_NAMES = 0;
    const COL_QUANTITIES = 1;
    const COL_SIZES = 2;

    // Returns existing session or creates new one otherwise
    public function getSession(){
        $this->session = Yii::$app->session;
        $session = $this->session;
        if (!$session->isActive){
            $session->open();
        }
        return $session;
    }
    public function init() {
        $this->getSession();
        parent::init();
    }
    
    /* @param $name key of item to set
     * @param $val value to set
     * @param $col_index index of cart table to add value to
     * adds value to index of column
     */
    public function setSessionVar($name,$val,$col_index){
        $str = $this->getTableNameByIndex($col_index) .".".$name;
        $this->getSession()->set($str,$val);
    }

   /* @param $name key of item to set
    * @param $col_index index of cart table to add value to
    * @return value of key in column given by index
    */
    public function getSessionVar($name,$col_index){
        $str = $this->getTableNameByIndex($col_index) .".".$name;
        return $this->getSession()->get($str);
    }
    
   /* @param $name value to add
    * @param $col_index cart column to add to
    * adds zero-indexed value to column
    */   
    public function addSessionVar($name,$col_index){
        $cat_name = $this->getTableNameByIndex($col_index);
        $_SESSION[$cat_name][] = $name;
    }
    
    public function addColumn($column = []){
        //find index of product that has same size and name as existing one
       $item_index = $this->findColumn($column);
       //if no such index exists, add new row to cart
       if($item_index == -1){
           $len = count($this->column_names);
           for($i=0;$i<$len;++$i){
               $this->addSessionVar($column[$i],$i);
           }
           return;
       }
       //otherwise, product with same name and size exsists,so just increase the quantity of a given product
       $cat = $this->getQuantityColumn();
       $cat[$item_index] += $column[1];
       $this->getSession()->set($this->getTableNameByIndex(1), $cat);
    }
   /* @param $column to search for index that matches index in columns given
    * @return integer index of item in column that has same size and name as product in cart  or -1 if no such index has been found
    */    
    public function findColumn($column = []){
        $len = count($this->getNamesColumn());
        if($len==0) {
            return -1;      
        } // find column index where name of cart column equals name of given column
          // and size of cart column equals size of given column
        for($i=0;$i<$len;++$i){
            if($this->getColumnByIndex(self::COL_NAMES)[$i]==$column[self::COL_NAMES]
            && $this->getColumnByIndex(self::COL_SIZES)[$i]==$column[self::COL_SIZES]){
                return $i;
            }
        }
        return -1;
    }
    /* @param $column_index index of cart column to select 
     * @param $product_index index of item in column to set
     * @param $val value to set item to
     * set cart column item to given value
     */   
    public function setColumnByIndex($column_index,$product_index,$val) {
        $_SESSION[$this->getTableNameByIndex($column_index)][$product_index] = $val;
    }
    
    /* @param $column_name name of cart column to remove
     * @param $remove_index index of item in column to set
     * remove product from column at given index
     */      
    public function removeByName($remove_index,$column_name){
        $column = $this->getColumnByName($column_name);
        unset($column[$remove_index]);
        $column =  array_values($column);
        $this->getSession()->set($column_name,$column);
    }
    /* @param $column_index index of cart column to remove
     * remove product from column at given index
     */       
    public function removeByIndex($remove_index,$column_index){
        $column = $this->getColumnByIndex($column_index);
        unset($column[$remove_index]);
        $column =  array_values($column);
        $this->getSession()->set($this->getTableNameByIndex($column_index),$column);
        
    } 
   /* @param $i index of cart column    
    * @return column name of cart table 
    */
    public function getTableNameByIndex($i){
        return $this->column_names[$i];
    }
   /* @param $name name of selected cart column 
    * @return array of cart table
    */
    public function getColumnByName($name){
        return $_SESSION[$name];
    }
    
   /* @param $i index of cart column
    * @return array of cart table
    */      
    public function getColumnByIndex($i){ 
        $name = $this->getTableNameByIndex($i);
        return $this->getColumnByName($name);
    }
    
    
    public function getNamesColumn(){
        return $this->getColumnByIndex(self::COL_NAMES);
    }
    
    public function getQuantityColumn(){
        return $this->getColumnByIndex(self::COL_QUANTITIES);
    }
    
    public function getSizeColumn(){
        return $this->getColumnByIndex(self::COL_SIZES);
    }
 
    // clears columns of cart
    public function clearCart(){
        foreach($this->column_names as $name){
            $_SESSION[$name] = [];
        }
    }

}