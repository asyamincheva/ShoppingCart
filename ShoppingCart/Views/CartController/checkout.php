<?php if (count($this->_viewBag['body']->getOutOfStock()) !== 0) : ?>
    <h1><?= $this->_viewBag['body']->getMessage() ?></h1>
    <?php foreach ($this->_viewBag['body']->getOutOfStock() as $product) : ?>
        <div class="alert alert-danger col-sm-3">
            <h3 class="panel-primary"><a href="<?= $this->getPath(); ?>product/<?= $product->getId() ?>/show"><?= $product->getName() ?></a>
            </h3>
        </div>
    <?php endforeach;
else:?>
    <h1 class="alert alert-success"><?= $this->_viewBag['body']->getMessage() ?></h1>
<?php endif; ?>