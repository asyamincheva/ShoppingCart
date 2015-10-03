<div class="panel">
    <?php foreach ($this->_viewBag['body']->getPromotions() as $promotion) :?>
        <div class="panel panel-primary">
            <div class="panel panel-heading">Promotion name: <?= $promotion->getName();?> </div>
            <div class="panel panel-body">
                <?php \Framework\FormViewHelper::init()
                    ->initForm($this->getPath() . 'editor/promotion/remove', ['class' => 'form-group col-sm-4'], 'delete')
                    ->initTextBox()->setName('name')->setAttribute('id', 'name')->setAttribute('style', 'display: none')
                    ->setAttribute('value', $promotion->getName())->create()
                    ->initSubmit()->setAttribute('value', 'Remove promotion')->setAttribute('class', 'btn btn-default')->create()
                    ->render(true); ?>
                <div>Product name: <?= $promotion->getProduct() ?></div>
                <div>Percentage: <?= $promotion->getPercentage() ?>%</div>
                <div>End date: <?= $promotion->getDate() ?></div>
            </div>
        </div>
    <?php endforeach ?>
</div>