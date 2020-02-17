<?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/header.php';

?>

<div class="row mt-2 mb-2 pt-2 pb-2 rounded head">
    <!-- <div class="col-sm-1"><img :src="userData.photo" alt="" class="img-fluid rounded"></div> -->
    <div class="col-12">
        <h3>{{ userData.name }}</h3>
        <p>{{ userData.workPosition }}</p>
    </div>
</div>

<div id="taskList" class="row">
    <div class="col-md-3 d-flex align-self-stretch justify-content-start" v-for="task in userTasks">
        <div :id="getTaskId(task.id)" class="card bg-light mt-2 mb-2 ml-auto mr-auto">
            <h5 class="card-header clickable" @click="showTask(task.id)">{{task.title}}</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><span class="text-info font-weight-bold">Оператор:</span> {{task.operator}}</li>
                <li v-if="task.deadline" class="list-group-item"><span class="text-info font-weight-bold">Дата начала работ:</span> {{task.deadline}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Статус:</span> {{task.status}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Адрес:</span> {{task.clientAddress}}</li>
            </ul>            
        </div>
    </div>
    
</div>




<?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/footer.php';