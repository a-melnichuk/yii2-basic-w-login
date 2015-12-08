<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$this->registerCssFile('@web/css/order.css');
$this->title = 'Кошик';
$this->params['breadcrumbs'][] = $this->title;
$total = 0;
?>

  <?php Pjax::begin([
      'id' => 'order-pjax',
      'enablePushState' => false]);?>
<?php if(count($items) == 0): ?>
    <h2>Кошик пустий...</h2>
<?php else: ?>
<div id="order-container">
<?php $form = ActiveForm::begin([
     'id' => 'order-form',
     'options' => ['data-pjax' => true],
      ]); ?>
    

  
    <table id = "order-table">
        <tr><th>Назва</th><th>Ціна</th><th>Кількість</th><th>Розмір</th><th>Всього</th></tr><th></th>
        <?php for($i=0;$i<count($items);++$i): ?>
            <?php
                $price = $items[$i]->price;
                $quantity = $items[$i]->quantity;
            ?>
            <tr id ="row-<?=$i?>">
                <td><?= $items[$i]->name ?></td>
                <td class = 'price'><?= $price ?></td>
                <td class='quantity'><?= $form->field($items[$i],"[$i]quantity")->label(false) ?></td>
                <td><?= $form->field($items[$i], "[$i]size")->label(false)
                    ->dropDownList($items[$i]->sizes) ?></td>
                <td class = 'row-total'></td>
                <td class='remove-btn-class'>
                    <?= Html::submitButton('&#10799;',['id'=>$i,'class'=>"btn-delete",'name'=>'remove','value'=>$i]); ?>
                </td>
            </tr>
        <?php endfor; ?> 
    </table>
    <?= Html::submitButton('Замовити',['class'=>"btn btn-primary active",'value'=>'order','name'=>'done']); ?>
   
    <?php ActiveForm::end(); ?>
    <h3>Всього:</h3>
    <h3 id='order-sum'></h3>
    <?php endif;?>
</div>
 <script type="text/javascript">
$(document).ready(function(){
    cartPopup.getCartData();
    function setTotalValues(){
        var total = 0;
        $('.row-total').each(function( i, value ) {
            var price = parseFloat($('.price')[i].innerHTML);
            var quantity = parseInt($('.quantity input')[i].value);
            var val = Math.round(price*quantity*100) / 100;
            total+=val;
            $(this).text(val);
        });
        $('#order-sum').text(total);
    }
   setTotalValues();
   function setSumOrdered(){
       var total = 0;
       $('.row-total').each(function( i, value ){
           total+=parseFloat($(this).text());
       });
       total = Math.round(total*100)/100;
       $('#order-sum').text(total);
   }
   
   $("input[type='text']").keyup( function() {
        var quantity = parseInt(this.value);
        if(isNaN(quantity) || quantity <= 0){ return;}
        var $parent =  $(this).closest('tr');
        var price = parseFloat($parent.find('.price').html());
        var val = Math.round(price*quantity*100) / 100;
        $parent.find('.row-total').html(val);
        
        setSumOrdered();  
    }); 
});

</script>
<?php Pjax::end(); ?>  
<script  type="text/javascript">  
$(document).ready(function(){ 
     $( document ).ajaxSuccess(function(event, xhr, settings ) {        
          $('#cart-popup a').click(function(e){
              $.pjax.reload({container: '#order-pjax'});
          });
         return false;
     });
});
</script>