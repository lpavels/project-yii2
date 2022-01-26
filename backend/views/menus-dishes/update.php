<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MenusDishes */

$this->title = 'Редактирование блюда меню: ';
$this->params['breadcrumbs'][] = ['label' => 'Menus Dishes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="menus-dishes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
