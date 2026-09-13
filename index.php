<?php
session_start();

   //DATABASE CONNECTION

$host = "localhost";
$user = "root";
$password = "9501Chris#";
$database = "beauty_parlour";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";
$error = "";

   //LOGOUT

if (isset($_GET['logout'])) {

    session_unset();
    session_destroy();

    header("Location: index.php");
    exit();
}

   //CREATE ACCOUNT

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password1 = $_POST['password'];
    $password2 = $_POST['confirm_password'];

    if (
        empty($name) ||
        empty($email) ||
        empty($phone) ||
        empty($password1) ||
        empty($password2)
    ) {

        $error = "Please complete all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password1 !== $password2) {

        $error = "Passwords do not match.";

    } elseif (strlen($password1) < 6) {

        $error = "Password must contain at least 6 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM clients WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $error = "An account with this email already exists.";

        } else {


            $hashedPassword = password_hash(
                $password1,
                PASSWORD_DEFAULT
            );

           //Insert client

            $stmt = $conn->prepare(
                "INSERT INTO clients
                (name, email, password, phone)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashedPassword,
                $phone
            );

            if ($stmt->execute()) {

                $message =
                    "Account created successfully. You can now login.";

            } else {

                $error =
                    "Could not create account: " .
                    $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}

   //LOGIN

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password, phone
             FROM clients
             WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $client = $result->fetch_assoc();

            //Verify hashed password

            if (
                password_verify(
                    $password,
                    $client['password']
                )
            ) {

                //Create session

                $_SESSION['client_id'] =
                    $client['id'];

                $_SESSION['client_name'] =
                    $client['name'];

                $_SESSION['client_email'] =
                    $client['email'];

                $_SESSION['client_phone'] =
                    $client['phone'];

                //Remember Me

                if (isset($_POST['remember'])) {

                    setcookie(
                        "remember_email",
                        $email,
                        time() + (7 * 24 * 60 * 60),
                        "/"
                    );
                }

                header("Location: index.php");
                exit();

            } else {

                $error =
                    "Incorrect email or password.";
            }

        } else {

            $error =
                "Incorrect email or password.";
        }

        $stmt->close();
    }
}

   //BOOK APPOINTMENT
 

if (
    isset($_POST['book']) &&
    isset($_SESSION['client_id'])
) {

    $service = $_POST['service'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    if (
        empty($service) ||
        empty($date) ||
        empty($time)
    ) {

        $error =
            "Please complete all appointment fields.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO appointments
            (client_id, service, appointment_date,
             appointment_time, status)
            VALUES (?, ?, ?, ?, 'Upcoming')"
        );

        $stmt->bind_param(
            "isss",
            $_SESSION['client_id'],
            $service,
            $date,
            $time
        );

        if ($stmt->execute()) {

            $message =
                "Appointment booked successfully.";

        } else {

            $error =
                "Could not book appointment.";
        }

        $stmt->close();
    }
}

  //CANCEL APPOINTMENT

if (
    isset($_POST['cancel']) &&
    isset($_SESSION['client_id'])
) {

    $appointment_id =
        intval($_POST['appointment_id']);

    $stmt = $conn->prepare(
        "UPDATE appointments
         SET status = 'Cancelled'
         WHERE id = ?
         AND client_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $appointment_id,
        $_SESSION['client_id']
    );

    if ($stmt->execute()) {

        $message =
            "Appointment cancelled successfully.";

    } else {

        $error =
            "Could not cancel appointment.";
    }

    $stmt->close();
}

//UPDATE APPOINTMENT

if (
    isset($_POST['update_appointment']) &&
    isset($_SESSION['client_id'])
) {

    $appointment_id =
        intval($_POST['appointment_id']);

    $service = $_POST['service'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    if (
        empty($service) ||
        empty($date) ||
        empty($time)
    ) {

        $error =
            "Please complete all fields.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE appointments
             SET service = ?,
                 appointment_date = ?,
                 appointment_time = ?
             WHERE id = ?
             AND client_id = ?"
        );

        $stmt->bind_param(
            "sssii",
            $service,
            $date,
            $time,
            $appointment_id,
            $_SESSION['client_id']
        );

        if ($stmt->execute()) {

            $message =
                "Appointment updated successfully.";

        } else {

            $error =
                "Could not update appointment.";
        }

        $stmt->close();
    }
}

   //GET REMEMBERED EMAIL
 

