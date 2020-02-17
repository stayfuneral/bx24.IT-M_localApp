<?php require __DIR__ . '/../configs/crest.php';
$postRawData = file_get_contents('php://input');
$postDecodedData = json_decode($postRawData, true);

if(!empty($postDecodedData['fields'])) {
    $taskId = $postDecodedData['taskId'];
    $status = intval($postDecodedData['fields']['UF_AUTO_179981168811']);

    $updateBatchParams = [];

    $task = CRest::call('tasks.task.get', [
        'taskId' => $taskId,
        'select' => $arSelect
    ])['result']['task'];

    if(!empty($task['ufCrmTask'])) {
        foreach($task['ufCrmTask'] as $ufCrm) {
            if(preg_match('/D_/', $ufCrm)) {
                $dealId = preg_replace('/[^0-9]/', '', $ufCrm);
            }
        }
    }
    // $dealStatus = '';
    switch($status) {
        case 2:
            $dealStatus = 'PREPARATION';
            break;
        case 3:
            $dealStatus = 'PREPAYMENT_INVOICE';
            break;
    }

    $updateBatchParams = [
        'updateTask' => [
            'method' => 'tasks.task.update',
            'params' => $postDecodedData
        ],
        'updateDeal' => [
            'method' => 'crm.deal.update',
            'params' => [
                'id' => $dealId,
                'fields' => [
                    'STAGE_ID' => $dealStatus
                ]
            ]
        ]
    ];

    $updateTask = CRest::callBatch($updateBatchParams)['result'];
    if(!empty($updateTask['result']) && is_array($updateTask['result'])) {
        writeToLog($_SERVER['DOCUMENT_ROOT'].'/log/update.txt', $updateTask, 'Debug');
        echo json_encode(['updateTask' => 'success', 'taskNewStatus' => $updateTask['result']['updateTask']['task']['ufAuto179981168811']], JSON_UNESCAPED_UNICODE);
    }    
}

if(!empty($_FILES)) {

    $penalty = -150;

    $uploadFolder = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
    $taskId = 0;
    $uploadPhotos = [];
    foreach($_FILES['photos']['name'] as $id => $photos) {
        $taskId += $id;
    }
    $tempFiles = [];

    if(is_array($_FILES['photos']['tmp_name'][$taskId])) {
        // $fileCount = count($_FILES['photos']['tmp_name'][$taskId]);

        // if($fileCount < 5) {
        //     $masterBonus = $penalty;
        // } else if($fileCount >= 5 && $fileCount <= 10) {
        //     $masterBonus = $fileCount * 2;
        // } else if($fileCount > 10) {
        //     $masterBonus = 20;
        // }

        foreach($_FILES['photos']['tmp_name'][$taskId] as $key => $value) {

            $Files = new SplFileInfo($value);
            if($Files->isFile()) {
                $tempFiles[] = $value;
                $tempFile = base64_encode(file_get_contents($value)); // base64-кодированное изображение
                $fileName = $_FILES['photos']['name'][$taskId][$key];
                $uploadedFile = $uploadFolder.$fileName;
                $uploadPhotos['add_file_to_task_'.$taskId.'_'.($key+1)] = [
                    'method' => 'task.item.addfile',
                    'params' => [
                        'TASK_ID' => $taskId,
                        'FILE[NAME]' => $fileName,
                        'FILE[CONTENT]' => $tempFile
                    ]
                ];
            }
        }

        $uploadPhotos['get_updated_task'] = [
            'method' => 'tasks.task.get',
            'params' => [
                'taskId' => $taskId,
                'select' => $arSelect
            ]
        ];

        $addPhotosToTask = CRest::callBatch($uploadPhotos)['result'];
        if(empty($addPhotosToTask['result_error'])) {

            $fileCount = count($addPhotosToTask['result']['get_updated_task']['task']['ufTaskWebdavFiles']);
            if($fileCount < 5) {
                $masterBonus = $penalty;
            } else if($fileCount >= 5 && $fileCount <= 10) {
                $masterBonus = $fileCount * 2;
            } else if($fileCount > 10) {
                $masterBonus = 20;
            }

            $setMasterBonus = CRest::call('tasks.task.update', [
                'taskId' => $taskId,
                'fields' => [
                    'UF_AUTO_128400396605' => $masterBonus
                ]
            ])['result']['task'];

            $result = [
                'uploadFiles' => 'success',
                // 'taskPhotos' => $fileCount,
                // 'setMasterBonus' => $setMasterBonus

            ];
            foreach($tempFiles as $tFile) {
                unlink($tFile);
            }
        }
    }




    echo json_encode($result, JSON_UNESCAPED_UNICODE);

}