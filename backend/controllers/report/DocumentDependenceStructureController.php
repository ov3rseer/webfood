<?php

namespace backend\controllers\report;

/**
 * Контроллер отчета "Связаные документы"
 */
class DocumentDependenceStructureController extends ReportController
{
    /**
     * @var string имя класса модели
     */
    public $modelClass = 'backend\models\report\DocumentDependenceStructure';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'backend\actions\report\base\IndexAction',
                'modelClass' => $this->modelClass,
                'viewPath' => '@backend/views/report/document-dependence-structure/index',
            ],
        ]);
    }
}
