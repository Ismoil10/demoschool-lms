    <!-- BEGIN: Content-->
    <div class="app-content content chat-application">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper container-xxl p-0">
            <div class="sidebar-left">
                <div class="sidebar">
                    <!-- Admin user profile area -->
                    <div class="chat-profile-sidebar">
                        <header class="chat-profile-header">
                            <span class="close-icon">
                                <i data-feather="x"></i>
                            </span>
                            <!-- User Information -->
                            <div class="header-profile-sidebar">
                                <div class="avatar box-shadow-1 avatar-xl avatar-border">
                                    <img src="../../../app-assets/images/portrait/small/avatar-s-11.jpg" alt="user_avatar" />
                                    <span class="avatar-status-online avatar-status-xl"></span>
                                </div>
                                <h4 class="chat-user-name">John Doe</h4>
                                <span class="user-post">Admin</span>
                            </div>
                            <!--/ User Information -->
                        </header>
                        <!-- User Details start -->
                        <div class="profile-sidebar-area">
                            <h6 class="section-label mb-1">About</h6>
                            <div class="about-user">
                                <textarea data-length="120" class="form-control char-textarea" id="textarea-counter" rows="5" placeholder="About User">
Dessert chocolate cake lemon drops jujubes. Biscuit cupcake ice cream bear claw brownie brownie marshmallow.</textarea>
                                <small class="counter-value float-right"><span class="char-count">108</span> / 120 </small>
                            </div>
                            <!-- To set user status -->
                            <h6 class="section-label mb-1 mt-3">Status</h6>
                            <ul class="list-unstyled user-status">
                                <li class="pb-1">
                                    <div class="custom-control custom-control-success custom-radio">
                                        <input type="radio" id="activeStatusRadio" name="userStatus" class="custom-control-input" value="online" checked />
                                        <label class="custom-control-label ml-25" for="activeStatusRadio">Active</label>
                                    </div>
                                </li>
                                <li class="pb-1">
                                    <div class="custom-control custom-control-danger custom-radio">
                                        <input type="radio" id="dndStatusRadio" name="userStatus" class="custom-control-input" value="busy" />
                                        <label class="custom-control-label ml-25" for="dndStatusRadio">Do Not Disturb</label>
                                    </div>
                                </li>
                                <li class="pb-1">
                                    <div class="custom-control custom-control-warning custom-radio">
                                        <input type="radio" id="awayStatusRadio" name="userStatus" class="custom-control-input" value="away" />
                                        <label class="custom-control-label ml-25" for="awayStatusRadio">Away</label>
                                    </div>
                                </li>
                                <li class="pb-1">
                                    <div class="custom-control custom-control-secondary custom-radio">
                                        <input type="radio" id="offlineStatusRadio" name="userStatus" class="custom-control-input" value="offline" />
                                        <label class="custom-control-label ml-25" for="offlineStatusRadio">Offline</label>
                                    </div>
                                </li>
                            </ul>
                            <!--/ To set user status -->

                            <!-- User settings -->
                            <h6 class="section-label mb-1 mt-2">Settings</h6>
                            <ul class="list-unstyled">
                                <li class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="check-square" class="mr-75 font-medium-3"></i>
                                        <span class="align-middle">Two-step Verification</span>
                                    </div>
                                    <div class="custom-control custom-switch mr-0">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch1" checked />
                                        <label class="custom-control-label" for="customSwitch1"></label>
                                    </div>
                                </li>
                                <li class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="bell" class="mr-75 font-medium-3"></i>
                                        <span class="align-middle">Notification</span>
                                    </div>
                                    <div class="custom-control custom-switch mr-0">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch2" />
                                        <label class="custom-control-label" for="customSwitch2"></label>
                                    </div>
                                </li>
                                <li class="mb-1 d-flex align-items-center cursor-pointer">
                                    <i data-feather="user" class="mr-75 font-medium-3"></i>
                                    <span class="align-middle">Invite Friends</span>
                                </li>
                                <li class="d-flex align-items-center cursor-pointer">
                                    <i data-feather="trash" class="mr-75 font-medium-3"></i>
                                    <span class="align-middle">Delete Account</span>
                                </li>
                            </ul>
                            <!--/ User settings -->

                            <!-- Logout Button -->
                            <div class="mt-3">
                                <button class="btn btn-primary">
                                    <span>Logout</span>
                                </button>
                            </div>
                            <!--/ Logout Button -->
                        </div>
                        <!-- User Details end -->
                    </div>
                    <!--/ Admin user profile area -->

                    <!-- Chat Sidebar area -->
                    <div class="sidebar-content">
                        <span class="sidebar-close-icon">
                            <i data-feather="x"></i>
                        </span>
                        <div class="col-1" style="padding-top: 20px; padding-left: 20px;">
                            <button type="button" class="btn btn-icon rounded-circle btn-primary" onclick="addAvatar()">
                                <i data-feather="user-plus"></i>
                            </button>
                        </div>
                        <!-- Sidebar Users start -->
                        <div id="users-list" class="chat-user-list-wrapper list-group">
                            <h4 class="chat-list-title">Talabalar</h4>
                            <div class="userList">
                            <ul class="chat-users-list chat-list media-list" onclick="openChat()">
                            <li>
                                <span class="avatar"><img src="${avatar}" height="42" width="42" alt="Generic placeholder image" />
                                    <span class="avatar-status-offline"></span>
                                </span>
                                <div class="chat-info flex-grow-1">
                                    <h5 class="mb-0">User</h5>
                                    <p class="card-text text-truncate">
                                        ...
                                    </p>
                                </div>
                                <div class="chat-meta text-nowrap">
                                    <small class="float-right mb-25 chat-time">4:14 PM</small>
                                    <span class="badge badge-danger badge-pill float-right">3</span>
                                </div>
                            </li>
                            <!-- NONE -->
                            <li class="no-results">
                                <h6 class="mb-0">No Chats Found</h6>
                            </li>
                        </ul>
                            </div>
                            <h4 class="chat-list-title">Guruhlar</h4>
                            <ul class="chat-users-list contact-list media-list">
                                <li>
                                    <span class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-7.jpg" height="42" width="42" alt="Generic placeholder image" />
                                    </span>
                                    <div class="chat-info">
                                        <h5 class="mb-0">Jenny Perich</h5>
                                        <p class="card-text text-truncate">
                                            Tart dragée carrot cake chocolate bar. Chocolate cake jelly beans caramels tootsie roll candy canes.
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <span class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" height="42" width="42" alt="Generic placeholder image" />
                                    </span>
                                    <div class="chat-info">
                                        <h5 class="mb-0">Sarah Montgomery</h5>
                                        <p class="card-text text-truncate">
                                            Tootsie roll sesame snaps biscuit icing jelly-o biscuit chupa chups powder.
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <span class="avatar"><img src="../../../app-assets/images/portrait/small/avatar-s-9.jpg" height="42" width="42" alt="Generic placeholder image" />
                                    </span>
                                    <div class="chat-info">
                                        <h5 class="mb-0">Heather Howell</h5>
                                        <p class="card-text text-truncate">
                                            Tart cookie dragée sesame snaps halvah. Fruitcake sugar plum gummies cheesecake toffee.
                                        </p>
                                    </div>
                                </li>
                                <li class="no-results">
                                    <h6 class="mb-0">No Contacts Found</h6>
                                </li>
                            </ul>
                        </div>
                        <!-- Sidebar Users end -->
                    </div>
                    <!--/ Chat Sidebar area -->

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper container-xxl p-0">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="body-content-overlay"></div>
                        <!-- Main chat area -->
                        <section class="chat-app-window">
                            <!-- To load Conversation -->
                            <div class="start-chat-area">
                                <div class="mb-1 start-chat-icon">
                                    <i data-feather="message-square"></i>
                                </div>
                                <h4 class="sidebar-toggle start-chat-text">Start Conversation</h4>
                            </div>
                            <!--/ To load Conversation -->

                            <!-- Active Chat -->
                            <div class="active-chat d-none">
                                <!--/ Chat Header -->

                                <!-- User Chat messages -->
                                <div class="user-chats">
                                    <div class="chats" id="chatContainer">
                                    </div>
                                </div>
                                <!-- User Chat messages -->

                                <!-- Submit Chat form -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card" id="saveContent" style="display: none; margin-bottom: 0px; padding: 10px 10px;">
                                            <div class="card-header" style="height: 50px; padding: 12px;">
                                                <div class="scrollable-container" style="overflow-x: auto;">
                                                    <h5 id="innerText" data-toggle="tooltip" data-placement="top">File</h5>
                                                </div>
                                                <div class="avatar bg-light-success">
                                                    <div class="avatar-content">
                                                        <i data-feather="file" class="font-medium-5"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <form class="chat-app-form" id="selectForm" enctype="multipart/form-data">
                                            <div class="input-group input-group-merge mr-1 form-send-message">
                                                <div class="input-group-prepend">
                                                    <span class="speech-to-text input-group-text"><i data-feather="mic" class="cursor-pointer"></i></span>
                                                </div>
                                                <input type="text" id="message-input" class="form-control message" placeholder="Type your message or use speech to text" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <label for="attach-doc" class="attachment-icon mb-0">
                                                            <i data-feather="image" class="cursor-pointer lighten-2 text-secondary"></i>
                                                            <input type="file" id="attach-doc" onchange="uploadFile()" hidden /> </label></span>
                                                </div>
                                            </div>
                                            <button class="btn btn-primary send" onclick="sendMessage(event);">
                                                <i data-feather="send" class="d-lg-none"></i>
                                                <span class="d-none d-lg-block">Send</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <!--/ Submit Chat form -->
                            </div>
                            <!--/ Active Chat -->
                        </section>
                        <!--/ Main chat area -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->
<? require "modules/med/student_chat/student_chat_js.php"; ?>
<? require "modules/med/student_chat/student_chat_modal.php"; ?>
    <style>
        #innerText {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 20ch;
        }

        .shortText {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 20ch;
        }
    </style>