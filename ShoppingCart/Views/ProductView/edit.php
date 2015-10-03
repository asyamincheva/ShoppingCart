<div class="panel panel-primary">
    <div class="panel-body">
        <?php \Framework\FormViewHelper::init()
            ->initForm($this->getPath() . 'product/change/'. $this->_viewBag['body']->getId(), ['class' => 'form-group'], 'put')
            ->initLabel()->setValue("Product name")->setAttribute('for', 'name')->create()
            ->initTextBox()->setName('name')->setAttribute('id', 'name')->setAttribute('class', 'form-control input-md')
            ->setAttribute('value', $this->_viewBag['body']->getName())->create()
            ->initLabel()->setValue("Description")->setAttribute('for', 'description')->create()
            ->initTextBox()->setName('description')->setAttribute('id', 'description')->setAttribute('class', 'form-control input-md')
            ->setAttribute('value', $this->_viewBag['body']->getDescription())->create()
            ->initLabel()->setValue("Price")->setAttribute('for', 'price')->create()
            ->initTextBox()->setName('price')->setAttribute('id', 'price')->setAttribute('class', 'form-control input-md')
            ->setAttribute('value', $this->_viewBag['body']->getPrice())->create()
            ->initLabel()->setValue("Quantity")->setAttribute('for', 'quantity')->create()
            ->initTextBox()->setName('quantity')->setAttribute('id', 'quantity')->setAttribute('class', 'form-control input-md')
            ->setAttribute('value', $this->_viewBag['body']->getQuantity())->create()
            ->initLabel()->setValue("Category")->setAttribute('for', 'category')->create()
            ->initTextBox()->setName('category')->setAttribute('id', 'category')->setAttribute('class', 'form-control input-md')
            ->setAttribute('value', $this->_viewBag['body']->getCategory())->create()
            ->initSubmit()->setAttribute('value', 'Change')->setAttribute('class', 'btn btn-primary btn-lg col-sm-4 col-sm-offset-4')->create()
            ->render(); ?>
    </div>
</div>