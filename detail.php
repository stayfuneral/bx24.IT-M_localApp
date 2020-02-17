<?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/header.php'; ?>



<div class="row">

    <!-- Task detail info area -->
    <div class="col-md-6">
        <div class="card mt-2 mb-2">
            <h3 class="card-header text-uppercase">{{detailTask.title}}</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><span class="text-info font-weight-bold">Статус:</span> {{detailTask.status}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Оператор:</span> {{detailTask.operator}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Время выезда:</span> {{detailTask.departureDate}}</li>                
                <li class="list-group-item" v-if="detailTask.deadline"><span class="text-info font-weight-bold">Время начала работы:</span> {{detailTask.deadline}}</li>
                <li class="list-group-item" v-if="detailTask.closedDate"><span class="text-info font-weight-bold">Время окончания работы:</span> {{detailTask.closedDate}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">ФИО клиента:</span> {{detailTask.clientName}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Адрес клиента:</span> {{detailTask.clientAdress}}</li>
                <li class="list-group-item"><span class="text-info font-weight-bold">Телефон клиента:</span> {{detailTask.clientPhone}}</li>
            </ul>
            <div class="card-footer">
                <div class="row mt-2 mb-2">
                    <div v-if="detailTask.status === 'новая'" class="row mx-auto">
                        <button @click="changeTaskStatus(detailTask.id, 2)" class="ui-btn ui-btn-primary mx-auto mt-1 mb-1">Подтвердить</button>
                    </div>
                    <div class="row mx-auto">
                        <button @click="changeTaskStatus(detailTask.id, 3)" class="ui-btn ui-btn-success mx-auto mt-1 mb-1">Отправить в офис</button>
                    </div>
                </div>                
            </div>
        </div>
        <button @click="redirect('/app.php')" class="ui-btn ui-btn-light">К списку задач</button>
    </div>

    <div class="col-md-6">

        <!-- Deal products area -->
        <div v-if="detailTask.workList" class="row">
            <div class="col-12">
                <div class="card mt-2 mb-2">
                    <h3 class="card-header text-uppercase">Список работ</h3>
                    <ul  v-for="work in detailTask.workList" class="list-group list-group-flush">
                        <li :id="getProductId(work.id)" class="list-group-item">
                            <div class="row">
                                <div class="col-sm-8">{{work.name}}</div>
                                <div class="col-sm-3">{{work.price}} ₽</div>
                                <div @click ="deleteWork(work.id)" class="col-sm-1 text-danger clickable"><i class="far fa-times-circle"></i></div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Photo area -->
        <div class="row">
            <div class="col-12">
                <div class="card mt-2 mb-2">
                    <h3 class="card-header text-uppercase">Фотографии</h3>
                    
                    <div v-if="detailTask.photos" class="row mt-1 mb-1 p-1">
                        <div v-for="photo in detailTask.photos" class="col-sm-4">
                            <img :src="photo" alt="" class="img-fluid rounded p-2">
                        </div>
                    </div>
                    <div class="card-footer">

                            <input class="mb-2" type="file" name="photos[]" id="photos" multiple/>
                            <button @click="uploadTaskPhotos(detailTask.id)" type="submit" class="ui-btn ui-btn-light">Загрузить файлы</button>        
                        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php require $_SERVER['DOCUMENT_ROOT'].'/app/view/footer.php';