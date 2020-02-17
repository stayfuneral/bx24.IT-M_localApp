<?php require __DIR__ . '/../configs/crest.php';

$postRawData = file_get_contents('php://input');
$postDecodedData = json_decode($postRawData, true);

if(!empty($postDecodedData) && $postDecodedData['action'] === 'deleteProduct') {
    
    $taskId = intval($postDecodedData['taskId']);
    $productId = intval($postDecodedData['productId']);

    $task = CRest::call('tasks.task.get', [
        'taskId' => $taskId,
        'select' => ['ID', 'TITLE', 'UF_CRM_TASK']
    ])['result']['task'];

    if(!empty($task['ufCrmTask'])) {
        foreach($task['ufCrmTask'] as $ufCrm) {
            if(preg_match('/D_/', $ufCrm)) {
                $dealId = preg_replace('/[^0-9]/', '', $ufCrm);
            }
        }
    }

    $products = CRest::call('crm.deal.productrows.get', [
        'id' => $dealId
    ])['result'];

    $newProductList = [];

    foreach($products as $prod) {
        if(intval($prod['ID']) !== $productId) {
            $newProductList[] = $prod;
        }
    }

    $updateDeal = CRest::call('crm.deal.productrows.set', [
        'id' => $dealId,
        'rows' => $newProductList
    ]);
    
    if($updateDeal['result'] === true) {
        $result = ['updateDeal' => 'success'];
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}