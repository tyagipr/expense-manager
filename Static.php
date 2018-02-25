<?php
session_start();
date_default_timezone_set("Asia/Calcutta");

define("HOST", "localhost");
define("USER", "root");
define("PASSWORD", "");
define("DATABASE", "expense_manager_db");
define("CURRENT_YEAR", date("Y"));

function getHeader() {
    ?>

    <base href="http://localhost/expensemanager/">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:400,100,300,500">

    <?PHP
}

function getNavbar($current) {
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

        <a class="navbar-brand" href="./dashboard"><b>Expense Manager</b></a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item <?PHP echo $current == "dashboard" ? "active" : "" ?>">
                    <a class="nav-link" href="./dashboard"> Dashboard</a>
                </li>
                <li class="nav-item <?PHP echo $current == "expenses" ? "active" : "" ?>">
                    <a class="nav-link" href="./expense">Expenses</a>
                </li>
                <li class="nav-item <?PHP echo $current == "income" ? "active" : "" ?>">
                    <a class="nav-link" href="./income">Income</a>
                </li>
                <li class="nav-item <?PHP echo $current == "contacts" ? "active" : "" ?>">
                    <a class="nav-link" href="./contact">Contacts</a>
                </li>
            </ul>
            <div class="justify-content-end">
                <ul class="navbar-nav">                    
                    <li class="nav-item <?PHP echo $current == "profile" ? "active" : "" ?>" >
                        <a class="nav-link mr-2" href="./profile" title="Your profile">Welcome <?PHP echo $_SESSION['first_name']; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="./logout.php" title="Sign Out"><i class="fa fa-power-off"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?PHP
}

function getFooter() {
    ?>
    <script src="assets/js/jquery-1.11.1.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.backstretch.min.js"></script>
    <script src="assets/js/scripts.js"></script>
    <?PHP
}