$rememberedEmail = "";

if (isset($_COOKIE['remember_email'])) {

    $rememberedEmail =
        $_COOKIE['remember_email'];
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Beauty Parlour Appointment System</title>


<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial, sans-serif;

    background: #f8e8ef;
}


header {

    background: #8e4b68;

    color: white;

    text-align: center;

    padding: 25px;
}


.container {

    width: 90%;

    max-width: 1000px;

    margin: 30px auto;
}


.card {

    background: white;

    padding: 30px;

    border-radius: 12px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.15);

    margin-bottom: 25px;
}



.login-card {

    max-width: 450px;

    margin: 40px auto;
}

h2 {

    color: #8e4b68;

    text-align: center;
}


label {

    display: block;

    margin-top: 15px;

    font-weight: bold;
}



input,
select {

    width: 100%;

    padding: 12px;

    margin-top: 6px;

    border: 1px solid #ccc;

    border-radius: 5px;
}


.checkbox {

    width: auto;

    margin-right: 5px;
}


button {

    background: #8e4b68;

    color: white;

    border: none;

    padding: 12px 18px;

    border-radius: 5px;

    cursor: pointer;

    margin-top: 20px;

    font-size: 15px;
}


button:hover {

    background: #713b53;
}


a {

    color: #8e4b68;

    text-decoration: none;

    font-weight: bold;
}

.center {

    text-align: center;
}


.success {

    background: #d4edda;

    color: #155724;

    padding: 15px;

    border-radius: 5px;

    margin-bottom: 20px;
}


.error {

    background: #f8d7da;

    color: #721c24;

    padding: 15px;

    border-radius: 5px;

    margin-bottom: 20px;
}


.menu {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

    margin-top: 25px;
}


.menu a {

    background: #8e4b68;

    color: white;

    padding: 14px;

    border-radius: 5px;

    flex: 1;

    min-width: 180px;

    text-align: center;
}


table {

    width: 100%;

    border-collapse: collapse;

    margin-top: 20px;
}


th {

    background: #8e4b68;

    color: white;
}


th,
td {

    padding: 12px;

    border: 1px solid #ddd;

    text-align: center;
}

.update-button {

    background: #007bff;

    color: white;

    padding: 8px 12px;

    border-radius: 4px;
}


.cancel-button {

    background: #dc3545;

    color: white;

    border: none;

    padding: 8px 12px;

    border-radius: 4px;

    margin-top: 0;
}


.logout {

    background: #333 !important;
}


@media(max-width:700px) {

    table {

        font-size: 12px;
    }

    th,
    td {

        padding: 7px;
    }
}

</style>

</head>


<body>


<header>

<h1>Beauty Parlour</h1>

<p>Online Appointment Booking System</p>

</header>


<div class="container">


<?php if ($message != ""): ?>

<div class="success">

<?php echo htmlspecialchars($message); ?>

</div>

<?php endif; ?>


<?php if ($error != ""): ?>

<div class="error">

<?php echo htmlspecialchars($error); ?>

</div>

<?php endif; ?>


<?php

   //LOGIN / CREATE ACCOUNT

