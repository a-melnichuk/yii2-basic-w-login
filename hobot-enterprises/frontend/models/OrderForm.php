<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace frontend\models;
use Yii;
use yii\base\Model;

class OrderForm extends Model{
    public $quantity;
    public $max;
    public $size;
    public $sizes;
    public $name;
    public $price;


    public function rules()
    {
        return [
            [['quantity','size'],'required'],
            [['quantity'],'integer','min'=>1,'max'=> $this->max,
            'tooSmall'=>"Необхідно обрати як мінімум одного слона",
            'tooBig'=>"Слонів не може бути більше, ніж $this->max"],
            ['quantity', 'default', 'value' => 1],
        ];
    }
    //set data to model from product given
    public function getLoadedModel($product,$cart,$i){
        $this->name = $product['name'];
        $this->max = $product['quantity'];
        $this->price = $product['price'];
        $this->quantity = $cart->getQuantityColumn()[$i];
        $this->size = $cart->getSizeColumn()[$i];
        $this->sizes = $product->getRelatedSizesArray();         
        return $this;
    }
   
}
