<div class="col-md-4 col-md-offset-4">
    <table class="cart-dropdown">
        <?php if($len==0): ?>
        <th>Кошик пустий...</th>
        <?php else: ?>
        <tr>
            <?php foreach($table_heads as $table_head): ?>
                <th><?= $table_head ?></th>  
            <?php endforeach; ?>
            <th></th>
        </tr>
        <?php for($i=0;$i<$len;++$i):?>
            <tr> 
                <?php foreach($table_heads as $table_head):?>
                    <td class="<?= $table_head ?>"><?= $cart->getColumnByName($table_head)[$i] ?></td>
                <?php endforeach; ?>
                <td><?=\yii\helpers\Html::a('&#10799;', ['site/remove','id'=>$i],[
                    'id'=>$i,
                    'class'=>'btn-delete'])?>
                </td>
            </tr> 
         <?php endfor;endif; ?> 
     </table>
</div>