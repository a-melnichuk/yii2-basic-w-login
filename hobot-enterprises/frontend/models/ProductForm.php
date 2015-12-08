<?php
namespace frontend\models;
use yii\base\Model;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProductForm extends Model {
    public $max = 0;
    public $quantity = 1;
    public $size;
    
    
    public function rules()
    {
        return [
            [['size'], 'required','message'=>'Оберіть розмір слона.'],
            [['quantity'],'integer','min'=>1,'max'=> $this->max,
            'tooSmall'=>"Необхідно обрати як мінімум одного слона",
            'tooBig'=>"Слонів не може бути більше, ніж $this->max"],          
            ['quantity', 'default', 'value' => 1],
        ];
    }
}
