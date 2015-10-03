<html>
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</head>
<body>
<h1>Index View</h1>

<div><?= $this->_viewBag->getData() ?></div>
<?php
?>
<hr/>
<?php
?>

<?php
\Framework\AjaxViewHelper::init()->initForm($this->getPath() . "help/jsonroutes", "put")->initCallback("function( msg ) {
            console.log( \"Data Saved: \" + msg );
        }")->render(true);
?>
</body>
</html>