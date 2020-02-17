<?php require __DIR__ . '/../configs/crest.php';
$postRawData = file_get_contents('php://input');
$postDecodedData = json_decode($postRawData, true);

if(!empty($postDecodedData['fields'])) {

    $updateTask = CRest::call('tasks.task.update', $postDecodedData);
    // writeToLog(__DIR__.'/../../updateTask_'.date('d.m.y_H.i.s').'.txt', ['updateTask' => 'success', 'task' => $updateTask], 'new task update action');
    if(!empty($updateTask['result']['task']) && is_array($updateTask['result']['task'])) {
        echo json_encode(['updateTask' => 'success', 'taskNewStatus' => $updateTask['result']['task']['ufAuto179981168811']], JSON_UNESCAPED_UNICODE);
    }    
}

if(!empty($_FILES)) {

    $uploadFolder = $_SERVER['DOCUMENT_ROOT'].'/uploads/';
    $taskId = 0;
    $uploadPhotos = [];
    foreach($_FILES['photos']['name'] as $id => $photos) {
        $taskId += $id;
    }
    $tempFiles = [];

    if(is_array($_FILES['photos']['tmp_name'][$taskId])) {
        $fileCount = count($_FILES['photos']['tmp_name'][$taskId]);

        foreach($_FILES['photos']['tmp_name'][$taskId] as $key => $value) {
            $Files = new SplFileInfo($value);
            if($Files->isFile()) {
                $tempFiles[] = $value;
                $tempFile = base64_encode(file_get_contents($value));
                $fileName = $_FILES['photos']['name'][$taskId][$key];
                $uploadedFile = $uploadFolder.$fileName;


                // move_uploaded_file($value, $uploadedFile);

                // $encodedFile = base64_encode(file_get_contents($uploadedFile));

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
        $addPhotosToTask = CRest::callBatch($uploadPhotos)['result'];
        if(empty($addPhotosToTask['result_error'])) {
            $result = [
                'uploadFiles' => 'success'
            ];
            foreach($tempFiles as $tFile) {
                unlink($tFile);
            }
        }
    }




    echo json_encode($result, JSON_UNESCAPED_UNICODE);

}