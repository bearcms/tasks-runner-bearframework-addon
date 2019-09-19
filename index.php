<?php

/*
 * Tasks runner addon for Bear CMS
 * https://github.com/bearcms/tasks-runner-bearframework-addon
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

use BearFramework\App;

$app = App::get();

$app->routes
    ->add('POST /-bearcms-tasks-runner-run', function (App\Request $request) use ($app) {
        $appSecretKey = $request->formData->getValue('appSecretKey');
        if ($appSecretKey !== \BearCMS\Internal\Config::getHashedAppSecretKey()) {
            return;
        }
        $app->tasks->run();
        $data  = [
            'status' => 'ok',
            'minStartTime' => $app->tasks->getMinStartTime()
        ];
        return new App\Response\JSON(json_encode($data));
    });

$hasNewTasks = false;

$app->tasks
    ->addEventListener('addTask', function () use (&$hasNewTasks) {
        $hasNewTasks = true;
    });

$app
    ->addEventListener('sendResponse', function () use ($app, &$hasNewTasks) {
        if (!$hasNewTasks) {
            return;
        }
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://tasks-runner.bearcms.com/add");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'appSecretKey' => \BearCMS\Internal\Config::getHashedAppSecretKey()
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
            //$app->logs->log('bearcms-tasks-runner-add', $response);
        } catch (\Exception $e) {
            $app->logs->log('bearcms-tasks-runner-error', $e->getMessage());
        }
    });
