<?php
session_start();
require "config/db.php";

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    // Existing code
    $_SESSION['user'] = $user;
    $_SESSION['role'] = $user['role']; 

    // ADD THIS LINE
    $_SESSION['user_id'] = $user['id']; 

    // ... rest of redirect logic

        // Redirect based on role
        if (isset($user['role']) && $user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php"); 
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Justinmind Sign In</title>
  <link rel="stylesheet" href="login.css">
  <style>
    /* Styling for the error message to fit your design */
    .error-msg {
      color: #e74c3c;
      background: #fdeaea;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-size: 0.9rem;
      text-align: center;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="signin-box">
      <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" alt="KISKEN TRENDS DUUKA" class="logo">
      <h2>Sign in to your account</h2>

      <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
      <?php endif; ?>

      <button class="google-btn">
        <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">
        Continue with Google
      </button>

      <div class="divider">
        <span>or</span>
      </div>

      <form id="signinForm" method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email address" id="email" required>
        
        <div class="password-wrapper">
          <input type="password" name="password" placeholder="Password" id="password" required>
          <span id="togglePassword" class="eye">👁️</span>
        </div>

        <a href="#" class="forgot-link">Forgot password?</a>

        <button type="submit" class="signin-btn">Sign in</button>
      </form>

      <a href="register.php" class="signup-link"> Create an account</a>
    </div>
    <br>

    <div class="back-home-container">
      <a href="index.php" class="back-home-btn">← Back to Home</a>
    </div>
  </div>

  <script>
    // Password toggle logic
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });
  </script>
</body>
</html>
<style>
  /* ─── Reset & base ─── */
*, *::before, *::after { box-sizing: border-box; }

body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f4f6f9;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ─── Page wrapper ─── */
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 1.5rem 1rem;
  width: 100%;
}

/* ─── Card ─── */
.signin-box {
  background: #fff;
  border: 1px solid #e8eaed;
  border-radius: 12px;
  padding: 2rem 2rem 1.75rem;
  width: 100%;
  max-width: 380px;
  text-align: center;
}

/* ─── Logo ─── */
.logo {
  height: 44px;
  margin-bottom: 1.25rem;
}

/* ─── Heading ─── */
h2 {
  font-size: 17px;
  font-weight: 500;
  color: #111;
  margin: 0 0 1.5rem;
  letter-spacing: -0.01em;
}

/* ─── Error ─── */
.error-msg {
  font-size: 13px;
  color: #b91c1c;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 9px 12px;
  margin-bottom: 1rem;
  text-align: center;
}

/* ─── Google button ─── */
.google-btn {
  width: 100%;
  padding: 9px 14px;
  border: 1px solid #dde1e7;
  border-radius: 8px;
  background: #fff;
  color: #1a1a1a;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: background 0.15s;
  margin-bottom: 1rem;
}
.google-btn:hover { background: #f8f9fa; }
.google-btn img { width: 18px; height: 18px; }

/* ─── Divider ─── */
.divider {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 1rem;
}
.divider::before,
.divider::after {
  content: "";
  flex: 1;
  height: 1px;
  background: #e8eaed;
}
.divider span {
  font-size: 12px;
  color: #9ca3af;
}

/* ─── Form fields ─── */
.field {
  margin-bottom: 10px;
  text-align: left;
}
.field label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  color: #555;
  margin-bottom: 5px;
  letter-spacing: 0.01em;
}
form input[type="email"],
form input[type="password"],
form input[type="text"] {
  width: 100%;
  padding: 9px 12px;
  font-size: 14px;
  border: 1px solid #dde1e7;
  border-radius: 8px;
  background: #fff;
  color: #111;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
form input:focus {
  border-color: #aab4c4;
  box-shadow: 0 0 0 3px rgba(120,130,150,0.1);
}
form input::placeholder { color: #b0b8c4; }

/* ─── Password wrapper ─── */
.password-wrapper {
  position: relative;
}
.password-wrapper input { padding-right: 38px; }
.eye {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 16px;
  color: #9ca3af;
  line-height: 1;
  user-select: none;
  transition: color 0.15s;
}
.eye:hover { color: #555; }

/* ─── Forgot link ─── */
.forgot-link {
  display: block;
  text-align: right;
  font-size: 12px;
  color: #555;
  text-decoration: none;
  margin: 6px 0 14px;
  transition: color 0.15s;
}
.forgot-link:hover { color: #111; }

/* ─── Sign-in button ─── */
.signin-btn {
  width: 100%;
  padding: 10px;
  font-size: 14px;
  font-weight: 500;
  background: #111;
  color: #fff;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: opacity 0.15s, transform 0.1s;
  margin-bottom: 1rem;
}
.signin-btn:hover  { opacity: 0.82; }
.signin-btn:active { transform: scale(0.99); }

/* ─── Footer links ─── */
.signup-link,
.back-home-btn {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 13px;
  color: #6b7280;
  text-decoration: none;
  padding: 6px 10px;
  border-radius: 6px;
  transition: color 0.15s, background 0.15s;
}
.signup-link:hover,
.back-home-btn:hover {
  color: #111;
  background: #f4f6f9;
}

.back-home-btn {
  font-size: 12px;
  color: #9ca3af;
}
.back-home-btn:hover { transform: translateX(-2px); }

/* ─── Footer divider ─── */
.signin-box .footer {
  border-top: 1px solid #f0f2f5;
  padding-top: 1rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
</style>