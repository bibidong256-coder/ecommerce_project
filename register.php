<?php
require "config/db.php";

$message = "";
$messageClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? 'New User';
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->execute([$email]);

    if ($checkEmail->rowCount() > 0) {
        $message = "This email is already registered.";
        $messageClass = "error-msg";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $email, $password])) {
            // Success! Redirect to login or show success message
            $message = "Registered successfully! You can now login.";
            $messageClass = "success-msg";
            // Optional: header("Location: login.php");
        } else {
            $message = "Something went wrong. Please try again.";
            $messageClass = "error-msg";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
</head>
<body>
<section>
    <div class="container">
        <div class="signup-box">
            <img src="shoes images/xxxxx/KSD Broken Face Logo Design.png" alt="Logo" class="logo" style="height: 44px; margin-bottom: 1.25rem;">
            <h2>Create a free account</h2>

            <?php if($message): ?>
                <div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <button class="google-btn">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo">
                Sign up with Google
            </button>

            <div class="divider"><span>or</span></div>

            <form id="signupForm" action="register.php" method="POST">
                <input type="text" name="name" placeholder="Your name (optional)" id="name">
                <input type="email" name="email" placeholder="Email address" id="email" required>
                
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Password" id="password" required>
                    <span id="togglePassword" class="eye">👁️</span>
                </div>
                
                <button type="submit" class="signup-btn">Sign up</button>
            </form>

            <p class="terms">By signing up, you agree to our <a href="terms.html">terms of use</a> and <a href="privacy-policy.html">privacy policy</a>.</p>

            <a href="login.php" class="signin-link">Already have an account</a> <br>
            <div class="back-home-container">
                <a href="index.php" class="back-home-btn">← Back to Home</a>
            </div>
        </div>
    </div>
</section>

<script>
    // Toggle password visibility
    const togglePassword = document.querySelector("#togglePassword");
    const passwordInput = document.querySelector("#password");

    togglePassword.addEventListener("click", () => {
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);
    });

    // IMPORTANT: I removed e.preventDefault() so the form actually submits to PHP!
</script>
</body>
</html>


<style>
        .error-msg { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
        .success-msg { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 10px; text-align: center; }
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

   section { width: 100%; }

   /* ─── Page wrapper ─── */
  .container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1.5rem 1rem;
  width: 100%;
  }

  /* ─── Card ─── */
  .signup-box {
  background: #fff;
  border: 1px solid #e8eaed;
  border-radius: 12px;
  padding: 2rem 2rem 1.5rem;
  width: 100%;
  max-width: 380px;
  text-align: center;
  }

  /* ─── Logo ─── */
   .  logo {
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

   /* ─── Alerts ─── */
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
  .success-msg {
  font-size: 13px;
  color: #166534;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 9px 12px;
  margin-bottom: 1rem;
  text-align: center;
  }

   /  * ─── Google button ─── */
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
  .divider span { font-size: 12px; color: #9ca3af; }

   /* ─── Form fields ─── */
   #signupForm input {
  width: 100%;
  padding: 9px 12px;
  font-size: 14px;
  border: 1px solid #dde1e7;
  border-radius: 8px;
  background: #fff;
  color: #111;
  outline: none;
  margin-bottom: 10px;
  transition: border-color 0.15s, box-shadow 0.15s;
  text-align: left;
  }
  #signupForm input:focus {
  border-color: #aab4c4;
  box-shadow: 0 0 0 3px rgba(120,130,150,0.1);
 }
 #signupForm input::placeholder { color: #b0b8c4; }

 /* ─── Password wrapper ─── */
 .password-wrapper { position: relative; }
  .password-wrapper input { padding-right: 38px; margin-bottom: 0; }
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

  /* ─── Sign-up button ─── */
  .signup-btn {
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
  margin: 10px 0 0.75rem;
 }
  .signup-btn:hover  { opacity: 0.82; }
  .signup-btn:active { transform: scale(0.99); }

 /* ─── Terms ─── */
  .terms {
  font-size: 11px;
  color: #9ca3af;
  line-height: 1.6;
  margin: 0 0 1rem;
   }
  .terms a {
  color: #6b7280;
  text-decoration: underline;
  text-underline-offset: 2px;
  }
  .terms a:hover { color: #111; }

   /* ─── Footer links ─── */
  .signin-link {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  color: #6b7280;
  text-decoration: none;
  padding: 5px 8px;
  border-radius: 6px;
  transition: color 0.15s, background 0.15s;
   }
  .signin-link:hover {
  color: #111;
  background: #f4f6f9;
    }

  .back-home-container { margin-top: 8px; }

   .back-home-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #9ca3af;
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 6px;
  transition: color 0.15s, transform 0.15s;
  }
  .back-home-btn:hover {
  color: #6b7280;
  transform: translateX(-2px);
  }

   /* ─── Separate footer section ─── */
  .signup-box > .signin-link,
  .signup-box > br,
  .back-home-container {
  display: block;
  text-align: center;
  
 .signup-box > hr,
 .signup-footer {
  border: none;
  border-top: 1px solid #f0f2f5;
  margin: 0.75rem 0 1rem;
 }
</style>