if (!isset($_SESSION['client_id'])) {

   //CREATE ACCOUNT PAGE
if (isset($_GET['create'])) {

?>


<div class="card login-card">

<h2>Create Account</h2>


<form method="POST">


<label>Full Name</label>

<input
    type="text"
    name="name"
    placeholder="Enter your full name"
    required
>


<label>Email</label>

<input
    type="email"
    name="email"
    placeholder="Enter your email"
    required
>


<label>Phone Number</label>

<input
    type="text"
    name="phone"
    placeholder="Enter your phone number"
    required
>


<label>Password</label>

<input
    type="password"
    name="password"
    placeholder="Create password"
    required
>


<label>Confirm Password</label>

<input
    type="password"
    name="confirm_password"
    placeholder="Confirm password"
    required
>


<button
    type="submit"
    name="register"
>

Create Account

</button>


</form>


<p class="center">

Already have an account?

<a href="index.php">

Login

</a>

</p>


</div>


<?php


   //LOGIN PAGE

} else {

?>


<div class="card login-card">

<h2>Client Login</h2>

<p class="center">

Please login to manage your appointments.

</p>


<form method="POST">


<label>Email</label>

<input
    type="email"
    name="email"
    value="<?php
        echo htmlspecialchars($rememberedEmail);
    ?>"
    placeholder="Enter email"
    required
>


<label>Password</label>

<input
    type="password"
    name="password"
    placeholder="Enter password"
    required
>


<label>

<input
    type="checkbox"
    name="remember"
    class="checkbox"
>

Remember Me

</label>


<button
    type="submit"
    name="login"
>

Login

</button>


</form>


<p class="center">

Don't have an account?

<a href="index.php?create=1">

Create Account

</a>

</p>


</div>


<?php

}

}



   //LOGGED IN DASHBOARD
 

else {

?>


<div class="card">

<h2>

Welcome,
<?php
echo htmlspecialchars(
    $_SESSION['client_name']
);
?>

</h2>


<p class="center">

You are successfully logged in.

</p>


<div class="menu">


<a href="index.php">

Dashboard

</a>


<a href="index.php?page=appointments">

View Appointments

</a>


<a href="index.php?page=book">

Book Appointment

</a>


<a href="index.php?page=profile">

My Profile

</a>


<a
    href="index.php?logout=1"
    class="logout"
>

Logout

</a>


</div>

</div>


<?php


   //VIEW APPOINTMENTS
  

if (
    isset($_GET['page']) &&
    $_GET['page'] == "appointments"
) {

?>


<div class="card">

<h2>My Appointments</h2>


<?php

$stmt = $conn->prepare(
    "SELECT id,
            service,
            appointment_date,
            appointment_time,
            status
     FROM appointments
     WHERE client_id = ?
     ORDER BY appointment_date,
              appointment_time"
);

$stmt->bind_param(
    "i",
    $_SESSION['client_id']
);

$stmt->execute();

$result = $stmt->get_result();

?>


<table>

<tr>

<th>Service</th>

<th>Date</th>

<th>Time</th>

<th>Status</th>

<th>Actions</th>

</tr>


<?php

//LOOP THROUGH APPOINTMENTS

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

?>


<tr>

<td>

<?php
echo htmlspecialchars(
    $row['service']
);
?>

</td>


<td>

<?php
echo htmlspecialchars(
    $row['appointment_date']
);
?>

</td>


<td>

<?php
echo htmlspecialchars(
    $row['appointment_time']
);
?>

</td>


<td>

<?php
echo htmlspecialchars(
    $row['status']
);
?>

</td>


<td>


<?php

if ($row['status'] != "Cancelled") {

?>


<a
    href="index.php?page=update&id=<?php
        echo $row['id'];
    ?>"
>

<button
    type="button"
    class="update-button"
>

Update

</button>

</a>


<form
    method="POST"
    style="display:inline;"
    onsubmit="
        return confirm(
            'Are you sure you want to cancel this appointment?'
        );
    "
>


<input
    type="hidden"
    name="appointment_id"
    value="<?php
        echo $row['id'];
    ?>"
>


<button
    type="submit"
    name="cancel"
    class="cancel-button"
>

Cancel

</button>


</form>


<?php

} else {

echo "Cancelled";

}

?>


</td>

</tr>


<?php

    }

} else {

?>


<tr>

<td colspan="5">

No appointments found.

</td>

</tr>


<?php

}

$stmt->close();

?>

</table>

