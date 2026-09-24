<?php

require "config.php";

$successMessage = "";
$errorMessage = "";

$name = "";
$email = "";
$mobile = "";
$district = "";
$gender = "";
$address = "";


/*=========================================
        PROCESS REGISTRATION FORM
=========================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";
    $mobile = trim($_POST["mobile"] ?? "");
    $district = trim($_POST["district"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $address = trim($_POST["address"] ?? "");


    // ================================
    // SERVER-SIDE VALIDATION
    // ================================

    if ($name === "") {

        $errorMessage = "Name is required.";

    }

    elseif ($email === "") {

        $errorMessage = "Email is required.";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errorMessage = "Please enter a valid email address.";

    }

    elseif ($password === "") {

        $errorMessage = "Password is required.";

    }

    elseif (
        strlen($password) < 8 ||
        !preg_match("/[A-Za-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {

        $errorMessage =
            "Password must contain at least 8 characters with letters and numbers.";

    }

    elseif ($confirmPassword === "") {

        $errorMessage = "Please confirm your password.";

    }

    elseif ($password !== $confirmPassword) {

        $errorMessage = "Passwords do not match.";

    }

    elseif ($mobile === "") {

        $errorMessage = "Mobile number is required.";

    }

    elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {

        $errorMessage =
            "Mobile number must contain exactly 10 digits.";

    }


    // ================================
    // INSERT INTO DATABASE
    // ================================

    else {

        try {

            // Check whether email already exists

            $checkSql =
                "SELECT id FROM users WHERE email = ? LIMIT 1";

            $checkStmt = $conn->prepare($checkSql);

            $checkStmt->execute([$email]);


            if ($checkStmt->fetch()) {

                $errorMessage =
                    "This email is already registered. Please use another email.";

            }

            else {

                // Securely hash password

                $hashedPassword =
                    password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );


                // Insert user

                $sql = "
                    INSERT INTO users
                    (
                        name,
                        email,
                        password,
                        mobile,
                        district,
                        gender,
                        address
                    )
                    VALUES
                    (
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?
                    )
                ";


                $stmt = $conn->prepare($sql);


                $stmt->execute([
                    $name,
                    $email,
                    $hashedPassword,
                    $mobile,
                    $district,
                    $gender,
                    $address
                ]);


                $successMessage =
                    "Registration successful! Your details have been saved.";


                // Clear form after successful registration

                $name = "";
                $email = "";
                $mobile = "";
                $district = "";
                $gender = "";
                $address = "";

            }

        }

        catch (PDOException $e) {

            $errorMessage =
                "Database error: " . $e->getMessage();

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">


<head>

<meta charset="UTF-8">


<meta
name="viewport"
content="width=device-width, initial-scale=1.0"
>


<meta
name="description"
content="Register for Kashmir Horticulture Portal"
>


<meta
name="keywords"
content="Kashmir Horticulture, Registration, Kashmir, Agriculture"
>


<meta
name="author"
content="Javed Iqbal"
>


<title>
Register | Kashmir Horticulture
</title>


<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet"
>


<link
rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
>


<link
rel="stylesheet"
href="css/style.css"
>


<link
rel="stylesheet"
href="css/responsive.css"
>


</head>


<body>


<!-- =========================
     NAVBAR
========================= -->


<nav class="navbar navbar-expand-lg navbar-dark">


<div class="container">


<a
class="navbar-brand fw-bold"
href="index.html"
>

🌿 Kashmir Horticulture

</a>


<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbar"
aria-controls="navbar"
aria-expanded="false"
aria-label="Toggle navigation"
>

<span class="navbar-toggler-icon"></span>

</button>


<div
class="collapse navbar-collapse"
id="navbar"
>


<ul class="navbar-nav ms-auto">


<li class="nav-item">

<a
class="nav-link"
href="index.html"
>

Home

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="about.html"
>

About

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="gallery.html"
>

Gallery

</a>

</li>


<li class="nav-item">

<a
class="nav-link active"
href="register.php"
aria-current="page"
>

Register

</a>

</li>


<li class="nav-item">

<a
class="nav-link"
href="contact.html"
>

Contact

</a>

</li>


</ul>


</div>


</div>


</nav>



<!-- =========================
     REGISTRATION SECTION
========================= -->


<section class="py-5 mt-5">


<div class="container">


<div class="form-container">


<h2 class="text-center">

<i class="bi bi-person-plus-fill"></i>

User Registration

</h2>


<p class="text-center text-muted">

Create your account for the Kashmir Horticulture Portal.

</p>



<!-- =========================
     SUCCESS MESSAGE
========================= -->


<?php if ($successMessage !== ""): ?>


<div
class="alert alert-success mt-4"
role="alert"
>

<i class="bi bi-check-circle-fill"></i>

<?php

echo htmlspecialchars(
    $successMessage,
    ENT_QUOTES,
    "UTF-8"
);

?>

</div>


<?php endif; ?>



<!-- =========================
     ERROR MESSAGE
========================= -->


<?php if ($errorMessage !== ""): ?>


<div
class="alert alert-danger mt-4"
role="alert"
>

<i class="bi bi-exclamation-triangle-fill"></i>

<?php

echo htmlspecialchars(
    $errorMessage,
    ENT_QUOTES,
    "UTF-8"
);

?>

</div>


<?php endif; ?>



<!-- =========================
     REGISTRATION FORM
========================= -->


<form
id="registrationForm"
method="POST"
action="register.php"
>


<!-- NAME + EMAIL -->


<div class="row">


<div class="col-md-6 mb-3">


<label
for="name"
class="form-label"
>

Full Name

</label>


<input
type="text"
id="name"
name="name"
class="form-control"
placeholder="Enter your full name"
value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
onkeyup="clearError('nameError')"
>


<div
class="error"
id="nameError"
></div>


</div>



<div class="col-md-6 mb-3">


<label
for="email"
class="form-label"
>

Email Address

</label>


<input
type="email"
id="email"
name="email"
class="form-control"
placeholder="Enter your email"
value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
onkeyup="clearError('emailError')"
>


<div
class="error"
id="emailError"
></div>


</div>


</div>



<!-- PASSWORD + CONFIRM PASSWORD -->


<div class="row">


<div class="col-md-6 mb-3">


<label
for="password"
class="form-label"
>

Password

</label>


<input
type="password"
id="password"
name="password"
class="form-control"
placeholder="Enter password"
onkeyup="clearError('passwordError')"
>


<div
id="passwordStrength"
class="mt-2"
></div>


<div
class="error"
id="passwordError"
></div>


</div>



<div class="col-md-6 mb-3">


<label
for="confirmPassword"
class="form-label"
>

Confirm Password

</label>


<input
type="password"
id="confirmPassword"
name="confirmPassword"
class="form-control"
placeholder="Confirm password"
onkeyup="clearError('confirmError')"
>


<div
class="error"
id="confirmError"
></div>


</div>


</div>



<!-- MOBILE + DISTRICT -->


<div class="row">


<div class="col-md-6 mb-3">


<label
for="mobile"
class="form-label"
>

Mobile Number

</label>


<input
type="text"
id="mobile"
name="mobile"
class="form-control"
placeholder="Enter 10 digit mobile number"
maxlength="10"
inputmode="numeric"
value="<?php echo htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8'); ?>"
onkeyup="clearError('mobileError')"
>


<div
class="error"
id="mobileError"
></div>


</div>



<div class="col-md-6 mb-3">


<label
for="district"
class="form-label"
>

District

</label>


<select
id="district"
name="district"
class="form-select"
>


<option value="">

Select District

</option>


<option
value="Srinagar"
<?php if ($district === "Srinagar") echo "selected"; ?>
>

Srinagar

</option>


<option
value="Baramulla"
<?php if ($district === "Baramulla") echo "selected"; ?>
>

Baramulla

</option>


<option
value="Anantnag"
<?php if ($district === "Anantnag") echo "selected"; ?>
>

Anantnag

</option>


<option
value="Pulwama"
<?php if ($district === "Pulwama") echo "selected"; ?>
>

Pulwama

</option>


<option
value="Kupwara"
<?php if ($district === "Kupwara") echo "selected"; ?>
>

Kupwara

</option>


<option
value="Budgam"
<?php if ($district === "Budgam") echo "selected"; ?>
>

Budgam

</option>


<option
value="Shopian"
<?php if ($district === "Shopian") echo "selected"; ?>
>

Shopian

</option>


<option
value="Bandipora"
<?php if ($district === "Bandipora") echo "selected"; ?>
>

Bandipora

</option>


</select>


</div>


</div>



<!-- GENDER + ADDRESS -->


<div class="row">


<div class="col-md-6 mb-3">


<label
for="gender"
class="form-label"
>

Gender

</label>


<select
id="gender"
name="gender"
class="form-select"
>


<option
value="Male"
<?php if ($gender === "Male" || $gender === "") echo "selected"; ?>
>

Male

</option>


<option
value="Female"
<?php if ($gender === "Female") echo "selected"; ?>
>

Female

</option>


<option
value="Other"
<?php if ($gender === "Other") echo "selected"; ?>
>

Other

</option>


</select>


</div>



<div class="col-md-6 mb-3">


<label
for="address"
class="form-label"
>

Address

</label>


<textarea
id="address"
name="address"
class="form-control"
rows="3"
placeholder="Enter your address"
><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></textarea>


</div>


</div>



<!-- =========================
     BUTTONS
========================= -->


<div class="text-center mt-4">


<button
type="submit"
id="registerButton"
class="btn btn-success btn-lg"
>

<i class="bi bi-person-check-fill"></i>

Register

</button>


<button
type="reset"
class="btn btn-secondary btn-lg ms-2"
>

<i class="bi bi-arrow-counterclockwise"></i>

Reset

</button>


</div>


</form>


</div>


</div>


</section>



<!-- =========================
     FOOTER
========================= -->


<footer>


<div class="container text-center">


<h3>

Kashmir Horticulture Portal

</h3>


<p>

Promoting sustainable horticulture across Kashmir.

</p>


<a
href="index.html"
class="btn btn-success"
>

Back to Home

</a>


<hr>


<p>

© <span id="year">2026</span>
Kashmir Horticulture Portal

</p>


</div>


</footer>



<!-- =========================
     BOOTSTRAP JS
========================= -->


<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>



<!-- =========================
     VALIDATION JS
========================= -->


<script src="js/validation.js"></script>


</body>

</html>