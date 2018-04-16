<nav id="sidebar" style="position:fixed;z-index:10;" class="nav flex-column bg-dark collapse multi-collapse">
    <br>
    <div class="row mx-auto">
    </div>
    <div id="UserArea" style="height:10%;">
        <div>
            <div class="row mx-auto">
                <hr>
                <div class="col-sm-12" id="usernameSec">
                    <div class="row mx-auto">
                        <div class="col-12" style="max-height:100%;">
                            <div class="row">
                                <div clas="col-12">
                                    <h4>
                                        {{Auth::user()->username}}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-auto">
                        <div clas="col-12">
                            <h5>
                                <div class="text-success" style="margin-left:2vw;">
                                    <i class="fas fa-plus"></i>{{Auth::user()->points}} Points</div>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr style="padding-bottom:1vh;">

    <!-- Sidebar Links -->
    <ul id = "sidebarElementsID" class="list-unstyled">
        <li class="active">
        <a href="{{url('users/'.Auth::user()->id)}}">
                <i class="fas fa-user"></i> Profile</a>
        </li>
        <li>
            <!-- Link with dropdown items -->
            <a id="sidebarDropdown2Button" href="#homeSubmenu2" data-target="#homeSubmenu2" data-toggle="collapse" aria-expanded="false">
                <i class="fas fa-archive"></i> Active Questions
                <span class="badge badge-pill badge-primary">3</span>
                <i id="arrowDown" class="fas fa-angle-down"></i>
            </a>
            <ul class="collapse list-unstyled" id="homeSubmenu2">
                <li>
                    <a href="./View Question/View Question.php">
                        <i class="far fa-envelope"></i> Java vs C++</a>
                </li>
                <li>
                    <a href="./View Question/View Question.php">
                        <i class="far fa-envelope"></i> Passing string by reference</a>
                </li>
                <li>
                    <a href="./View Question/View Question.php">
                        <i class="far fa-envelope"></i> JVM Thrash collector</a>
                </li>
            </ul>
        </li>
        @if(Auth::user()->type == 'ADMIN')
        <li class="active">
            <a href="./admin/admin.php">
                <i class="fas fa-cogs"></i> Administration</a>
        </li>
        @endif
        <hr>
        <li id="signOutID" class="active">
            <a style="margin-left:1%; width:98%;" href="{{ route('logout') }}" class="btn rounded-0 border border-dark btn-default btn-lg"
                onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <span class="glyphicon glyphicon-log-out"></span>Logout
            </a>

            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>
        </li>
    </ul>
</nav>