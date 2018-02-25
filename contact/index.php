<?PHP
require_once '../Static.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../logout.php");
}

require_once '../Database.php';
$database = new Database();

if (isset($_POST['submit'])) {

    $postType = filter_input(INPUT_POST, "submit");

    if ($postType === "AddContact") {
        $full_name = filter_input(INPUT_POST, "full_name");
        $phone = filter_input(INPUT_POST, "phone");

        $result = $database->addContact($full_name, $phone);
    } else if ($postType === "DeleteContact") {
        $contact_id = filter_input(INPUT_POST, "contact_id");
        $result = $database->deleteFrom("contact_table", "contact_id", $contact_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?PHP getHeader(); ?>
        <link rel="stylesheet" href="assets/css/style.css">
        <title>Contacts</title>
    </head>

    <body>

        <?PHP getNavbar("contacts"); ?>

        <div class="container">
            <div class="row">
                <?PHP
                $result = $database->getContact();
                $count = mysqli_num_rows($result);
                ?>

                <div class="card col px-0">
                    <div class="card-header text-white bg-info">
                        <b><i class="fa fa-book"></i> All Contacts (<?PHP echo $count ?>)</b>
                    </div>

                    <div class="card-body px-0 py-0">

                        <table class="table my-0 text-center">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?PHP
                                $index = 1;

                                if ($count > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?PHP echo $index++; ?></th>
                                            <td><?PHP echo $row['full_name']; ?></td>
                                            <td><?PHP echo $row['phone']; ?></td>
                                            <td>
                                                <form method="POST">
                                                    <input type="hidden" name="contact_id" value="<?PHP echo $row['contact_id']; ?>">
                                                    <!--<button type="button" class="btn btn-sm btn-outline-info py-0" title="Edit contact"t><i class="fa fa-pencil"></i></button>-->
                                                    <button name="submit" type="submit" value="DeleteContact" class="btn btn-sm btn-outline-danger py-0 ml-1" title="Remove contact"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?PHP
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="disabled">No contacts available</td>
                                    </tr>
                                    <?PHP
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>

                    <div class="card-footer text-white bg-info">
                        <button class="btn btn-sm btn-light px-3" data-toggle="modal" data-target="#AddContactModal"><i class="fa fa-plus"></i> New</button>                        
                    </div>
                </div>

            </div>
        </div>

        <!--New Contact Modal-->
        <div class="modal fade" id="AddContactModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Add new contact</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Full Name</div>
                                </div>
                                <input type="text" name="full_name" class="form-control form-control-sm" placeholder="Enter full name" pattern="[a-zA-Z\s]+" required>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Phone</div>
                                </div>
                                <input type="text" name="phone" placeholder="Enter phone number" class="form-contact form-control form-control-sm" pattern="\d{10}" required>
                            </div>
                        </div>
                        <div class="modal-footer">                            
                            <button name="submit" value="AddContact" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Add Contact</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?PHP getFooter(); ?>
    <script src="assets/js/dashboard.js"></script>
</body>
</html>
