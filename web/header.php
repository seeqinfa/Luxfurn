<?php

if (isset($_SERVER['SCRIPT_FILENAME']) && realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    header('Location: /FYP-25-S2-34-Chatbot/Src/Boundary/index.php');
    exit;
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$flash = $_SESSION['message'] ?? null;
if ($flash !== null) { unset($_SESSION['message']); }
require_once dirname(__DIR__) . '/src/config.php';

/* -------------------------------------------------------------------
   Backfill session with user_id (and role) from DB when username exists
   Assumes: $conn is a mysqli connection and users table has (id, username, role)
------------------------------------------------------------------- */
if (!empty($_SESSION['username']) && empty($_SESSION['user_id'])) {
    if (isset($conn) && $conn instanceof mysqli) {
        if ($stmt = $conn->prepare("SELECT id, role FROM users WHERE username = ? LIMIT 1")) {
            $stmt->bind_param('s', $_SESSION['username']);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    $_SESSION['user_id'] = (int)$row['id'];
                    if (empty($_SESSION['role']) && isset($row['role'])) {
                        $_SESSION['role'] = $row['role'];
                    }
                    // Ensure login flag is aligned
                    if (empty($_SESSION['is_logged_in'])) {
                        $_SESSION['is_logged_in'] = true;
                    }
                } else {
                    // Username not found in DB → clear inconsistent session bits
                    unset($_SESSION['user_id'], $_SESSION['role'], $_SESSION['is_logged_in']);
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LuxFurn</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="<?= CSS_PATH ?>/style.css" />
	<link rel="stylesheet" href="<?= CSS_PATH ?>/chatbot.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Internal Styles -->
    <style>
        /* General UI */
        .wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(100px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transform: scale(0);
            transition: transform 0.3s ease;
        }

        .wrapper.active-popup {
            transform: scale(1);
        }

        .wrapper .form-box {
            width: 400px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .icon-close {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 30px;
            height: 30px;
            background: black;
            color: white;
            font-size: 1.2rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
        }

        /* Navigation */
        .nav-dropdown {
            position: relative;
            display: inline-block;
        }

        .nav-link {
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            right: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            border-radius: 6px;
        }

        .nav-dropdown-content a {
            color: black;
            padding: 12px 16px;
            display: block;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.2s ease;
            margin-left: 0 !important;
        }

        .nav-dropdown-content a:hover {
            background-color: #e67e22;
            color: white;
        }

        .nav-dropdown:hover .nav-dropdown-content {
            display: block;
        }

        /* Put this AFTER your existing nav CSS (ideally in header.php after the current styles) */

        /* Keep dropdown panel solid white above everything */
        header .navigation .nav-dropdown-content {
        background: #fff;
        z-index: 1000;
        }

        /* Stronger selector so it beats `.navigation a { color:white; }` */
        header .navigation .nav-dropdown-content a {
        color: #000;               /* default black text */
        background: #fff;          /* no transparency */
        padding: 12px 16px;
        display: block;
        text-decoration: none;
        font-size: 14px;
        margin-left: 0;            /* don’t inherit nav spacing */
        }

        /* Remove the animated underline from dropdown items */
        header .navigation .nav-dropdown-content a::after {
        content: none !important;
        }

        /* Hover state: orange bg, white text */
        header .navigation .nav-dropdown-content a:hover {
        background: #e67e22;
        color: #fff;
        }

        /* Logo */
        .logo-img {
            height: 40px;
            width: auto;
        }

        .logo-text {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }

        .logo-link {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            color: inherit;
        }
		
		.chat-buttons {
			margin-top: 5px;
		}
		.chat-button {
			margin: 3px;
			padding: 6px 12px;
			border: none;
			background-color: #e28743;
			color: white;
			border-radius: 4px;
			cursor: pointer;
		}
		.chat-button:hover {
			background-color: #0056b3;
		}
	  /* Centered flash overlay */
	  .flash-overlay{
		position:fixed; inset:0;
		display:flex; align-items:center; justify-content:center;
		background:rgba(0,0,0,.35);
		z-index:10000; /* above nav/modal */
	  }
	  .flash-box{
		position:relative;
		background:#fdecea; color:#721c24;
		border:1px solid #f5c6cb; border-radius:10px;
		padding:16px 44px 16px 16px;
		width:min(520px,90vw); max-width:90vw;
		text-align:center; box-shadow:0 10px 30px rgba(0,0,0,.2);
		font-size:14px;
	  }
	  .flash-box .flash-close{
		position:absolute; right:8px; top:6px;
		background:transparent; border:0; font-size:20px;
		cursor:pointer; line-height:1;
	  }

    </style>
</head>
<body>
<?php if (!empty($flash)): ?>
  <div class="flash-overlay" id="flashOverlay" role="dialog" aria-modal="true" aria-live="polite">
    <div class="flash-box">
      <?= htmlspecialchars($flash) ?>
      <button type="button" class="flash-close" aria-label="Close">×</button>
    </div>
  </div>
<?php endif; ?>

<header>
    <div class="logo-container">
        <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/index.php" class="logo-link">
            <img src="/FYP-25-S2-34-Chatbot/Src/img/logo.png" alt="LuxFurn" class="logo-img" />
            <h2 class="logo-text">LuxFurn</h2>
        </a>
    </div>
   <nav class="navigation">
    <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/aboutpageUI.php">About</a>
    <div class="nav-dropdown">
        <a href="#" class="nav-link">Furniture ▾</a>
        <div class="nav-dropdown-content">
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/viewFurnitureUI.php">View Furniture</a>
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/CustomerInstructionmanualUI.php">Instruction Manuals</a>
        </div>
    </div>
    <div class="nav-dropdown">
        <a href="#" class="nav-link">My Orders ▾</a>
        <div class="nav-dropdown-content">
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/CartUI.php">View Cart</a>
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/viewOrderUI.php">View Order</a>
        </div>
    </div>
    <div class="nav-dropdown">
        <a href="#" class="nav-link">Tickets ▾</a>
        <div class="nav-dropdown-content">
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/Customer/CustomerSupportTicketsUI.php">My Tickets</a>
        </div>
    </div>
    <?php if (!empty($_SESSION['is_logged_in'])): ?>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/FYP-25-S2-34-Chatbot/Src/Boundary/admin/adminDashboardUI.php">Admin Dashboard</a>
        <?php endif; ?>
        <span style="color:white; margin-left:20px;">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="<?= CONTROLLERS_URL ?>/logoutController.php">Logout</a>
    <?php else: ?>
        <button class="btnLogin">Login</button>
    <?php endif; ?>
</nav>

</header>

<!-- Login/Register Modal -->
<div class="wrapper" id="loginWrapper">
    <span class="icon-close" id="closeLogin">&times;</span>

    <!-- Login Form -->
    <div class="form-box login">
        <h2>Login</h2>
        <form action="<?= CONTROLLERS_URL ?>/loginController.php" method="POST">
            <div class="input-box">
                <span class="icon"><ion-icon name="person"></ion-icon></span>
                <input type="text" name="username" required />
                <label>Username</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                <input type="password" name="password" required />
                <label>Password</label>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" /> Remember me</label>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
            <div class="login-register">
                <p>Don't have an account? <a href="#" class="register-link">Register</a></p>
            </div>
        </form>
    </div>

    <!-- Register Form -->
    <div class="form-box register" style="display: none;">
        <h2>Register</h2>
        <form action="<?= CONTROLLERS_URL ?>/registerController.php" method="POST">
            <div class="input-box">
                <span class="icon"><ion-icon name="person"></ion-icon></span>
                <input type="text" name="username" required />
                <label>Username</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail"></ion-icon></span>
                <input type="email" name="email" required />
                <label>Email</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed"></ion-icon></span>
                <input type="password" name="password" required />
                <label>Password</label>
            </div>
            <div class="remember-forgot">
                <label><input type="checkbox" required /> I agree to the terms & conditions</label>
            </div>
            <button type="submit" name="register" class="btn">Register</button>
            <div class="login-register">
                <p>Already have an account? <a href="#" class="login-link">Login</a></p>
            </div>
        </form>
    </div>
</div>

<!-- Chatbot (only for logged-in users) -->
<?php if (!empty($_SESSION['is_logged_in'])): ?>
    <div id="chatbot-container">
        <div id="chatbot-header">LuxBot</div>
        <div id="chatbot-body">
            <div id="chat-log"></div>
            <form id="chat-form">
                <input type="text" id="chat-input" placeholder="Ask me something..." autocomplete="off" />
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
<?php endif; ?>

</body>
<!-- External Scripts -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- Internal Scripts -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    /* ==========  LOGIN / REGISTER POP-UP  ========== */
    const loginBtn      = document.querySelector(".btnLogin");
    const wrapper       = document.getElementById("loginWrapper");
    const closeBtn      = document.getElementById("closeLogin");
    const registerLink  = document.querySelector(".register-link");
    const loginLink     = document.querySelector(".login-link");
    const loginForm     = document.querySelector(".form-box.login");
    const registerForm  = document.querySelector(".form-box.register");
	const flashOverlay = document.getElementById('flashOverlay');
	if (flashOverlay) {
	  const closeBtn = flashOverlay.querySelector('.flash-close');
	  function closeFlash(){ flashOverlay.remove(); }

	  closeBtn && closeBtn.addEventListener('click', function(e){ e.preventDefault(); closeFlash(); });
	  flashOverlay.addEventListener('click', function(e){ if (e.target === flashOverlay) closeFlash(); });
	  document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeFlash(); }, { once:true });
	}
	
    loginBtn?.addEventListener("click", () => {
        document.body.style.overflow = "hidden";
        wrapper.classList.add("active-popup");
        loginForm.style.display = "block";
        registerForm.style.display = "none";
    });
    closeBtn?.addEventListener("click", () => {
        document.body.style.overflow = "";
        wrapper.classList.remove("active-popup");
    });
    registerLink?.addEventListener("click", e => {
        e.preventDefault();
        loginForm.style.display    = "none";
        registerForm.style.display = "block";
    });
    loginLink?.addEventListener("click", e => {
        e.preventDefault();
        loginForm.style.display    = "block";
        registerForm.style.display = "none";
    });

    /* ==========  CHAT (run only when logged in)  ========== */
    <?php if (!empty($_SESSION['is_logged_in'])): ?>
    const form = document.getElementById("chat-form");
    const input = document.getElementById("chat-input");
    const log   = document.getElementById("chat-log");
    if (!form || !input || !log) return;          // page without chatbot

    /* ---- 1. Load saved history from PHP ---- */
    fetch("/FYP-25-S2-34-Chatbot/Src/Controllers/chatHistoryController.php")
        .then(r => r.json())
        .then(history => {
            history.forEach(row => addBubble(row.sender, row.message_text));
            scrollToBottom();
        })
        .catch(err => console.error("history error:", err));

    /* ---- 2. Send new message → Rasa → save in PHP ---- */
    form.addEventListener("submit", async e => {
        e.preventDefault();
        const userText = input.value.trim();
        if (!userText) return;

        addBubble("user", userText);
        input.value = "";

        /* 2a. Ask Rasa for reply */
        let botReplies = [];
        try {
            const rasaRes = await fetch("http://localhost:5005/webhooks/rest/webhook", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    sender: "<?=$_SESSION['user_id']?>|<?=$_SESSION['username']?>",
                    message: userText
                })
            });
            botReplies = await rasaRes.json();   // array
        } catch (err) {
            console.error("Rasa error:", err);
            addBubble("bot", "Sorry, I'm having trouble connecting to the server.");
            scrollToBottom();
            return;
        }

        /* 2b. Show Rasa replies in bubbles */
        if (botReplies.length === 0) {
            addBubble("bot", "…");
        } else {
            botReplies.forEach(r => {
				const text = r.text || "";
				const buttons = r.buttons || [];
				addBubble("bot", text, buttons);
			});
        }
        scrollToBottom();

        /* 2c. Save both user & bot lines via PHP controller */
        const botText = botReplies.map(r => r.text).filter(Boolean).join(" || ");

        fetch("/FYP-25-S2-34-Chatbot/Src/Controllers/chatbotController.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "user=" + encodeURIComponent(userText) +
                  "&bot=" + encodeURIComponent(botText)
        }).catch(err => console.error("save error:", err));
    });
	/* ---- CHATBOT BUTTONS ---- */
	log.addEventListener("click", async e => {
		if (!e.target.classList.contains("chat-button")) return;

		const payload = e.target.getAttribute("data-payload");
		if (!payload) return;

		addBubble("user", e.target.textContent);

		try {
			const rasaRes = await fetch("http://localhost:5005/webhooks/rest/webhook", {
				method: "POST",
				headers: { "Content-Type": "application/json" },
				body: JSON.stringify({
					sender: "<?=$_SESSION['user_id']?>|<?=$_SESSION['username']?>",
					message: payload
				})
			});
			const botReplies = await rasaRes.json();
			botReplies.forEach(r => {
				const text = r.text || "";
				const buttons = r.buttons || [];
				addBubble("bot", text, buttons);
			});
			scrollToBottom();
		} catch (err) {
			console.error("Rasa error:", err);
			addBubble("bot", "Sorry, I'm having trouble.");
		}
	});
    /* ---- Helper functions ---- */
    function addBubble(sender, text, buttons = []) {
        const label = sender === "user" ? "You" : "LuxBot";
        const cls   = sender === "user" ? "user-message" : "bot-message";
        
		// Create base message
		let html = `<div class="chat-message ${cls}"><strong>${label}:</strong> ${text}`;
		
		// Append buttons if they exist
		if (buttons.length > 0) {
			html += `<div class="chat-buttons">`;
			buttons.forEach(btn => {
				html += `<button class="chat-button" data-payload="${btn.payload}">${btn.title}</button>`;
			});
			html += `</div>`;
		}

		html += `</div>`;
		log.insertAdjacentHTML("beforeend", html);
    }
    function scrollToBottom() { log.scrollTop = log.scrollHeight; }
    <?php endif; ?>
});
</script>
