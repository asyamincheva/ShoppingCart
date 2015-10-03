<div class="alert alert-success" role="alert" id="#" style="display: none"></div>

<?php
if (!$this->_viewBag['body']->getProducts()) : ?>
    <h1 class="alert alert-danger text-center">No Products</h1>
<?php endif;
foreach ($this->_viewBag['body']->getProducts() as $product) : ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a href="<?= $this->getPath() . 'product/' . $product->getId() . '/show'; ?>"><?= $product->getName() ?></a>
            </h3>
        </div>
        <div class="panel-body">
            <div>Description: <?= $product->getDescription(); ?></div>
            <?php if ($product->getPromotion() !== 0) : ?>
                <div>Price: <del><?= $product->getPrice() ?>$</del>: <?= $product->getPrice() * (1 - $product->getPromotion() / 100) ?>$.
                    <span class="label label-warning">Promotion</span>
                </div>
            <?php else: ?>
                <div>Price: <?= $product->getPrice() ?>$</div>
            <?php endif; ?>
            <div>Quantity: <?= $product->getQuantity(); ?> remaining</div>
            <div>
                <a href="<?= $this->getPath() . 'categories/' . $product->getCategory() . '/0/3'; ?>">Category: <?= $product->getCategory() ?></a>
            </div>
            <?php if (\Framework\App::getInstance()->isLogged()) : ?>
                <div id="btn"
                     class="panel panel-primary btn btn-default"
                     onclick="sendAjax('<?= $product->getName(); ?>', '<?= $this->getPath() . 'cart/add/' . $product->getId(); ?>')">Add to cart
                </div>
                <div id="btn"
                     class="panel panel-primary btn btn-default"
                     onclick="enableReviewForm(<?= $product->getId() ?>)">Write review
                </div>
                <a href="<?= $this->getPath() . 'product/' . $product->getId() . '/show'; ?>" id="btn" class="panel panel-primary btn btn-default">Show reviews</a>
            <?php else: ?>
                <a href="<?= $this->getPath(); ?>home/login" class="panel panel-primary btn btn-default">Login to add to cart!</a>
                <a href="<?= $this->getPath(); ?>home/login" class="panel panel-primary btn btn-default">Login to write review!</a>
                <a href="<?= $this->getPath(); ?>product/<?= $product->getId() ?>/show" id="btn" class="panel panel-primary btn btn-default">Show reviews</a>
            <?php endif?>
            <?php
            if (\Framework\App::getInstance()->isAdmin() || \Framework\App::getInstance()->isEditor()) :?>
                <a href="<?= $this->getPath(); ?>product/<?= $product->getId() ?>/edit" class="panel panel-primary btn btn-default">Edit</a>
                <?php
                \Framework\FormViewHelper::init()
                    ->initForm($this->getPath() . 'product/' . $product->getId() . '/delete', ['style' => 'display: inline;'], 'delete')
                    ->initSubmit()->setAttribute('value', 'Delete')->setAttribute('class', 'panel panel-primary btn btn-default')->create()
                    ->render(true);
                ?>
            <?php endif;?>
            <?php if (\Framework\App::getInstance()->isLogged()) :
                \Framework\FormViewHelper::init()->initForm($this->getPath() . 'review/add/' . $product->getId(),
                    ['class' => 'form-group', 'style' => 'display: none', 'id' => $product->getId()])
                    ->initLabel()->setAttribute('for', 'message')->setValue('Message')->create()
                    ->initTextArea()->setAttribute('name', 'message')->setAttribute('class', 'form-control input-md')->setAttribute('id', 'message')->create()
                    ->initSubmit()->setAttribute('value', 'Send')->setAttribute('class', 'btn btn-primary btn-sm col-sm-1 col-sm-offset-5')->create()
                    ->render(true);
            endif; ?>
        </div>
    </div>
<?php endforeach; ?>
<ul class="pager">
    <li>
        <?php
            $start = $this->_viewBag['body']->getStart();
            $start - 3 >= 0 ? $start -= 3 : 0;

            $end = $this->_viewBag['body']->getEnd();
            $end = $end - 3 > 0 ? $end -= 3 : 3;
        ?>
        <a href="<?= "{$this->getPath()}categories/{$this->_viewBag['body']->getCategory()}/{$start}/{$end}"; ?>">Previous</a>
    <?php if ($this->_viewBag['body']->getProducts()) : ?>
        <li>
            <?php
                $start = $this->_viewBag['body']->getStart() + 3;
                $end = $this->_viewBag['body']->getEnd() + 3;
            ?>
        <a href="<?= "{$this->getPath()}categories/{$this->_viewBag['body']->getCategory()}/{$start}/{$end}"; ?>">Next</a>
    <?php endif; ?>
</ul>