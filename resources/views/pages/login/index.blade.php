﻿<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap Login &amp; Register Templates</title>

        <!-- CSS -->
        <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <link rel="stylesheet" href="../assets/css/login/form-elements.css">
        <link rel="stylesheet" href="../assets/css/login/style.css">
        <link rel="stylesheet" href="../assets/css/common.css">

    </head>

    <body style="background: rgba(223, 220, 220, 0.842);">

        <!-- Top content -->
        <div class="top-content">

            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="social-login">
                                <div class="social-login-buttons">
                                    <a class="btn btn-link-1 btn-link-1-google-plus" href="../index logged in.php">
                                        <i class="fab fa-google-plus-g"></i> Login With Google
                                    </a>
                                    <a href="../index.php">
                                        <button class="btn btn-primary" style="background:#007bff;">
                                            <i class="fas fa-home"></i> Back To Home
                                        </button>
                                    </a> 
                                </div>
                            </div>
                            <div class="form-box" style="border: solid grey 1px;">
                                <div class=" form-top ">
                                    <div class="form-top-left ">
                                            @if($errors->any())
                                            <h4>{{$errors->first()}}</h4>
                                            @endif
                                        <h3>Login to our site</h3>
                                        <p>Enter username and password to log in:</p>
                                    </div>
                                    <div class="form-top-right ">
                                        <i class="fas fa-key "></i>
                                    </div>
                                </div>
                                <div class="form-bottom ">
                            <form action="/login" method="post">
                                    {!! csrf_field() !!}
                                    <!-- <form role="form" action="../index logged in.php" method="post" class="login-form "> -->
                                    <div class="form-group">
                                        <label class="sr-only " for="form-username">Username</label>
                                        <input type="text" name="username" placeholder="Username... " class="form-username form-control " id="form-username">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only " for="form-password">Password</label>
                                        <input type="password" name="pass_token" placeholder="Password...
                                               " class="form-password form-control" id="form-password">
                                    </div>
                                    <button type="submit" class="btn btn-lg center-block btn-primary" style="background:#007bff;">Sign in!</button>
                                    <!--</form>-->
                            </form>

                                    <div class="register-link ">
                                        <h3>
                                            <a href="./register">Register here </a>
                                        </h3>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Javascript -->
        <script src="../assets/js/jquery-1.11.1.min.js "></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js "></script>
        <script src="../assets/js/jquery.backstretch.min.js "></script>
        <script src="../assets/js/scripts.js "></script>

    </body>

</html>