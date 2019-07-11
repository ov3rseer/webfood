<?php

use backend\controllers\system\RoleController;
use backend\widgets\GridView\GridViewWithToolbar;
use common\models\system\Role;

/* @var yii\web\View $this */
/* @var yii\data\ActiveDataProvider $dataProvider */
/* @var Role $filterModel */

$this->title = $filterModel->getPluralName();
$this->params['breadcrumbs'][] = $this->title;

/** @var RoleController $controller */
$controller = $this->context;

?>
<div class="reference-index">

    <?= GridViewWithToolbar::widget([
        'gridToolbarOptions' => ['layout' => ['refresh', 'create']],
        'gridOptions'        => [
            'dataProvider' => $dataProvider,
            'filterModel'  => $filterModel,
            'columns'      => [
                'description',
            ],
        ],
    ]); ?>

</div>
