<?php
session_start();
require_once('../config/database.php');

// 1. Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admins/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // This is the critical check
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role']; 
                    
                    if ($_SESSION['role'] === 'admin') {
                        header("Location: ../admins/dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit();
                } else {
                    $error = "Incorrect password.";
                }
            } else {
                $error = "Username not found.";
            }
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - WMSU Faculty Union</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
<link rel="icon" href="../images/facultyunion.png">

  <style>
    body { 
        font-family: 'Montserrat', sans-serif; 
        background: linear-gradient(135deg, #4a0d0d 0%, #150303 100%); 
        min-height: 100vh; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin: 0;
        padding: 40px 20px;
        position: relative;
        overflow-x: hidden;
        overflow-y: auto;
    }
    body::before, body::after {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        filter: blur(100px);
        z-index: -1;
        animation: float 10s infinite ease-in-out alternate;
    }
    body::before {
        background: rgba(212, 175, 55, 0.4); 
        top: -100px; left: -100px;
    }
    body::after {
        background: rgba(181, 44, 44, 0.6); 
        bottom: -100px; right: -100px;
        animation-delay: -5s;
    }
    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        100% { transform: translate(30px, 30px) scale(1.1); }
    }
    .login-card { 
        background: rgba(255, 255, 255, 0.97); 
        padding: 45px 40px; 
        width: 100%; 
        max-width: 420px; 
        box-shadow: 0 25px 50px rgba(0,0,0,0.4); 
        border-radius: 16px; 
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .login-header h2 { 
        font-family: 'Playfair Display', serif; 
        color: #601414; 
        text-align: center; 
        font-weight: 700; 
        font-size: 2rem;
        margin-bottom: 5px;
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
        background: #f8f9fa;
        height: auto;
    }
    .form-control:focus {
        border-color: #8c1d1d;
        box-shadow: 0 0 0 0.2rem rgba(140, 29, 29, 0.15);
        background: #fff;
    }
    .btn-login { 
        background: linear-gradient(90deg, #8c1d1d, #601414); 
        color: white; 
        font-weight: 700; 
        text-transform: uppercase; 
        border: none; 
        padding: 14px; 
        border-radius: 8px;
        transition: all 0.3s ease; 
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(140, 29, 29, 0.3);
    }
    .btn-login:hover { 
        background: linear-gradient(90deg, #d4af37, #b5952f); 
        color: #fff; 
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    .alert-custom { 
        background-color: #fff3f3; 
        color: #d32f2f; 
        border: 1px solid #ffcdd2; 
        padding: 12px; 
        margin-bottom: 25px; 
        border-radius: 8px; 
        text-align: center; 
        font-weight: 500;
        font-size: 0.9rem;
    }
    .header-logo {
        width: 100px;
        height: 100px;
        object-fit: contain;
        display: block;
        margin: 0 auto 20px auto;
        filter: drop-shadow(0 8px 15px rgba(0, 0, 0, 0.15));
    }
    .return-link {
        color: #888;
        text-decoration: none;
        transition: 0.3s;
        font-weight: 500;
    }
    .return-link:hover {
        color: #8c1d1d;
        text-decoration: none;
    }
    /* Floating Labels */
    .form-floating {
        position: relative;
        margin-bottom: 22px;
    }
    .form-floating input {
        width: 100%;
        padding: 24px 15px 8px 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        background: rgba(248, 249, 250, 0.8);
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .form-floating input:focus {
        border-color: #8c1d1d;
        box-shadow: 0 0 0 0.2rem rgba(140, 29, 29, 0.15);
        background: #fff;
        outline: none;
    }
    .form-floating label {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        padding: 16px 15px;
        pointer-events: none;
        transform-origin: 0 0;
        transition: opacity .2s ease-in-out, transform .2s ease-in-out;
        color: #777;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .form-floating input:focus ~ label,
    .form-floating input:not(:placeholder-shown) ~ label {
        transform: scale(0.75) translateY(-14px) translateX(2px);
        opacity: 0.8;
        color: #8c1d1d;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
        <img src="../images/facultyunion.png" alt="WMSU Faculty Union logo" class="header-logo">
      <h2>WMSU-FU</h2>
      <p class="text-center text-muted">Faculty Union Portal</p>
    </div>

    <?php if ($error): ?>
      <div class="alert-custom"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-floating">
        <input type="text" name="username" id="username" class="form-control" placeholder=" " required autofocus>
        <label for="username">Username</label>
      </div>
      <div class="form-floating">
        <input type="password" name="password" id="password" class="form-control" placeholder=" " required>
        <label for="password">Password</label>
      </div>
      <button type="submit" class="btn btn-login btn-block mt-4">Sign In</button>
    </form>
    <div class="text-center mt-4">
        <a href="../index.php" class="return-link small">&larr; Return to Website</a>
    </div>
  </div>
</body>
</html>
