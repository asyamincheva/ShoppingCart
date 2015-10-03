<div class="panel panel-primary  col-sm-6 col-sm-offset-3">
    <h1 class="text-center">Sell your product here:</h1>
    <?php
    \Framework\FormViewHelper::init()
        ->initForm($this->getPath() . 'product/add', ['class' => 'form-group'], 'post')
        ->initLabel()->setValue("Product name")->setAttribute('for', 'name')->create()
        ->initTextBox()->setName('name')->setAttribute('id', 'name')->setAttribute('class', 'form-control input-md')->create()
        ->initLabel()->setValue("Description")->setAttribute('for', 'description')->create()
        ->initTextBox()->setName('description')->setAttribute('id', 'description')->setAttribute('class', 'form-control input-md')->create()
        ->initLabel()->setValue("Price")->setAttribute('for', 'price')->create()
        ->initTextBox()->setName('price')->setAttribute('id', 'price')->setAttribute('class', 'form-control input-md')->create()
        ->initLabel()->setValue("Category")->setAttribute('for', 'category')->create()
        ->initTextBox()->setName('category')->setAttribute('id', 'category')->setAttribute('class', 'form-control input-md')->create()
        ->initSubmit()->setAttribute('value', 'Sell')->setAttribute('class', 'btn btn-primary btn-lg col-sm-4 col-sm-offset-4')->create()
        ->render();
    ?>
</div>
