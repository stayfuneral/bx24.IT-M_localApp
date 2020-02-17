<?php require __DIR__ . '/../configs/crest.php';

$taskList = [];

if (!empty($_REQUEST['userId'])) {

    $userId = intval($_REQUEST['userId']);
    
    $taskListParams = [
        'order' => ['DEADLINE' => 'asc'],
        'filter' => [
            'RESPONSIBLE_ID' => $userId,
            '!STATUS' => 5, // Незавершённые задачи
            'UF_AUTO_179981168811' => [1, 2] // Статусы "Новая" и "Подтверждено мастером"
        ],
        'select' => $arSelect
    ];

    $rawTaskList = CRest::call('tasks.task.list', $taskListParams)['result']['tasks'];

// Формируем массив для ответа клиенту

    foreach ($rawTaskList as $task) {
        $taskList[$task['id']] = [
            'id' => intval($task['id']),
            'title' => $task['title'],
            'departureDate' => getPrintableDate($task['createdDate'], true),
            'operator' => $task['creator']['name'],
            'responsible' => $task['responsible']['name']
        ];
        (!empty($task['deadline'])) ? $taskList[$task['id']]['deadline'] = getPrintableDate($task['deadline']) : null;
        (!empty($task['closedDate'])) ? $taskList[$task['id']]['closedDate'] = getPrintableDate($task['closedDate']) : null;
        (!empty($task['ufAuto179981168811'])) ? $taskList[$task['id']]['status'] = $taskStatuses[$task['ufAuto179981168811']] : null;
        (!empty($task['ufAuto838124798893'])) ? $taskList[$task['id']]['clientAddress'] = $task['ufAuto838124798893'] : null;
        (!empty($task['ufAuto934441417404'])) ? $taskList[$task['id']]['clientName'] = $task['ufAuto934441417404'] : null;
        (!empty($task['ufAuto847674715171'])) ? $taskList[$task['id']]['clientPhone'] = $task['ufAuto847674715171'] : null;
    }
    echo json_encode($taskList, JSON_UNESCAPED_UNICODE);
}