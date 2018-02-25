<?PHP
require_once '../Static.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../logout.php");
}

require_once '../Database.php';
$database = new Database();

if (isset($_POST['submit'])) {

    $postType = filter_input(INPUT_POST, "submit");

    if ($postType === "AddReminder") {
        $desc = filter_input(INPUT_POST, "desc");
        $c_id = filter_input(INPUT_POST, "c_id");
        $amount = filter_input(INPUT_POST, "amount");
        $f_id = filter_input(INPUT_POST, "f_id");
        $date = filter_input(INPUT_POST, "date");

        $result = $database->addBillReminder($desc, $c_id, $amount, $f_id, $date);
    } else if ($postType === "DeleteReminder") {

        $br_id = filter_input(INPUT_POST, "br_id");
        $result = $database->deleteFrom("bill_reminder_table", "br_id", $br_id);
    } else if ($postType === "BillMarkComplete") {

        $br_id = filter_input(INPUT_POST, "br_id");
        $result = $database->billMarkCompleted($br_id);
    } else if ($postType === "SaveOwe") {

        $contact_id = filter_input(INPUT_POST, "contact_id");
        $c_id = filter_input(INPUT_POST, "c_id");
        $desc = filter_input(INPUT_POST, "desc");
        $amount = filter_input(INPUT_POST, "amount");
        $result = $database->addIOwe($contact_id, $c_id, $desc, $amount);
    } else if ($postType === "DeleteOwe") {

        $owe_id = filter_input(INPUT_POST, "owe_id");
        $result = $database->deleteFrom("owe_table", "owe_id", $owe_id);
    } else if ($postType === "BillMoveToExpenses") {

        $br_id = filter_input(INPUT_POST, "br_id");
        $database->billMoveToExpense($br_id);
    } else if ($postType === "OweMoveToExpenses") {

        $owe_id = filter_input(INPUT_POST, "owe_id");
        $database->oweMoveToExpense($owe_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?PHP getHeader(); ?>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/bootstrap-datepicker.standalone.min.css">
        <title>Dashboard</title>
    </head>

    <body>

        <?PHP getNavbar("dashboard"); ?>

        <div class="container">

            <div class="row">

                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header text-white bg-info pb-2">
                            <b><i class="fa fa-lightbulb-o"></i> Bill Reminders</b>
                        </div>

                        <ul class="list-group list-group-flush">

                            <?PHP
                            $result = $database->getBillReminder();
                            $count = mysqli_num_rows($result);
                            $today = date("d/m/Y");

                            if ($count > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $date = $row['date'];
                                    $status = $row['status'];

                                    if ($status === "unpaid") {
                                        ?>
                                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer" title="Unpaid">
                                            <div>
                                                <span class="text-info mr-2">
                                                    <i class="fa fa-info-circle"></i> <?PHP echo $date == $today ? "Today" : $date ?> 
                                                </span>
                                                - <?PHP echo $row['br_desc'] ?>
                                            </div>
                                            <div>
                                                <span class="badge badge-pill badge-info mr-2"><?PHP echo $row['amount'] . " " . $_SESSION['cur_code'] ?></span>
                                                <form method="POST" style="display: inline-block">
                                                    <input type="hidden" name="br_id" value="<?PHP echo $row['br_id'] ?>">
                                                    <button type="submit" name="submit" value="BillMarkComplete" class="btn btn-sm btn-outline-success py-0" title="Mark as completed"><i class="fa fa-check"></i></button>
                                                    <button type="submit" name="submit" value="BillMoveToExpenses" class="btn btn-sm btn-outline-info py-0" title="Move to expenses"><i class="fa fa-exchange"></i></button>
                                                    <button type="submit" name="submit" value="DeleteReminder" class="btn btn-sm btn-outline-danger py-0" title="Remove reminder"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </li>
                                        <?PHP
                                    } else {
                                        ?>
                                        <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer" title="Paid">
                                            <div>
                                                <span class="text-success mr-2">
                                                    <i class="fa fa-check"></i> <?PHP echo $date == $today ? "Today" : $date ?> 
                                                </span>
                                                - <?PHP echo $row['br_desc'] ?>
                                            </div>
                                            <div>
                                                <span class="badge badge-pill badge-info mr-2"><?PHP echo $row['amount'] . " " . $_SESSION['cur_code'] ?></span>
                                                <form method="POST" style="display: inline-block">
                                                    <input type="hidden" name="br_id" value="<?PHP echo $row['br_id'] ?>">
                                                    <button type="submit" name="submit" value="DeleteReminder" class="btn btn-sm btn-outline-danger py-0" title="Remove reminder"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </li>
                                        <?PHP
                                    }
                                }
                            } else {
                                echo '<li class="list-group-item list-group-item-action disabled">
                                    No bill reminders available
                                </li>';
                            }
                            ?>

                        </ul>

                        <div class="card-footer text-white bg-info">
                            <button class="btn btn-sm btn-light px-3" data-toggle="modal" data-target="#AddReminder"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card">
                        <div class="card-header text-white bg-info pb-2">
                            <b><i class="fa fa-money"></i> I Owe</b>
                        </div>
                        <ul class="list-group list-group-flush">

                            <?PHP
                            $result = $database->getIOwe();
                            $count = mysqli_num_rows($result);

                            if ($count > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer">
                                        <div>
                                            <?PHP echo $row['full_name'] ?>
                                            - <?PHP echo $row['owe_desc'] ?>
                                        </div>
                                        <div>
                                            <span class="badge badge-pill badge-info mr-2"><?PHP echo $row['amount'] . " " . $_SESSION['cur_code']; ?></span>
                                            <form method="POST" style="display: inline-block">
                                                <input type="hidden" name="owe_id" value="<?PHP echo $row['owe_id'] ?>">                                                
                                                <button type="submit" name="submit" value="OweMoveToExpenses" class="btn btn-sm btn-outline-info py-0" title="Move to expenses"><i class="fa fa-exchange"></i></button>
                                                <button type="submit" name="submit" value="DeleteOwe" class="btn btn-sm btn-outline-danger py-0" title="Remove i owe"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </li>
                                    <?PHP
                                }
                            } else {
                                echo '<li class="list-group-item list-group-item-action disabled">
                                    Empty
                                </li>';
                            }
                            ?>

                        </ul>

                        <div class="card-footer text-white bg-info">
                            <button class="btn btn-sm btn-light px-3" data-toggle="modal" data-target="#AddOwe"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mt-4">

                <?PHP
                $income = $database->getIncome();
                $totalIncome = 0;
                if (mysqli_num_rows($income) > 0) {
                    while ($row = mysqli_fetch_assoc($income)) {
                        $totalIncome += $row['amount'];
                    }
                }

                $expense = $database->getExpense();
                $totalExpense = 0;
                if (mysqli_num_rows($expense) > 0) {
                    while ($row = mysqli_fetch_assoc($expense)) {
                        $totalExpense += $row['amount'];
                    }
                }

                $totalSaving = $totalIncome - $totalExpense;
                ?>

                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header text-white bg-success">
                            Income <i class="fa fa-arrow-circle-up"></i>
                        </div>
                        <div class="card-body">
                            <span class="card-text text-success">Total income for this year : <?PHP echo $totalIncome . " " . $_SESSION['cur_code'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header text-white bg-danger">
                            Expenses <i class="fa fa-arrow-circle-down"></i>
                        </div>
                        <div class="card-body">
                            <span class="card-text text-danger">Total expenses for this year : <?PHP echo $totalExpense . " " . $_SESSION['cur_code'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-header text-white bg-info">
                            Savings <i class="fa fa-line-chart"></i>
                        </div>
                        <div class="card-body">
                            <span class="card-text text-info">Total saving for this year : <?PHP echo ($totalSaving < 0 ? 0 : $totalSaving) . " " . $_SESSION['cur_code'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">

                <div class="col">

                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-2 mb-0 text-center text-muted"><?PHP echo "Income vs. Expenses - " . CURRENT_YEAR ?></h4>

                            <div class="p-3">
                                <canvas id="incomeVsExpenses" style="max-width: 100%; min-height: 300px"></canvas>
                            </div>

                            <?PHP
                            $expenseMonths = $database->getExpenseDataForYear("2018");
                            $incomeMonths = $database->getIncomeDataForYear("2018");
                            ?>

                            <script>
                                window.onload = function () {

                                    var options = {
                                        maintainAspectRatio: false,
                                        spanGaps: false,
                                        elements: {
                                            line: {
                                                tension: 0.000001
                                            }
                                        },
                                        scales: {

                                        }
                                    };
                                    new Chart("incomeVsExpenses", {
                                        type: 'line',
                                        data: {
                                            labels: ["January", "February", "March", "April", "May", "June",
                                                "July", "August", "September", "October", "November", "December"],
                                            datasets: [
                                                {
                                                    label: "Income",
                                                    backgroundColor: "#3e95cd",
                                                    data: [<?PHP echo $incomeMonths ?>],
                                                    borderColor: "#3e95cd",
                                                    fill: false
                                                }, {
                                                    label: "Expenses",
                                                    backgroundColor: "#c45850",
                                                    data: [<?PHP echo $expenseMonths ?>],
                                                    borderColor: "#c45850",
                                                    fill: false
                                                }
                                            ]
                                        },
                                        options: options
                                    });
                                };
                            </script>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="modal fade" id="AddReminder" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white bg-info">
                        <h5 class="modal-title" id="modalLabel">Add reminder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Description</div>
                                </div>
                                <input type="text" name="desc" class="form-control form-control-sm" placeholder="Enter description" required>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Category</div>
                                </div>

                                <select name="c_id" class="form-control form-control-sm">

                                    <?PHP
                                    $categories = $database->getDebitCategory();
                                    if (mysqli_num_rows($categories) > 0) {
                                        while ($row = mysqli_fetch_assoc($categories)) {
                                            $id = $row['category_id'];
                                            $title = $row['title'];
                                            echo "<option value='$id'>$title</option>";
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Amount</div>
                                </div>
                                <input type="number" name="amount" class="form-control form-control-sm" placeholder="Enter amount" required>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Frequency</div>
                                </div>
                                <select name="f_id" class="form-control form-control-sm">
                                    <?PHP
                                    $frequencies = $database->getFrequency();
                                    if (mysqli_num_rows($frequencies) > 0) {
                                        while ($row = mysqli_fetch_assoc($frequencies)) {
                                            $id = $row['frequency_id'];
                                            $title = $row['title'];
                                            echo "<option value='$id'>$title</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Date</div>
                                </div>
                                <input type="text" data-provide="datepicker" name="date" class="form-control form-control-sm" placeholder="Enter date" value="<?PHP echo date("d/m/Y") ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">                            
                            <button name="submit" value="AddReminder" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Add Reminder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AddOwe" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white bg-info">
                        <h5 class="modal-title" id="modalLabel">Add i Owe</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Contact</div>
                                </div>

                                <select name="contact_id" class="form-control form-control-sm">

                                    <?PHP
                                    $contacts = $database->getContact();
                                    if (mysqli_num_rows($contacts) > 0) {
                                        while ($row = mysqli_fetch_assoc($contacts)) {
                                            $id = $row['contact_id'];
                                            $full_name = $row['full_name'];
                                            echo "<option value='$id'>$full_name</option>";
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Description</div>
                                </div>
                                <input type="text" name="desc" class="form-control form-control-sm" placeholder="Enter description" required>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Category</div>
                                </div>

                                <select name="c_id" class="form-control form-control-sm">

                                    <?PHP
                                    $categories = $database->getDebitCategory();
                                    if (mysqli_num_rows($categories) > 0) {
                                        while ($row = mysqli_fetch_assoc($categories)) {
                                            $id = $row['category_id'];
                                            $title = $row['title'];
                                            echo "<option value='$id'>$title</option>";
                                        }
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="input-group input-group-sm mb-3 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text text-white bg-info">Amount</div>
                                </div>
                                <input type="number" name="amount" class="form-control form-control-sm" placeholder="Enter amount" required>
                            </div>
                        </div>
                        <div class="modal-footer">                            
                            <button name="submit" value="SaveOwe" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?PHP getFooter(); ?>
        <script src="assets/js/bootstrap-datepicker.min.js"></script>
        <script src="assets/js/Chart.min.js"></script>
        <script>
            $('[data-provide="datepicker"]').datepicker({format: "dd/mm/yyyy", autoclose: true, todayHighlight: true});
        </script>
    </body>
</html>
