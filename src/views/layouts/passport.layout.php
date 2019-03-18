<?php
\effsoft\eff\asset\jquery\JqueryAssetBundle::register($this);
\effsoft\eff\asset\bootstrap\bundle\BootstrapBundleAssetBundle::register($this);
\effsoft\eff\asset\jquery\easing\JqueryEasingAssetBundle::register($this);
\effsoft\eff\asset\magnific\popup\MagnificPopupAssetBundle::register($this);
\effsoft\eff\asset\chart\js\ChartJsAssetBundle::register($this);
\effsoft\eff\theme\sbadmin2\BootstrapSBAdmin2AssetBundle::register($this);
\effsoft\eff\theme\sbadmin2\BootstrapSBAdmin2CustomAssetBundle::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language; ?>">
<head>
    <meta charset="<?= \Yii::$app->charset; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?=\yii\helpers\Html::csrfMetaTags();?>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <?php $this->head() ?>
    <title><?= \yii\helpers\Html::encode($this->title); ?></title>
</head>
<body>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

