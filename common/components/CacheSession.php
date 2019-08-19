<?php

namespace common\components;

class CacheSession extends \yii\web\CacheSession
{
    /**
     * @inheritdoc
     */
    public function destroySession($id)
    {
        if (!$this->cache->exists($id)) {
            return true;
        }
        return parent::destroySession($id);
    }
}
