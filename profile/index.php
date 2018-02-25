<?PHP
require_once '../Static.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../logout.php");
}

require_once '../Database.php';
$database = new Database();

$profile = $database->getProfile();

if (isset($_POST['update'])) {

    $oldpassword = filter_input(INPUT_POST, "oldpassword");

    if ($profile['password'] === $oldpassword) {

        $postType = filter_input(INPUT_POST, "update");

        if ($postType === "UpdateAccount") {
            $firstname = filter_input(INPUT_POST, "firstname");
            $lastname = filter_input(INPUT_POST, "lastname");
            $phone = filter_input(INPUT_POST, "phone");
            $email = filter_input(INPUT_POST, "email");
            $newpassword_ = filter_input(INPUT_POST, "newpassword");

            $newpassword = empty($newpassword_) ? $oldpassword : $newpassword_;

            $database->updateProfile($firstname, $lastname, $phone, $email, $newpassword);

            $profile = $database->getProfile();
            $_SESSION['first_name'] = $profile['first_name'];

            $info_message = "Account updated!";
        } else if ($postType === "DeleteAccount") {
            $user_id = $_SESSION['user_id'];

            $result = $database->deleteFrom("user_table", "user_id", $user_id);
            header("location: ../logout.php");
        }
    } else {
        $login_error = "Invalid password provided!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?PHP getHeader(); ?>
        <link rel="stylesheet" href="assets/css/style.css">
        <title>Contacts</title>
        <style>
            .input-group-text{
                width: 140px;
            }
        </style>
    </head>

    <body>

        <?PHP getNavbar("profile"); ?>

        <div class="container">

            <div class="row">

                <div class="col-6 m-auto">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            Manage Profile <i class="fa fa-user"></i>
                        </div>
                        <div class="card-body">

                            <form id="updateForm" method="post">

                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">First name</div>
                                    </div>
                                    <input value="<?PHP echo $profile['first_name'] ?>" type="text" name="firstname" class="form-control form-control-sm" placeholder="Enter first name" pattern="[a-zA-Z\s]+" required>
                                </div>
                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">Last name</div>
                                    </div>
                                    <input value="<?PHP echo $profile['last_name'] ?>" type="text" name="lastname" class="form-control form-control-sm" placeholder="Enter last name" pattern="[a-zA-Z\s]+" required>
                                </div>
                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">Phone</div>
                                    </div>
                                    <input value="<?PHP echo $profile['phone'] ?>" type="text" name="phone" class="form-control form-control-sm" placeholder="Enter phone number" pattern="\d{10}" required>
                                </div>
                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">Email</div>
                                    </div>
                                    <input value="<?PHP echo $profile['email'] ?>" type="email" name="email" class="form-control form-control-sm" placeholder="Enter email address" required>
                                </div>
                                <hr>
                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">New Password</div>
                                    </div>
                                    <input type="password" name="newpassword" class="form-control form-control-sm" placeholder="Enter new password">
                                </div>
                                <div class="input-group input-group-sm mb-3 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text text-white bg-info">Current Password</div>
                                    </div>
                                    <input type="password" name="oldpassword" class="form-control form-control-sm" placeholder="Enter current password" required>
                                </div>

                                <button type="submit" name="update" value="UpdateAccount" class="btn btn-primary mt-3 pull-right"><i class="fa fa-check"></i> Save Changes</button>
                                <button type="submit" name="update" value="DeleteAccount" class="btn btn-danger mt-3"><i class="fa fa-trash"></i> Delete Changes</button>
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
                                if (isset($info_message)) {
                                    ?>
                                    <br><br>
                                    <div class="alert alert-success alert-dismissible fade show my-0" role="alert">
                                        <strong>Information!</strong> <?PHP echo $info_message ?>
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

            </div>

        </div>

        <?PHP getFooter(); ?>

    </body>
</html>
