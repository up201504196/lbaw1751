﻿<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap Login &amp; Register Templates</title>

        <link href="../assets/css/admin.css" rel="stylesheet">
        <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"> 
        <link href="../assets/css/bootstrap.css" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <link href="../assets/css/bars.css" rel="stylesheet">
        <link href="../assets/css/common.css" rel="stylesheet">
        <link href="../assets/css/contacts.css" rel="stylesheet">

        <script src="../assets/js/jquery-1.11.1.min.js"></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="../assets/js/jquery.min.js"></script>
        <script src="../assets/js/popper.min.js"></script>

    </head>

    <body>

        <div id="wrap" class="wrapper">
            @include('pages.sidebar')
            <div id="content">
                @include('pages.navbar logged in')
                <div id = "containerID">
                    <div id = "contentID">
                        <div id ="jumbotronID" class="jumbotron jumbotron-sm">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-12 col-lg-12">
                                        <h1 id ="titleID" class="h1 text-primary">
                                            Contact us
                                            <small>Feel free to contact us</small> </h1>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="classContainerID" class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="well well-sm">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">
                                                            Name</label>
                                                        <input type="text" class="form-control" id="name" placeholder="Enter name" required="required" />
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">
                                                            Email Address</label>
                                                        <div class="input-group">
                                                            <input type="email" class="form-control" id="email" placeholder="Enter email" required="required" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="subject">
                                                            Subject</label>
                                                        <select id="subject" name="subject" class="form-control" required="required">
                                                            <option value="na" selected="">Choose One:</option>
                                                            <option value="service">General Customer Service</option>
                                                            <option value="suggestions">Suggestions</option>
                                                            <option value="product">Product Support</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">
                                                            Message</label>
                                                        <textarea name="message" id="message" class="form-control" rows="7" cols="25" required="required" placeholder="Message" style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <button type="submit" class="btn btn-primary pull-right" id="btnContactUs">
                                                        Send Message</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <hr>

                                <div class="col-md-4">
                                    <form>
                                        <legend>
                                            <span class="fas fa-globe"></span> Our office</legend>
                                        <address>
                                            <strong>Faculdade de Engenharia da Universidade do Porto</strong>
                                            <br>Rua Dr. Roberto Frias, s/n 4200-465 Porto PORTUGAL
                                            <br>
                                            <abbr title="Phone">
                                                Phone:</abbr>
                                            (+351) 22 508 14 00
                                        </address>
                                        <address>
                                            <strong>Email</strong>
                                            <br>
                                            <a href="mailto:#"> feup@fe.up.pt</a>
                                        </address>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="../assets/js/bars.js"></script>


                </body>

                </html>