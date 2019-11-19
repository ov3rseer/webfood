<?php

use common\models\enum\MenuCycle;
use common\models\enum\WeekDay;

return [
    'menu-1' => [
        'name' => 'Меню понедельника',
        'menu_id' => 1,
        'menu_cycle_id' => MenuCycle::WEEKLY,
        'week_day_id' => WeekDay::MONDAY,
    ],
    'menu-2' => [
        'name' => 'Меню вторника',
        'menu_id' => 2,
        'menu_cycle_id' => MenuCycle::WEEKLY,
        'week_day_id' => WeekDay::TUESDAY,
    ],
    'menu-3' => [
        'name' => 'Меню среды',
        'menu_id' => 3,
        'menu_cycle_id' => MenuCycle::WEEKLY,
        'week_day_id' => WeekDay::WEDNESDAY,
    ],
    'menu-4' => [
        'name' => 'Меню четверга',
        'menu_id' => 4,
        'menu_cycle_id' => MenuCycle::WEEKLY,
        'week_day_id' => WeekDay::THURSDAY,
    ],
    'menu-5' => [
        'name' => 'Меню пятницы',
        'menu_id' => 5,
        'menu_cycle_id' => MenuCycle::WEEKLY,
        'week_day_id' => WeekDay::FRIDAY,
    ],
];