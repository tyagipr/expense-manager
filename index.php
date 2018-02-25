<?PHP
require_once './Static.php';

if (isset($_SESSION['user_id'])) {
    header("location: ./dashboard/");
}

require_once './Database.php';
$database = new Database();

if (isset($_POST['submit'])) {

    $postType = filter_input(INPUT_POST, "submit");

    $result = false;

    if ($postType === "SignUp") {
        $first_name = filter_input(INPUT_POST, "first_name");
        $last_name = filter_input(INPUT_POST, "last_name");
        $email = filter_input(INPUT_POST, "email");
        $phone = filter_input(INPUT_POST, "phone");
        $password = filter_input(INPUT_POST, "password");
        $cur_id = filter_input(INPUT_POST, "cur_id");

        $result = $database->createAccount($cur_id, $first_name, $last_name, $email, $phone, $password);
    } else if ($postType === "SignIn") {
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $result = $database->login($email, $password);
        $login_error = $result ? "" : "Invalid email or password!";
    }

    if ($result) {
        header("location: ./dashboard/");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>

        <?PHP getHeader(); ?>

        <link rel="stylesheet" href="assets/css/logn.css">
        <link rel="stylesheet" href="assets/css/form-elements.css">

        <title>Super Expense Manager - Login &amp; Sign Up</title>
    </head>

    <body>

        <div class="top-content">

            <div class="inner-bg">
                <div class="container">

                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text m-auto">
                            <h1><strong>Super Expense Manager</strong></h1>
                            <div class="description">
                                <p>
                                    Manage your daily and monthly <strong>Expenses</strong>, track them and find way to save <strong>Money</strong>.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5">

                            <div class="form-box">
                                <div class="form-top">
                                    <div class="form-top-left">
                                        <h3>Login to continue</h3>
                                        <p>Enter email and password to log on:</p>
                                    </div>
                                    <div class="form-top-right">
                                        <i class="fa fa-lock"></i>
                                    </div>
                                </div>
                                <div class="form-bottom">
                                    <form role="form" action="" method="post" class="login-form">
                                        <div class="form-group">
                                            <label class="sr-only" for="form-username">Email</label>
                                            <input type="email" name="email" placeholder="Email address..." class="form-username form-control" id="form-username">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="form-password">Password</label>
                                            <input type="password" name="password" placeholder="Password..." class="form-password form-control" id="form-password">
                                        </div>
                                        <button name="submit" value="SignIn" type="submit" class="btn">Sign in!</button>

                                        <?PHP
                                        if (isset($login_error)) {
                                            ?>
                                            <br><br>
                                            <div class="alert alert-warning alert-dismissible fade show my-0" role="alert">
                                                <strong>Warning!</strong> <?PHP echo $login_error ?>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <?PHP
                                        }
                                        ?>


                                    </form>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-1 middle-border"></div>
                        <div class="col-sm-1"></div>

                        <div class="col-sm-5">

                            <div class="form-box">
                                <div class="form-top">
                                    <div class="form-top-left">
                                        <h3>Sign up now</h3>
                                        <p>Fill in the form below for instant access:</p>
                                    </div>
                                    <div class="form-top-right">
                                        <i class="fa fa-pencil"></i>
                                    </div>
                                </div>
                                <div class="form-bottom">
                                    <form role="form" method="post" class="registration-form">
                                        <div class="form-group">
                                            <label class="sr-only">First name</label>
                                            <input type="text" name="first_name" placeholder="First name..." class="form-first-name form-control" pattern="[a-zA-Z]+">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">Last name</label>
                                            <input type="text" name="last_name" placeholder="Last name..." class="form-last-name form-control" pattern="[a-zA-Z]+">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">Email</label>
                                            <input type="email" name="email" placeholder="Email..." class="form-email form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">Phone</label>
                                            <input type="text" name="phone" placeholder="Phone..." class="form-contact form-control" pattern="\d{10}">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">Password</label>
                                            <input type="password" name="password" placeholder="Password..." class="form-password form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only">Country</label>
                                            <select name="cur_id" class="form-control">
                                                <?PHP
                                                $countries = $database->getCountry();
                                                if (mysqli_num_rows($countries) > 0) {
                                                    while ($row = mysqli_fetch_assoc($countries)) {
                                                        $country = $row['country'];
                                                        $cur_id = $row['cur_id'];
                                                        echo "<option value='$cur_id'>$country</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button name="submit" value="SignUp" type="submit" class="btn">Sign me up!</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>

        <footer>
            <div class="container">
                <div class="row">

                    <div class="col-sm-8 col-sm-offset-2 m-auto">
                        <div class="footer-border"></div>
                        An assignment work to get internship, Submitted by <strong>Pragya Tyagi</strong>
                    </div>

                </div>
            </div>
        </footer>

        <?PHP getFooter(); ?>

    </body>

</html>