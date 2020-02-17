<?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/header.php';

$result = CRest::installApp();
if($result['rest_only'] === false):?>
<head>
	<script src="//api.bitrix24.com/api/v1/"></script>
	<?if($result['install'] == true):?>
	<script>
		BX24.init(function(){
			BX24.installFinish();
		});
	</script>
	<?endif;?>
</head>
<body>
	<?if($result['install'] == true):?>
		installation has been finished
	<?else:?>
		installation error
	<?endif;?>
</body>
<?php endif;
require $_SERVER['DOCUMENT_ROOT'].'/app/view/footer.php';