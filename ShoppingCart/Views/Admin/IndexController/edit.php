<div class="panel">
    <h1 class="text-center">Admin Edit</h1>
    <div class="row">
        <?php
        \Framework\FormViewHelper::init()
            ->initForm($this->getPath() . 'admin/index/add', ['class' => 'form-group col-sm-4 col-sm-offset-1'], 'post')
            ->initLabel()->setValue("Username")->setAttribute('for', 'username')->create()
            ->initTextBox()->setName('username')->setAttribute('id', 'username')->setAttribute('class', 'form-control input-md')->create()
            ->initLabel()->setValue("Right name")->setAttribute('for', 'rightName')->create()
            ->initTextBox()->setName('rightName')->setAttribute('id', 'rightName')->setAttribute('class', 'form-control input-md')->create()
            ->initSubmit()->setAttribute('value', 'Give right')->setAttribute('class', 'btn btn-primary col-sm-4 col-sm-offset-4')->create()
            ->render();

        \Framework\FormViewHelper::init()
            ->initForm($this->getPath() . 'admin/index/remove', ['class' => 'form-group col-sm-4 col-sm-offset-2'], 'delete')
            ->initLabel()->setValue("Username")->setAttribute('for', 'username')->create()
            ->initTextBox()->setName('username')->setAttribute('id', 'username')->setAttribute('class', 'form-control input-md')->create()
            ->initLabel()->setValue("Right name")->setAttribute('for', 'rightName')->create()
            ->initTextBox()->setName('rightName')->setAttribute('id', 'rightName')->setAttribute('class', 'form-control input-md')->create()
            ->initSubmit()->setAttribute('value', 'Remove right')->setAttribute('class', 'btn btn-primary col-sm-4 col-sm-offset-4')->create()
            ->render(true);
        ?>
    </div>
</div>