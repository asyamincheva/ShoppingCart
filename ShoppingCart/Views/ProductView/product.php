<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">
            <a href="<?= $this->getPath(); ?>product/<?= $this->_viewBag['body']->getId() ?>/show"><?= $this->_viewBag['body']->getName() ?></a>
        </h3>
    </div>
    <div class="panel-body">
        <div>Description: <?= $this->_viewBag['body']->getDescription() ?></div>
        <?php if ($this->_viewBag['body']->getPromotion() !== 0) : ?>
            <div>Price: <del><?= $this->_viewBag['body']->getPrice() ?>$</del>: <?= $this->_viewBag['body']->getPrice() * (1 - $this->_viewBag['body']->getPromotion() / 100) ?>$
                <span class="label label-warning">Promotion</span>
            </div>
        <?php else: ?>
            <div>Price: <?= $this->_viewBag['body']->getPrice() ?>$</div>
        <?php endif; ?>
        <div>Quantity: <?= $this->_viewBag['body']->getQuantity() ?> remaining</div>
        <div>
            <a href="<?= $this->getPath(); ?>categories/<?= $this->_viewBag['body']->getCategory() ?>/0/3">Category: <?= $this->_viewBag['body']->getCategory() ?></a>
        </div>
        <?php if (\Framework\App::getInstance()->isLogged()) : ?>
            <div id="btn"
                 class="panel panel-primary btn btn-default"
                 onclick="sendCart('<?= $this->getPath() . 'cart/add/' . $this->_viewBag['body']->getId(); ?>', '<?= $this->getPath(); ?>')">Add to cart
            </div>
        <?php else: ?>
            <a href="<?= $this->getPath(); ?>home/login" class="panel panel-primary btn btn-default">Login to add to cart!</a>
        <?php endif ?>
        <?php if (\Framework\App::getInstance()->isLogged()) {
            if (count($this->_viewBag['body']->getGivenReviews()) === 0) : ?>
                <div id="btn"
                     class="panel panel-primary btn btn-default"
                     onclick="enableReviewForm(<?= $this->_viewBag['body']->getId() ?>)">
                    There are no reviews! Write the first review
                </div>
            <?php else : ?>
                <div id="btn"
                     class="panel panel-primary btn btn-default"
                     onclick="enableReviewForm(<?= $this->_viewBag['body']->getId() ?>)">
                    Write review
                </div>
            <?php endif;

            if (\Framework\App::getInstance()->isAdmin() || \Framework\App::getInstance()->isEditor()) : ?>
                <a href="<?= $this->getPath(); ?>product/<?= $this->_viewBag['body']->getId() ?>/edit"
                   class="panel panel-primary btn btn-default">Edit</a>
                <?php
                \Framework\FormViewHelper::init()
                    ->initForm($this->getPath() . 'product/' . $this->_viewBag['body']->getId() . '/delete', ['style' => 'display: inline;'], 'delete')
                    ->initSubmit()->setAttribute('value', 'Delete')->setAttribute('class', 'panel panel-primary btn btn-default')->create()
                    ->render(true);
            endif;

            if (\Framework\App::getInstance()->isLogged()) {
                \Framework\FormViewHelper::init()->initForm($this->getPath() . 'review/add/' . $this->_viewBag['body']->getId(),
                    ['class' => 'form-group', 'style' => 'display: none', 'id' => $this->_viewBag['body']->getId()])
                    ->initLabel()->setAttribute('for', 'message')->setValue('Message')->create()
                    ->initTextArea()->setAttribute('name', 'message')->setAttribute('class', 'form-control input-md')->setAttribute('id', 'message')->create()
                    ->initSubmit()->setAttribute('value', 'Send')->setAttribute('class', 'btn btn-primary btn-sm col-sm-1 col-sm-offset-5')->create()
                    ->render(true);
            }
        }

        foreach ($this->_viewBag['body']->getGivenReviews() as $review) : ?>
            <div class="panel  panel-primary">
                <div class="panel panel-body"><?= $review->getMessage() ?></div>
                <div class="panel panel-footer">
                    <div class="col-sm-10">
                        Written by <a href="<?= $this->getPath() ?>user/<?= $review->getUsername() ?>/profile"><?= ucfirst($review->getUsername()) ?></a>
                        <?php if ($review->getIsAdmin()) : ?>
                            <span class="label label-danger">Admin</span>
                        <?php endif; ?>
                        <?php if ($review->getIsEditor()) : ?>
                            <span class="label label-info">Editor</span>
                        <?php endif; ?>
                        <?php if ($review->getIsModerator()) : ?>
                            <span class="label label-success">Moderator</span>
                        <?php endif; ?>
                    </div>
                    <?php if (\Framework\App::getInstance()->isAdmin() || \Framework\App::getInstance()->isModerator()) : ?>
                        <div class="col-sm-2 text-right">
                            <button class="btn btn-sm btn-default" onclick="enableReviewForm('<?= $review->getId() . 'r'?>')">Edit</button>
                            <?php
                                \Framework\FormViewHelper::init()
                                    ->initForm($this->getPath() . 'review/' . $review->getId() . '/delete', ['style' => 'display: inline;'], 'delete')
                                    ->initSubmit()->setAttribute('value', 'Delete')->setAttribute('class', 'btn btn-sm btn-default')->create()
                                    ->render(true);
                            ?>
                        </div>
                        <?php
                            \Framework\FormViewHelper::init()->initForm($this->getPath() . 'review/' . $review->getId() . '/edit',
                                ['class' => 'form-group', 'style' => 'display: none', 'id' => $review->getId() . 'r'], 'put')
                                ->initLabel()->setAttribute('for', 'message')->setValue('Edit Message')->create()
                                ->initTextArea($review->getMessage())->setAttribute('name', 'message')
                                    ->setAttribute('class', 'form-control input-md')
                                    ->setAttribute('id', 'message')
                                    ->create()
                                ->initSubmit()
                                    ->setAttribute('value', 'Edit')
                                    ->setAttribute('class', 'btn btn-primary btn-sm col-sm-1 col-sm-offset-5')
                                    ->create()
                                ->render(true);
                        ?>
                     <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>