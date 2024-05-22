<?php
session_start();

$host = "startrek-payroll-mysql";
$db_name = $_SERVER["MYSQL_DATABASE"];
$db_username = $_SERVER["MYSQL_USER"];
$db_password = $_SERVER["MYSQL_PASSWORD"];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['s'])) {
        // Xử lý đăng nhập
        $user = $_POST['user'];
        error_log("USERNAME:" . $user);
        $pass = $_POST['password'];
        error_log("PASSWORD:" . $pass);
        $sql = "SELECT concat(first_name, ' ', last_name) as name, salary FROM users WHERE username = ? AND password = ?";
        error_log("QUERY:" . $sql);
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user, $pass]);
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $_SESSION['loggedin'] = true;
                $_SESSION['user'] = $user;
                
                // Hiển thị thông tin người dùng
                echo "<center>";
                echo "<h2>Welcome, " . $user . "</h2><br>";
                echo "<table style='border-radius: 25px; border: 2px solid black;' cellspacing=30>";
                echo "<tr><th>Name</th><th>Salary</th></tr>";
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['salary'] . "</td>";
                echo "</tr>";
                echo "</table>";
                echo '<form method="post"><button type="submit" name="logout">Logout</button></form>';
                echo "</center>";
            } else {
                $error = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $error = "Error executing SQL statement: " . $e->getMessage();
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
