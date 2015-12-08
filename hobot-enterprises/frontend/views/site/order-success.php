<?php
use yii\helpers\Html;
$this->title = 'Товари замовлено!';
$thumbnails_url = Yii::$app->urlManagerBackEnd->createUrl('uploads/thumbnails');
$this->registerCssFile('@web/css/order-success.css');
?>
<div class = 'order-success'>
    <h2>Ви замовили:</h2>
    <table>
        <th>Товар</th><th>Рисунок</th><th>Розмір</th>
    <?php foreach($items as $item):?>
        <tr>
            <td><?= $item->name ?> (<?= $item->quantity ?> шт.)</td>
            <td><?= Html::img($thumbnails_url .'/'. $item->img , ['alt' => $item->name])?></td>
            <td><?= $item->size ?></td>
        </tr>
    <?php endforeach; ?>
    </table>

<h1 id="order-success-message" style="text-align:center">Дякую за купівлю!</h1>
  <?= Html::img(\Yii::$app->urlManagerBackEnd->baseUrl .'/uploads/icons/happy_elephant.jpg',['id'=>'empty-cart-img','style'=>  
                                                                                                                   'display:block;
                                                                                                                    margin-left:auto;
                                                                                                                    margin-right:auto;']); ?>
</div>