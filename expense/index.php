<?PHP
require_once '../Static.php';

if (!isset($_SESSION['user_id'])) {
    header("location: ../logout.php");
}

require_once '../Database.php';
$database = new Database();

if (isset($_POST['submit'])) {

    $postType = filter_input(INPUT_POST, "submit");

    if ($postType === "AddExpense") {
        $desc = filter_input(INPUT_POST, "desc");
        $c_id = filter_input(INPUT_POST, "c_id");
        $amount = filter_input(INPUT_POST, "amount");
        $f_id = filter_input(INPUT_POST, "f_id");
        $date = filter_input(INPUT_POST, "date");

        $result = $database->addExpense($desc, $c_id, $amount, $f_id, $date);
    } else if ($postType === "DeleteExpense") {
        $ex_id = filter_input(INPUT_POST, "ex_id");
        $result = $database->deleteFrom("expenses_table", "ex_id", $ex_id);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?PHP getHeader(); ?>
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" href="assets/css/bootstrap-datepicker.standalone.min.css">
        <title>Expenses</title>
    </head>

    <body>

        <?PHP getNavbar("expenses"); ?>

        <div class="container">

            <div class="row">
                <?PHP
                $result = $database->getExpense();
                $count = mysqli_num_rows($result);
                $totalExpenses = 0;
                ?>

                <div class="card col px-0 mb-4">
                    <div class="card-header text-white bg-info">
                        <b><i class="fa fa-money"></i> All Expenses (<?PHP echo $count ?>)</b>
                    </div>

                    <div class="card-body px-0 py-0">

                        <table class="table my-0 text-center">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?PHP
                                $index = 1;

                                if ($count > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $ex_id = $row['ex_id'];
                                        $date = $row['date'];
                                        $ex_desc = $row['ex_desc'];
                                        $title = $row['title'];
                                        $amount = $row['amount'];
                                        $totalExpenses += $amount;
                                        ?>
                                        <tr>
                                            <td><?PHP echo $index ?></td>
                                            <td><?PHP echo $date ?></td>
                                            <td><?PHP echo $ex_desc ?></td>
                                            <td><?PHP echo $title ?></td>
                                            <td><?PHP echo $amount . " " . $_SESSION['cur_code'] ?></td>
                                            <td>
                                                <form method="POST" style="display: inline-block">
                                                    <input type="hidden" name="ex_id" value="<?PHP echo $ex_id ?>">
                                                    <button type="submit" name="submit" value="DeleteExpense" class="btn btn-sm btn-outline-danger py-0" title="Remove expense"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?PHP
                                        $index++;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" class="disabled">Empty</td>
                                    </tr>
                                    <?PHP
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>

                    <div class="card-footer text-white bg-info">
                        <button class="btn btn-sm btn-light px-3" data-toggle="modal" data-target="#AddExpense"><i class="fa fa-plus"></i> New</button>                        
                        <span class="pull-right mt-1">Total expenses : <?PHP echo $totalExpenses . " " . $_SESSION['cur_code'] ?></span>
                    </div>
                </div>

            </div>

            <div class="row mt-4">

                <div class="col p-0">

                    <div class="card">
                        <div class="card-body">

                            <h4 class="mt-2 mb-0 text-center text-muted"><?PHP echo "Expenses - " . CURRENT_YEAR ?></h4>

                            <div class="p-3">
                                <canvas id="expenses" style="max-width: 100%; min-height: 300px"></canvas>
                            </div>

                            <?PHP
                            $expenseMonths = $database->getExpenseDataForYear(CURRENT_YEAR);
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
                                    new Chart("expenses", {
                                        type: 'line',
                                        data: {
                                            labels: ["January", "February", "March", "April", "May", "June",
                                                "July", "August", "September", "October", "November", "December"],
                                            datasets: [
                                                {
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

        <div class="modal fade" id="AddExpense" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header text-white bg-info">
                        <h5 class="modal-title" id="modalLabel">Add Expense</h5>
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
                            <button name="submit" value="AddExpense" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Add Expense</button>
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