</div>


<?php

}


   //BOOK APPOINTMENT


if (
    isset($_GET['page']) &&
    $_GET['page'] == "book"
) {

?>


<div class="card">

<h2>Book Appointment</h2>


<form method="POST">


<label>Service</label>

<select name="service" required>

<option value="">
Select a service
</option>

<option value="Haircut">
Haircut
</option>

<option value="Hair Styling">
Hair Styling
</option>

<option value="Manicure">
Manicure
</option>

<option value="Pedicure">
Pedicure
</option>

<option value="Facial">
Facial
</option>

<option value="Massage">
Massage
</option>

</select>


<label>Appointment Date</label>

<input
    type="date"
    name="appointment_date"
    required
>


<label>Appointment Time</label>

<input
    type="time"
    name="appointment_time"
    required
>


<button
    type="submit"
    name="book"
>

Book Appointment

</button>


</form>

</div>


<?php

}

   //UPDATE APPOINTMENT

if (
    isset($_GET['page']) &&
    $_GET['page'] == "update" &&
    isset($_GET['id'])
) {

    $appointment_id =
        intval($_GET['id']);

    $stmt = $conn->prepare(
        "SELECT *
         FROM appointments
         WHERE id = ?
         AND client_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $appointment_id,
        $_SESSION['client_id']
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows == 1) {

        $appointment =
            $result->fetch_assoc();

?>


<div class="card">

<h2>Update Appointment</h2>


<form method="POST">


<input
    type="hidden"
    name="appointment_id"
    value="<?php
        echo $appointment['id'];
    ?>"
>


<label>Service</label>

<select name="service" required>


<option
    value="Haircut"
    <?php
    if ($appointment['service'] == "Haircut")
        echo "selected";
    ?>
>
Haircut
</option>


<option
    value="Hair Styling"
    <?php
    if ($appointment['service'] == "Hair Styling")
        echo "selected";
    ?>
>
Hair Styling
</option>


<option
    value="Manicure"
    <?php
    if ($appointment['service'] == "Manicure")
        echo "selected";
    ?>
>
Manicure
</option>


<option
    value="Pedicure"
    <?php
    if ($appointment['service'] == "Pedicure")
        echo "selected";
    ?>
>
Pedicure
</option>


<option
    value="Facial"
    <?php
    if ($appointment['service'] == "Facial")
        echo "selected";
    ?>
>
Facial
</option>


<option
    value="Massage"
    <?php
    if ($appointment['service'] == "Massage")
        echo "selected";
    ?>
>
Massage
</option>


</select>


<label>Appointment Date</label>

<input
    type="date"
    name="appointment_date"
    value="<?php
        echo htmlspecialchars(
            $appointment['appointment_date']
        );
    ?>"
    required
>


<label>Appointment Time</label>

<input
    type="time"
    name="appointment_time"
    value="<?php
        echo htmlspecialchars(
            $appointment['appointment_time']
        );
    ?>"
    required
>


<button
    type="submit"
    name="update_appointment"
>

Update Appointment

</button>


</form>

</div>


<?php

    }

    $stmt->close();
}

   //PROFILE

if (
    isset($_GET['page']) &&
    $_GET['page'] == "profile"
) {

?>


<div class="card">

<h2>My Profile</h2>


<form method="POST">


<label>Full Name</label>

<input
    type="text"
    name="name"
    value="<?php
        echo htmlspecialchars(
            $_SESSION['client_name']
        );
    ?>"
    required
>


<label>Email</label>

<input
    type="email"
    name="email"
    value="<?php
        echo htmlspecialchars(
            $_SESSION['client_email']
        );
    ?>"
    required
>


<label>Phone Number</label>

<input
    type="text"
    name="phone"
    value="<?php
        echo htmlspecialchars(
            $_SESSION['client_phone']
        );
    ?>"
    required
>


<button
    type="submit"
    name="update_profile"
>

Update Profile

</button>


</form>

</div>


<?php

}

}

?>


</div>

</body>

</html>