<?php include 'includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4 fw-bold">Welcome Back</h3>
                    <div id="login-alert" class="alert d-none"></div>
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label fw-medium">Username</label>
                            <input type="text" class="form-control" id="username" required style="border-radius: 8px; padding: 12px;">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <input type="password" class="form-control" id="password" required style="border-radius: 8px; padding: 12px;">
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 custom-btn-dark fs-5">Login</button>
                    </form>
                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account? <a href="register.php" class="text-dark fw-bold">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const alertBox = document.getElementById('login-alert');

    fetch('api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username: username, password: password })
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (res.status === 200) {
            alertBox.classList.add('alert-success');
            alertBox.innerText = 'Login successful! Redirecting...';
            setTimeout(() => {
                window.location.href = 'index.php'; // Or redirect based on role later
            }, 1000);
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerText = res.body.message || 'Login failed.';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerText = 'An error occurred. Please try again.';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
