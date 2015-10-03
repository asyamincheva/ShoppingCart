<?php

if (!$this->_viewBag['body']->getProducts()) : ?>
    <h1 class="alert alert-danger text-center">Your cart is empty!</h1>
<?php else: ?>
    <h1 class="row text-center">
        <div class="col-sm-4">Your balance: <?= $this->_viewBag['body']->getMoney() ?> $</div>
        <div class="col-sm-4">Total price: <?= $this->_viewBag['body']->getTotalSum() ?> $</div>
        <?php
        \Framework\FormViewHelper::init()->initForm($this->getPath() . 'cart/checkout')
            ->initSubmit()->setAttribute('value', 'Checkout')->setAttribute('class', 'btn btn-default col-sm-2 col-sm-offset-1')
            ->create()->render();
        ?>
    </h1>
<?php endif; ?>
    <?php foreach ($this->_viewBag['body']->getProducts() as $product) : ?>
        <div class="panel col-sm-3">
            <h3 class="panel-primary"><a href="<?= $this->getPath() . 'product/' . $product->getId(); ?>/show"><?= $product->getName() ?></a>
            </h3>

            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6 text">Price: <?= $product->getPrice() ?> $</div>
                    <!--panel panel-primary btn btn-default col-sm-6-->
                    <?php
                    \Framework\FormViewHelper::init()
                        ->initForm($this->getPath() . 'cart/remove/' . $product->getId(), array(), 'delete')
                        ->initSubmit()->setAttribute('value', 'Remove')->setAttribute('class', 'panel panel-primary btn btn-default col-sm-6')
                        ->create()
                        ->render(true);
                    ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>