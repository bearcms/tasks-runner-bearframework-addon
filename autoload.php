<?php

/*
 * Tasks runner addon for Bear CMS
 * https://github.com/bearcms/tasks-runner-bearframework-addon
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

BearFramework\Addons::register('bearcms/tasks-runner-bearframework-addon', __DIR__, [
    'require' => [
        'bearcms/bearframework-addon',
        'bearframework/tasks-addon'
    ]
]);
