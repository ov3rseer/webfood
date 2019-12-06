<?php

use common\models\reference\ProductProvider;
use common\models\reference\ServiceObject;

return [
    'provider-1#object-1' => [
        'parent_id' => $this->getFixtureModel(ProductProvider::class, 'provider-1')->primaryKey,
        'service_object_id' => $this->getFixtureModel(ServiceObject::class, 'object-1')->primaryKey,
    ],
    'provider-1#object-2' => [
        'parent_id' => $this->getFixtureModel(ProductProvider::class, 'provider-1')->primaryKey,
        'service_object_id' => $this->getFixtureModel(ServiceObject::class, 'object-2')->primaryKey,
    ],
];