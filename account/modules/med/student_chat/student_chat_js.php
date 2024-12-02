<script>
    $(document).ready(function() {

        addAvatar = function() {
            $("#addModal").modal("show");
        }
    });

    // File upload function

    async function uploadFile() {
        const getContent = document.getElementById("saveContent");
        const getText = document.getElementById("innerText");
        const input = document.getElementById("attach-doc");
        const file = input.files[0];
        getContent.style.display = "block";

        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                const fileData = e.target.result;
            };
            getText.textContent = file.name;
            getText.title = file.name;

            console.log(getText)
            reader.readAsDataURL(file);
        } else {
            console.error('No file selected.');
        }

    }

    // Websocket-related code here
    const chatBox = document.getElementById('chat-box');
    const messageInput = document.getElementById('message-input');
    const domain = "%APP_URL%";
    const ws = new WebSocket('wss://' + 'lms.senet.uz/ws/?chat_id=1&student_id=1');

    ws.addEventListener('open', function(event) {
        //addMessage('Connected!', 'server-message');
        const loadUsers = {
            "Action": "GetStudents",
            "Data": {
                "page": 1
            }
        }

        ws.send(JSON.stringify(loadUsers));
    });

    window.onload = function(){
        ws.addEventListener('message', function(event) {

            const getUsers = JSON.parse(event.data).Data;
            if (getUsers) {
                getUsers.forEach(v => {
                    let id = v.Related_id;
                    let name = v.Name;
                    let avatar = v.Avatar;

                    getAllUsers(id, name, avatar);

                });
            }
        });
    }


    function getAllUsers(id, userName, avatar){

        const getList = document.querySelector(".userList");
        //console.log(id, userName, avatar);
        const chatList = document.createElement("ul");
        chatList.className = "chat-users-list chat-list media-list";
        chatList.onclick = function() {
            openChat(id);
        };

        const listItem = document.createElement("li");

        listItem.innerHTML = `
        <span class="avatar"><img src="${avatar}" height="42" width="42" alt="Generic placeholder image" />
            <span class="avatar-status-offline"></span>
        </span>
        <div class="chat-info flex-grow-1">
            <h5 class="mb-0">${userName}</h5>
            <p class="card-text text-truncate">
                ...
            </p>
        </div>
        <div class="chat-meta text-nowrap">
            <small class="float-right mb-25 chat-time">4:14 PM</small>
            <span class="badge badge-danger badge-pill float-right">3</span>
        </div>
    `;

        chatList.appendChild(listItem);

        getList.appendChild(chatList);
        //console.log(getList)
    }

    async function createUser(event) {
        event.preventDefault();

        const getUser = document.getElementById("relatedId");
        const selectedUser = getUser.options[getUser.selectedIndex];

        const selectPhoto = document.getElementById("getAvatar");
        const photo = selectPhoto.files[0];

        const formData = new FormData();
        formData.append("avatar", photo);

        let userId = parseInt(selectedUser.value);
        let userName = selectedUser.textContent;
        let getPhoto;

        if (photo) {
            let domain = "https://demoschool.senet.uz";
            const url = domain + '/account/ajax/student_chat/add_file.php';

            const response = await fetch(url, {
                method: "POST",
                body: formData
            });
            result = await response.json();
            getPhoto = domain + result.url;

        }

        const userData = {
            "Action": "CreateUser",
            "Data": {
                "related_id": userId,
                "name": userName,
                "avatar": getPhoto
            }
        }

        ws.send(JSON.stringify(userData))

        ws.addEventListener('message', function(event) {

            const getData = JSON.parse(event.data);

            let id = getData.Data.Related_id;
            let userName = getData.Data.Name;
            let avatar = getData.Data.Avatar;
            console.log(getData);

            //addNewUser(id, userName, avatar);
        });
    }

    openChat = function() {
        const chat = {
            "Action": "OpenChat",
            "Data": {
                "chat_id": 1,
                "sender_id": 0
            }
        }

        ws.send(JSON.stringify(chat));
        openChat = false;

        ws.addEventListener('message', function(event) {

            const getMessage = JSON.parse(event.data);
            const data = getMessage.Data.Messages;

            if (data) {

                const compareById = (a, b) => a.ID - b.ID;
                data.sort(compareById);

                data.forEach(message => {

                    const interData = JSON.parse(message.Data);

                    let id = message.ID;
                    let senderId = message.Student_id;
                    let text = interData.text;
                    let file = interData.file_name;
                    let url = interData.file_url;

                    if (file || text != '') {
                        addMessage(id, senderId, text, file, url);
                    }
                });
            }

            const userData = getMessage.Data;

            console.log(getMessage);
            if (userData) {

                const getData = JSON.parse(userData.Data);

                const id = userData.ID;
                const senderId = userData.Student_id;
                const message = getData.text;
                const fileName = getData.file_name;
                const file_url = getData.file_url;

                addMessage(id, senderId, message, fileName, file_url);
            }


        });

    };

    async function sendMessage(event) {
        event.preventDefault();
        const removeContent = document.getElementById("saveContent");
        removeContent.style.display = "none";

        const input = document.getElementById("attach-doc");
        const getFile = input.files[0];

        let file = null;
        let get_url = null;

        const formData = new FormData();
        formData.append("file", getFile);

        if (getFile) {
            const url = 'https://demoschool.senet.uz/account/ajax/student_chat/add_file.php';

            const response = await fetch(url, {
                method: "POST",
                body: formData
            });

            result = await response.json();
            get_url = 'https://demoschool.senet.uz' + result.url;
            file = getFile.name;
        }

        const message = messageInput.value;

        const msgData = {
            "Action": "SendMessage",
            "Data": {
                "chat_id": 1,
                "sender_id": 0,
                "message_type": "text",
                "data": {
                    "file_name": file,
                    "file_url": get_url,
                    "text": message
                }
            }
        }

        ws.send(JSON.stringify(msgData));

        messageInput.value = '';
    }

    const chatCon = document.querySelector("#chatContainer");

    function addMessage(id, senderId, message, file, get_url) {
        const leftBox = document.createElement('div');
        const rightBox = document.createElement('div');
        const chatBody = document.createElement("div");
        const chatBox = document.createElement('div');


        leftBox.className = "chat chat-left";
        rightBox.className = "chat";
        chatBody.className = "chat-body";
        chatBox.className = 'chat-content';
        chatBox.id = 'chat-box';

        rightBox.innerHTML = `
            <div class="chat-avatar">
                <span class="avatar box-shadow-1 cursor-pointer">
                    <img src="https://raw.githubusercontent.com/Samandar-Developer/Sam-blog/main/static/img/account.png" alt="avatar" height="36" width="36" />
                </span>
            </div>
            `;

        leftBox.innerHTML = `
            <div class="chat-avatar">
            <span class="avatar box-shadow-1 cursor-pointer">
                <img src="https://raw.githubusercontent.com/Samandar-Developer/Sam-blog/main/static/img/account.png" alt="avatar" height="36" width="36" />
            </span>
            </div>
            `

        if (file) {

            chatBox.innerHTML = `
                <a href="${get_url}" target="_blank" data-toggle="tooltip" data-placement="top" title="${file}">    
    <div class="card-header" style="align-items: center; padding: 12px 12px;">
        <div class="row">
            <div class="col-md-8">
                <h5 class="shortText">${file}</h5>
            </div>
            <div class="col-md-2 file-icon" style="display: flex; padding-left: 12px;">
                <div class="avatar bg-light-success get-icon">
                    <div class="avatar-content">
                        ${feather.icons['file'].toSvg()}
                    </div>
                </div>
            </div>
        </div>
        </div>
        </a>
        <div style="margin-top: 10px;">
            <p>${message}</p>
        </div>
            `;
        } else {
            chatBox.innerHTML = `
                <p>${message}</p>
                `;
        }
        chatBody.insertAdjacentElement('beforeend', chatBox);



        if (senderId != 0) {
            leftBox.insertAdjacentElement('beforeend', chatBody);
            chatCon.appendChild(leftBox);
        } else {
            rightBox.insertAdjacentElement('beforeend', chatBody);
            chatCon.appendChild(rightBox);
        }


    }

    this.addEventListener('keypress', event => {
        if (event.keyCode === 13) {
            const inputVal = messageInput.value
            if (inputVal.trim().length !== 0) {
                //sendMessage();
            }
        }
    });
</script>