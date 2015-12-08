<?php
namespace frontend\components\CartComponent;

use yii\base\Widget;
use yii\helpers\Html;
use Yii;
use yii\bootstrap\ActiveForm;
class CartWidget extends Widget {
    public $view;
    public $CartComponent;
    public $counter_id;
    public $counted_col;
    public $div;
    public $actionUrl;
    public $popup;
    public function init()
    {
        $this->CartComponent = Yii::$app->CartComponent;
        parent::init();  
        //no required fields are given,throw an exception
        if($this->view === null){
            throw new Exception('Please,enter widgets view');
        }
        if($this->actionUrl === null){
            throw new Exception('Please,enter widgets action url');
        }
        
        //set default html names if none are set
        if($this->counted_col===null){
            $this->counted_col = false;
            if($this->counter_id === null){
                $this->counter_id="cw_counter";
            }
        }
        if($this->div === null){
            $this->div = "cw_widget";
        }
        if($this->popup === null){
            $this->popup = "cw_popup";
        }
    }
    //generate html,css and js of widget
    public function run()
    {    
        return $this->getHtml().  $this->getCss().  $this->getJs();
    }
    // get number of items in cart
    private function getCount(){
        $num_products =  0;
        $category = $this->CartComponent->getQuantityColumn();
         if(count($category)==0){
             return 0;
         }   
        foreach ($category as $product_count){
            $num_products+=$product_count;
        }
        return $num_products;
    }
    // get default cart image
    private function getCartImg(){
        return Html::img(Yii::$app->urlManagerBackEnd->baseUrl .'/uploads/icons/icon_shopcart.png', ['alt' => 'Кошик','id'=>'cart-icon']);
    }
    
    public static function getCart(){
         $form_category = ActiveForm::begin(['id' => 'product-categories', 
                                    'method'=> 'get', 
                                    'action' => ['//site/index'],
                                    ]);
    echo Html::submitButton('Default', ['class' => 'btn btn-primary','name'=>'category','value'=>'default']);
    }


    private function getHtml() {return  <<<HTML
        <div id="{$this->div}">
            <p id = "{$this->counter_id}">{$this->getCount()}</p>
                {$this->getCartImg()}
        </div>
HTML;
    }
    private function getJs(){ return <<<JS
                <script  type="text/javascript">  
                var cartPopup = 0;
                $(document).ready(function () {
                    cartPopup = new CartPopup('#'+'{$this->div}',
                                              '#'+'{$this->popup}',
                                              '{$this->counted_col}',    
                                              '#'+'{$this->counter_id}',
                                              '{$this->actionUrl}');    
                    cartPopup.init();                          
                });
                </script>
JS;
    }
    
    
    private function getCss(){
        return<<<CSS
        <style type="text/css">
        #{$this->popup}{
            display: none;
        }

        #{$this->counter_id} {
            margin:18px 0px 0px -5px;
            position:absolute;
            color:black;
            background-color:aliceblue;
            border-radius:25%;
            padding:1px;
        }
        
        </style>
CSS;
    }
    
    
}
?>
