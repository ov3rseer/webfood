<?php

namespace frontend\actions\setMenu;

use common\models\reference\SetMenu;
use frontend\actions\FrontendModelAction;
use Yii;
use yii\base\UserException;

class DeleteMenuAction extends FrontendModelAction
{
    /**
     * @return bool
     * @throws UserException
     */
    public function run()
    {
        $setMenuId = Yii::$app->request->post('setMenuId');
        if ($setMenuId) {
            $setMenu = SetMenu::findOne(['id' => $setMenuId]);
            if ($setMenu) {
                $setMenu->is_active = false;
                $setMenu->save();
            }
        }
        return true;
    }
}