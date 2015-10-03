<div class="input-group">
    <h1>Welcome to the Admin Home</h1>
    <h2>Admin list:</h2>

    <div class="row">
        <ul class="list-group col-sm-6 col-sm-offset-2">
            <?php foreach ($this->_viewBag['body']->getAdmins() as $key => $admin) : ?>
                <li class="list-group-item"><a href="<?= $this->getPath(); ?>user/<?= $admin ?>/profile"><?= ucfirst($admin) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <div class="col-sm-col-sm-6">
            <a href="<?= $this->getPath(); ?>admin/index/edit" class="panel panel-primary btn btn-default">Edit rights</a>
        </div>
    </div>
</div>