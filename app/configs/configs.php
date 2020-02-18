<?php 

// Скопируйте данные переменные и функции в файл settings.php из SDK-класса CRest или подключите его через функцию require_once

$taskStatuses = [
    1 => 'новая',
    2 => 'подтверждена мастером',
    3 => 'отправлено в офис',
    4 => 'проверена менеджером',
    5 => 'все бонусы выплачены',
    6 => 'отказ'
];

// Выбор полей задачи

$ufStatus = 'UF_AUTO_179981168811';
$ufClientAddress = 'UF_AUTO_838124798893';
$ufClientName = 'UF_AUTO_934441417404';
$ufClientPhone = 'UF_AUTO_847674715171';
$ufCrmTask = 'UF_CRM_TASK';
$ufTaskWebdavFiles = 'UF_TASK_WEBDAV_FILES';
$ufMasterBonus = 'UF_AUTO_128400396605';

$arSelect = ['ID', 'TITLE', 'STATUS', 'DEADLINE', 'CREATED_DATE', 'CLOSED_DATE', 'START_DATE_PLAN', 'CREATED_BY', 'RESPONSIBLE_ID', $ufStatus, $ufClientAddress, $ufClientName, $ufClientPhone, $ufCrmTask, $ufTaskWebdavFiles, $ufMasterBonus];

function getPrintableDate($date, $time = false) {
    $dateFormat = 'd.m.Y';
    if($time === true) {
        $dateFormat .= ' H:i:s';
    }
    return date($dateFormat, strtotime($date));
}

function getDealId(array $params) {
    if(!is_array($params)) {
        return false;
    }

    foreach($params as $param) {
        if(preg_match('/D_/', $param)) {
            $dealId = preg_replace('/[^0-9]/', '', $param);
        }
    }

    return $dealId;
}

function getTask($taskId, $selectFields) {
    $restMethod = 'tasks.task.get';
    $params = [
        'taskId' => $taskId
    ];

    if(!empty($selectFields)) {
        $params['select'] = $selectFields;
    }

    $call = CRest::call($restMethod, $params);

    if(!empty($call['result']['task'])) {
        return $call['result']['task'];
    }
}