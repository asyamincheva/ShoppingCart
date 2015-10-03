<html>
<head>
    <meta charset="UTF-8">
    <title>Api</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="<?= $this->getPath(); ?>Public/js/utility.js"></script>
</head>
<body>
<div class="container">
    <?php
    $routes = $this->_viewBag->getRoutes();

    $id = 0;
    foreach ($routes as $route => $bindingModel) : ?>
        <div>
            <div class="text-primary" style="font-size: 1.8em"><?= $route ?></div>

            <?php if ($bindingModel) : ?>
                <button onclick="enableReviewForm(<?= $id; ?>)" class="btn btn-primary btn-xs">Show binding model</button>
                <div id="<?= $id++ ?>" style="display: none">
                    <?php foreach ($bindingModel as $param) :
                        \Framework\FormViewHelper::init()
                            ->initDiv()
                            ->setValue("*$param")
                            ->create()
                            ->render();
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <hr/>
    <?php endforeach; ?>
</div>
</body>
</html>