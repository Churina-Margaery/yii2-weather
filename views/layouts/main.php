<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
?>
<?php $this->beginPage() ?>

<!DOCTYPE html>

<html lang="<?= Yii::$app->language ?>" class="h-100">
<html>
  <head>
      <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
      <link rel="shortcut icon" href="images/NSU_small.svg" />
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.css">
      <script src="https://cdn.jsdelivr.net/chartist.js/latest/chartist.min.js"></script>
  </head>
  <body>
      <?php $this->beginBody() ?>
      
      <header>
          <div class="infobox">
            <a href="https://www.nsu.ru/n/" target="_blank" class="logo">
            <img src="images/NSU.svg" alt="NSU">
            </a>
          </div>
          
          <div class="menu-wrapper">
          <div class="menu">
            <div class="menu-items">
                <a href="https://table.nsu.ru/">Расписание</a>
                <a href="https://www.nsu.ru/n/media/news/">Новости</a>
                <a href="https://www.nsu.ru/n/media/events/">События</a>
                <a href="https://www.nsu.ru/n/university/how-to-reach/">Как добраться</a>
                <a href="https://www.nsu.ru/n/contacts/">Контакты</a>
            </div>
          </div>
          </div>
      </header>
      
      <main id="main" class="flex-shrink-0" role="main">
        <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </main>
      
    <?php $this->endBody() ?>
  </body>
</html>

<?php $this->endPage() ?>