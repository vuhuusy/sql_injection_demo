<?php
session_start();

$host = "startrek-payroll-mysql";
$db_name = $_SERVER["MYSQL_DATABASE"];
$db_username = $_SERVER["MYSQL_USER"];
$db_password = $_SERVER["MYSQL_PASSWORD"];

$conn = new mysqli($host, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['s'])) {
        // Xử lý đăng nhập
        $user = $_POST['user'];
        error_log("USERNAME:" . $user);
        $pass = $_POST['password'];
        error_log("PASSWORD:" . $pass);
        $sql = "select first_name, salary from users where username = '$user' and password = '$pass'";
        error_log("QUERY:" . $sql);

        if ($conn->multi_query($sql)) {
            $hasResults = false;  // Biến để kiểm tra xem có kết quả hay không
            do {
                if ($result = $conn->store_result()) {
                    if ($result->num_rows > 0) {
                        $hasResults = true;
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user'] = $user;

                        // Hiển thị thông tin người dùng
                        echo "<center>";
                        echo "<h2>Welcome, " . $user . "</h2><br>";
                        echo "<table style='border-radius: 25px; border: 2px solid black;' cellspacing=30>";
                        echo "<tr><th>Name</th><th>Salary</th></tr>";
                        while ($row = $result->fetch_assoc()) {
                            $keys = array_keys($row);
                            echo "<tr>";
                            foreach ($keys as $key) {
                                echo "<td>" . $row[$key] . "</td>";
                            }
                            echo "</tr>\n";
                        }
                        echo "</table>";
                        echo '<form method="post"><button type="submit" name="logout">Logout</button></form>';
                        echo "</center>";
                    }
                    $result->free();
                }
            } while ($conn->next_result());

            if (!$hasResults) {
                // Nếu không có kết quả nào, thiết lập thông báo lỗi
                $error = "Invalid username or password";
            }
        }
    } elseif (isset($_POST['logout'])) {
        // Xử lý đăng xuất
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Nếu người dùng chưa đăng nhập, hiển thị form đăng nhập
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
?>
    <center>
        <form action="" method="post">
            <h2>Payroll Login</h2>
            <table style="border-radius: 25px; border: 2px solid black; padding: 20px;">
                <tr>
                    <td>User</td>
                    <td><input type="text" name="user"></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td><input type="submit" value="OK" name="s">
                </tr>
            </table>
            <?php if ($error): ?>
                <p style="color: red;"><?= $error ?></p>
            <?php endif; ?>
        </form>
    </center>
<?php
}
?>

