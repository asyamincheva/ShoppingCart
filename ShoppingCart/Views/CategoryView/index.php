<?php
foreach ($this->_viewBag['body']->getCategories() as $category) : ?>
    <a class="panel panel-primary col-sm-3 col-sm-offset-1 btn btn-default text-center"
       href="<?= $this->getPath() . 'categories/' . $category->getName(); ?>/0/3"><?= $category->getName(); ?></a>
<?php endforeach; ?>