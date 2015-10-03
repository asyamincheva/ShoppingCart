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
\Framework\FormViewHelper::init()
    ->initTextBox()->setAttribute('class', 'some')->setName('username')->setAttribute('value', 'pesho')->setAttribute('class1', 'some2')->create()
    ->initTextArea()->create()
    ->initUploadFile()->create()
    ->initRadioBox()->setChecked()->create()
    ->initRadioBox()->create()
    ->initUploadFile()->create()
    ->initCheckBox()->setChecked()->create()
    ->initBoostrapDropDown('drop down')->setDropDownLi('#', '1')->setDropDownLi('#', '2')->create()
    ->render();
?>
</body>
</html>