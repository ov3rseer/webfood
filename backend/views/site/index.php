<?php

/* @var $this yii\web\View */

use common\models\reference\File;
use common\models\reference\Unit;
use common\models\reference\User;
use common\models\reference\Contractor;
use common\models\reference\Contract;
use common\models\reference\Product;
use common\models\tablepart\ContractorContract;
use yii\base\UserException;
use yii\helpers\Html;
use yii\helpers\Json;

$this->title = 'Admin WebFood';
?>
<div class="site-index">

    <div class="jumbotron">

        <p><?= \yii\helpers\Html::a('Импорт контрагентов и договоров',
                ['/system/import-contractor-and-contract/index'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>
        <p><?= \yii\helpers\Html::a('Экспорт авторизационных данных новых контрагентов',
                ['export-contractors-authorization-data'], ['class' => 'btn btn-lg btn-success', 'style' => 'width:600px;']) ?></p>

    </div>

</div>
