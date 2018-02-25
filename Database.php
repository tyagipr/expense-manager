<?php

class Database {

    private $connection;

    function __construct() {
        $this->connection = mysqli_connect(HOST, USER, PASSWORD, DATABASE);
    }

    function createAccount($cur_id, $first_name, $last_name, $email, $phone, $password) {
        $query = "INSERT INTO user_table (cur_id, first_name, last_name, email, phone, password) "
                . "VALUES ($cur_id, '$first_name', '$last_name', '$email', '$phone', '$password')";

        if (mysqli_query($this->connection, $query)) {
            return $this->login($email, $password);
        }

        return false;
    }

    function login($email, $password) {
        $query = "SELECT * from user_table WHERE email = '$email' AND password = '$password' LIMIT 1;";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];

            $this->updateCurrencyCode($row['cur_id']);
            return true;
        }

        return false;
    }

    function updateCurrencyCode($cur_id) {
        $query = "SELECT * from currencies_table WHERE cur_id = $cur_id  LIMIT 1;";

        $result = mysqli_query($this->connection, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION['cur_id'] = $row['cur_id'];
            $_SESSION['cur_code'] = $row['code'];
        }
    }

    function addContact($full_name, $phone) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO contact_table (user_id, full_name, phone) "
                . "VALUES ($user_id, '$full_name', '$phone')";
        return mysqli_query($this->connection, $query);
    }

    function getContact() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * from contact_table WHERE user_id = $user_id;";
        return mysqli_query($this->connection, $query);
    }

    function getDebitCategory() {
        $query = "SELECT * from debit_category_table";
        return mysqli_query($this->connection, $query);
    }

    function getCreditCategory() {
        $query = "SELECT * from credit_category_table";
        return mysqli_query($this->connection, $query);
    }

    function getFrequency() {
        $query = "SELECT * from frequency_table";
        return mysqli_query($this->connection, $query);
    }

    function getCountry() {
        $query = "SELECT * from currencies_table";
        return mysqli_query($this->connection, $query);
    }

    public function addExpense($desc, $c_id, $amount, $f_id, $date) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO expenses_table (user_id, category_id, frequency_id, ex_desc, amount, date) "
                . "VALUES ($user_id, $c_id, $f_id, '$desc', $amount, '$date')";
        return mysqli_query($this->connection, $query);
    }

    public function addIncome($desc, $c_id, $amount, $f_id, $date) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO income_table (user_id, category_id, frequency_id, i_desc, amount, date) "
                . "VALUES ($user_id, $c_id, $f_id, '$desc', $amount, '$date')";
        return mysqli_query($this->connection, $query);
    }

    public function addBillReminder($desc, $c_id, $amount, $f_id, $date) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO bill_reminder_table (user_id, category_id, frequency_id, br_desc, amount, date) "
                . "VALUES ($user_id, $c_id, $f_id, '$desc', $amount, '$date')";
        return mysqli_query($this->connection, $query);
    }

    public function addIOwe($contact_id, $c_id, $desc, $amount) {
        $user_id = $_SESSION['user_id'];
        $query = "INSERT INTO owe_table (user_id, category_id, contact_id, owe_desc, amount) "
                . "VALUES ($user_id, $c_id, $contact_id, '$desc', $amount)";
        return mysqli_query($this->connection, $query);
    }

    function getIOwe() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT owe_id, full_name, owe_desc, amount FROM owe_table INNER JOIN contact_table ON contact_table.contact_id = owe_table.contact_id WHERE owe_table.user_id = $user_id;";
        return mysqli_query($this->connection, $query);
    }

    function getBillReminder() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * from bill_reminder_table WHERE user_id = $user_id";
        return mysqli_query($this->connection, $query);
    }

    function getExpense() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT ex_id, date, ex_desc, title, amount FROM expenses_table INNER JOIN debit_category_table ON expenses_table.category_id = debit_category_table.category_id WHERE user_id = $user_id;";
        return mysqli_query($this->connection, $query);
    }

    function getIncome() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT i_id, date, i_desc, title, amount FROM income_table INNER JOIN credit_category_table ON income_table.category_id = credit_category_table.category_id WHERE user_id = $user_id;";
        return mysqli_query($this->connection, $query);
    }

    public function billMarkCompleted($br_id) {
        $query = "UPDATE bill_reminder_table SET status = 'paid' WHERE br_id = $br_id";
        return mysqli_query($this->connection, $query);
    }

    function deleteFrom($tableName, $columnName, $columnValue) {
        $query = "DELETE FROM $tableName WHERE $columnName = $columnValue";
        return mysqli_query($this->connection, $query);
    }

    public function billMoveToExpense($br_id) {
        $user_id = $_SESSION['user_id'];

        $query = "SELECT * from bill_reminder_table WHERE user_id = $user_id AND br_id = $br_id LIMIT 1";
        $result = mysqli_query($this->connection, $query);
        $row = mysqli_fetch_assoc($result);

        $c_id = $row['category_id'];
        $f_id = $row['frequency_id'];
        $desc = $row['br_desc'];
        $amount = $row['amount'];
        $date = $row['date'];

        $this->addExpense($desc, $c_id, $amount, $f_id, $date);
        $this->deleteFrom("bill_reminder_table", "br_id", $br_id);
    }

    public function oweMoveToExpense($owe_id) {
        $user_id = $_SESSION['user_id'];

        $query = "SELECT * from owe_table WHERE user_id = $user_id AND owe_id = $owe_id LIMIT 1";
        $result = mysqli_query($this->connection, $query);
        $row = mysqli_fetch_assoc($result);

        $c_id = $row['category_id'];
        $desc = $row['owe_desc'];
        $amount = $row['amount'];

        $this->addExpense($desc, $c_id, $amount, 1, date("d/m/Y"));
        $this->deleteFrom("owe_table", "owe_id", $owe_id);
    }

    public function getExpenseDataForYear($year) {
        $months = array();
        for ($index = 1; $index <= 12; $index++) {
            $months[sprintf("%02d", $index)] = 0;
        }

        $expense = $this->getExpense();
        while ($row = mysqli_fetch_assoc($expense)) {
            list($d, $m, $y) = explode('/', $row['date']);
            if ($year === $y) {
                $months[$m] += $row['amount'];
            }
        }

        return implode(", ", array_filter($months));
    }

    public function getIncomeDataForYear($year) {
        $months = array();
        for ($index = 1; $index <= 12; $index++) {
            $months[sprintf("%02d", $index)] = 0;
        }

        $expense = $this->getIncome();
        while ($row = mysqli_fetch_assoc($expense)) {
            list($d, $m, $y) = explode('/', $row['date']);
            if ($year === $y) {
                $months[$m] += $row['amount'];
            }
        }

        return implode(", ", array_filter($months));
    }

    public function getProfile() {
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * from user_table WHERE user_id = $user_id";
        $result = mysqli_query($this->connection, $query);
        return mysqli_fetch_assoc($result);
    }

    public function updateProfile($firstname, $lastname, $phone, $email, $newpassword) {
        $user_id = $_SESSION['user_id'];

        $query = "UPDATE user_table SET first_name = '$firstname', "
                . "last_name = '$lastname', "
                . "phone = '$phone', email = '$email', "
                . "password = '$newpassword'  WHERE user_id = $user_id";

        return mysqli_query($this->connection, $query);
    }

}
