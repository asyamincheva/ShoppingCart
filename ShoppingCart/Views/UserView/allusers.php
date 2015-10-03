<div class="row">
    <?php if (!$this->_viewBag['body']->getUsers()) : ?>
        <h1 class="alert alert-danger text-center">No Users!</h1>
    <?php endif;?>
    <ul class="list-group col-sm-6 col-sm-offset-3">
        <?php foreach ($this->_viewBag['body']->getUsers() as $user) : ?>
            <li class="list-group-item"><a href="<?= $this->getPath(); ?>user/<?= $user->getUserName() ?>/profile"><?= ucfirst($user->getUserName()) ?></a>
                <?php if ($user->getIsAdmin()) : ?>
                    <span class="label label-danger">Admin</span>
                <?php endif; ?>
                <?php if ($user->getIsEditor()) : ?>
                    <span class="label label-info">Editor</span>
                <?php endif; ?>
                <?php if ($user->getIsModerator()) : ?>
                    <span class="label label-success">Moderator</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<ul class="pager">
    <li>
        <?php
        $start = $this->_viewBag['body']->getStart();
        $start - 10 >= 0 ? $start -= 10 : 0;

        $end = $this->_viewBag['body']->getEnd();
        $end = $end - 10 > 0 ? $end -= 10 : 10;
        ?>
        <a href="<?= "{$this->getPath()}users/all/{$start}/{$end}"; ?>">Previous</a>
    </li>
    <?php if ($this->_viewBag['body']->getUsers()) : ?>
        <li>
            <?php
            $start = $this->_viewBag['body']->getStart() + 10;
            $end = $this->_viewBag['body']->getEnd() + 10;
            ?>
            <a href="<?= "{$this->getPath()}users/all/{$start}/{$end}"; ?>">Next</a>
        </li>
    <?php endif; ?>
</ul>