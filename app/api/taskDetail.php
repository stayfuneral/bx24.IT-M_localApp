<?php require __DIR__ . '/../configs/crest.php';
$domain = '';
if(!empty($_REQUEST['taskId'])) {

    $task = [];
    $taskId = intval($_REQUEST['taskId']);

    $rawBatchRequest = CRest::callBatch([
        'get_task' => [
            'method' => 'tasks.task.get',
            'params' => [
                'taskId' => $taskId,
                'select' => $arSelect
            ]
        ],
        'get_task_photos' => [
            'method' => 'task.item.getfiles',
            'params' => [
                'TASKID' => $taskId
            ]
        ],
    ])['result']['result'];
    
    if(!empty($rawBatchRequest)) {

        $getTask = $rawBatchRequest['get_task']['task'];
        $getPhotos = $rawBatchRequest['get_task_photos'];

        $task = [
            'id' => $getTask['id'],
            'title' => $getTask['title'],
            'departureDate' => getPrintableDate($getTask['createdDate'], true),
            'operator' => $getTask['creator']['name'],
            'responsible' => $getTask['responsible']['name'],
            'status' => $taskStatuses[$getTask['ufAuto179981168811']],
            'clientAdress' => $getTask['ufAuto838124798893'],
            'clientName' => $getTask['ufAuto934441417404'],
            'clientPhone' => $getTask['ufAuto847674715171'],
        ];
        (!empty($getTask['closedDate'])) ? $task['closedDate'] = getPrintableDate($getTask['closedDate']) : null;
        (!empty($getTask['deadline'])) ? $task['deadline'] = getPrintableDate($getTask['deadline']) : null;

        if(!empty($getPhotos)) {
            foreach($getPhotos as $photo) {
                $task['photos'][] = 'https://gazpromneft.bitrix24.ru'.$photo['VIEW_URL'];
            }
        }

        if(!empty($getTask['ufCrmTask'])) {
            foreach($getTask['ufCrmTask'] as $ufCrm) {
                if(preg_match('/D_/', $ufCrm)) {
                    $dealId = preg_replace('/[^0-9]/', '', $ufCrm);
                }
            }

            $products = CRest::call('crm.productrow.list', [
                'filter' => [
                    'OWNER_TYPE' => 'D',
                    'OWNER_ID' => $dealId
                ]
            ])['result'];
            
            if(!empty($products)) {
                foreach($products as $product) {
                    $task['workList'][] = [
                        'id' => $product['ID'],
                        'name' => $product['PRODUCT_NAME'],
                        'price' => intval($product['PRICE']),
                        'quantity' => $product['QUANTITY']
                    ];
                }
            }
        }
    }
    writeToLog(__DIR__.'/../../log/detailTask.txt', [date('d.m.Y H:i:s') => $task], 'Task Detail Info');
    echo json_encode($task, JSON_UNESCAPED_UNICODE);
}