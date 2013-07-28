<!DOCTYPE HTML>
<!--[if LT IE 7 ]> <html class="ie ie6 no-js" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="en"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="en"> <![endif]-->
<!--[if GT IE 9]><!--><html class="no-js" lang="en"><!--<![endif]-->
<!-- the "no-js" class is for Modernizr. -->
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="content-language" content="en" />
  <meta name="viewport" content="width=device-width" />
  <link rel="shortcut icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/images/favicon.ico" /> 
  <!-- blueprint CSS framework -->
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/screen.css" media="screen, projection" />
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/print.css" media="print" />
  <!--[if lt IE 8]>
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/ie.css" media="screen, projection" />
  <![endif]-->
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/form.css" />
  <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/metadatagames.css" />

  <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <!--[if gte IE 9]>
    <style type="text/css">
        .gradient {
            filter: none;
        }
    </style>
    <![endif]-->
</head>

<body class="no-js">
<?php echo $content; ?>
</body>
</html>