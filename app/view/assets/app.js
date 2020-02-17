let app = new Vue({
    el: '#app',
    data: {
        currentSize: BX24.getScrollSize(),
        userData: {},
        userTasks: [],
        detailTask: [],
        taskStatuses: {
            1: 'новая',
            2: 'подтверждена мастером',
            3: 'отправлено в офис',
            4: 'проверена менеджером',
            5: 'все бонусы выплачены',
            6: 'отказ'
        },
        file: window.location.pathname,
        promises: {
            BX24_Promise: null,
            TaskPromise: null,
            updateTaskPromise: null
        },
        taskId: null,
    },
    methods: {
        saveFrameWidth: function() {
            let frameWidth = document.getElementById('app').offsetWidth;
            return frameWidth;
        },
        getScrollHeight: function() {
            let frameHeight = window.innerHeight;            
            return frameHeight;
        },
        resizeFrame: function(taskCount) {
            let head = document.querySelector('.head').offsetHeight;
            let cardHeight = document.querySelector('.card').offsetHeight;
            let rowsCount = Math.ceil(taskCount / 4);
            let margin = rowsCount * 30;
            let actualHeight = (rowsCount * cardHeight) + head + margin
            let frameWidth = this.saveFrameWidth();
            if(rowsCount > 1) {
                BX24.resizeWindow(frameWidth, actualHeight)
            }
        },
        showTask: function(taskId) {
            this.redirect('/detail.php?task=' + taskId);
        },
        reload: function() {
            return window.location.reload(true);
        },
        redirect: function(url) {
            return window.location.href = url;
        },
        uploadTaskPhotos: function(taskId) {
            const input = document.querySelector('input[type="file"]');
            const formData = new FormData();
            for(const file of input.files) {
                formData.append('photos[' + taskId + '][]', file);
            }

            axios.post('/app/api/taskUpdate.php', formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                if(response.data.uploadFiles === 'success') {
                    this.reload();
                }
            })
            .catch(error => console.error('ERROR:', error))
        },
        changeTaskStatus: function(taskId, status) {
            this.promises.updateTaskPromise = new Promise((resolve, reject) => {
                axios.post('/app/api/taskUpdate.php', 
                    {
                        taskId: taskId,
                        fields: {
                            UF_AUTO_179981168811: status
                        }
                    }
                )
                .then(response => {                    
                    if(response.data.updateTask === 'success') {
                        switch(response.data.taskNewStatus) {
                            case '2':
                                this.reload();
                                break;
                            case '3':
                                this.redirect('/app.php');
                                break;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error)
                })
            })
        },
        getTaskId: function(taskId) {
            return 'task_' + taskId;
        },
        getProductId: function(id) {
            return 'product_' + id;
        },
        deleteWork: function(id) {
            let productId = this.getProductId(id);
            let element = document.getElementById(productId);
            return new Promise((resolve, reject) => {
                axios.post('/app/api/dealUpdate.php', {
                    taskId: this.taskId,
                    action: 'deleteProduct',
                    productId: id
                })
                .then(response => {
                    if(response.data.updateDeal === 'success') {
                        element.remove();
                    }
                })
            })
        }
    },
    created: function() {
        let url = new URL(window.location.href);
        this.taskId = url.searchParams.get('task');
        switch(this.file) {
            case '/app.php':
                
                this.promises.BX24_Promise = new Promise(function(resolve, reject) {
                    BX24.callMethod('user.current', {}, function(result) {
                        axios.get('/app/api/taskList.php', {
                            params: {
                                userId: result.answer.result.ID
                            }
                        }).then(function(response) {
                            resolve(app.userTasks = response.data);
                            resolve(app.userData = {
                                id: result.answer.result.ID,
                                name: result.answer.result.NAME + ' ' + result.answer.result.LAST_NAME,
                                photo: result.answer.result.PERSONAL_PHOTO,
                                workPosition: result.answer.result.WORK_POSITION
                            });
                        })
                    });
                });
                this.promises.BX24_Promise.then(response => {
                    let userTaskCount = Object.keys(this.userTasks).length
                    // this.resizeFrame(userTaskCount);
                });

                break;
            case '/detail.php':
                if(this.taskId) {
                    this.promises.TaskPromise = new Promise((resolve, reject) => {
                        axios.get('/app/api/taskDetail.php', {
                            params: {
                                taskId: this.taskId
                            }
                        }).then(response => {
                            resolve(app.detailTask = response.data);
                        }).catch(error => {
                            console.log(error);
                        });
                    });
                }
                this.promises.TaskPromise.then(response => {
                })     
                break;
            case '/edit.php':
                break;
        }
    },
    updated : function () {
        BX24.resizeWindow(document.body.clientWidth, document.getElementsByClassName("workarea")[0].clientHeight);
    },
});