<?php include 'includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4 fw-bold">Create an Account</h3>
                    
                    <!-- Role Selection Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4 justify-content-center p-1" style="background-color: var(--pill-bg); border-radius: 50px;" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-medium" id="pills-customer-tab" data-bs-toggle="pill" data-bs-target="#pills-customer" type="button" role="tab" aria-controls="pills-customer" aria-selected="true" style="color: var(--text-primary);">Customer</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-medium" id="pills-agency-tab" data-bs-toggle="pill" data-bs-target="#pills-agency" type="button" role="tab" aria-controls="pills-agency" aria-selected="false" style="color: var(--text-primary);">Agency</button>
                        </li>
                    </ul>

                    <div id="register-alert" class="alert d-none"></div>

                    <div class="tab-content" id="pills-tabContent">
                        <!-- Registration Form (Shared structure, dynamic role) -->
                        <form id="registerForm">
                            <input type="hidden" id="role" value="customer">
                            
                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Full Name / Agency Name</label>
                                <input type="text" class="form-control" id="name" required style="border-radius: 8px; padding: 12px;">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-medium">Username</label>
                                <input type="text" class="form-control" id="username" required style="border-radius: 8px; padding: 12px;">
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <input type="password" class="form-control" id="password" required style="border-radius: 8px; padding: 12px;">
                            </div>
                            
                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 custom-btn-dark fs-5">Register</button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted">Already have an account? <a href="login.php" class="text-dark fw-bold">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Handle tab switching styling and hidden input
document.addEventListener('DOMContentLoaded', function() {
    const customerTab = document.getElementById('pills-customer-tab');
    const agencyTab = document.getElementById('pills-agency-tab');
    const roleInput = document.getElementById('role');

    // Add explicit dark styling to active tab
    const setActiveStyle = (activeBtn, inactiveBtn) => {
        activeBtn.style.backgroundColor = 'var(--dark-btn-bg)';
        activeBtn.style.color = '#ffffff';
        inactiveBtn.style.backgroundColor = 'transparent';
        inactiveBtn.style.color = 'var(--text-primary)';
    };

    // Initial load
    setActiveStyle(customerTab, agencyTab);

    customerTab.addEventListener('shown.bs.tab', function (event) {
        roleInput.value = 'customer';
        setActiveStyle(customerTab, agencyTab);
    });
    
    agencyTab.addEventListener('shown.bs.tab', function (event) {
        roleInput.value = 'agency';
        setActiveStyle(agencyTab, customerTab);
    });
});

document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value;
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const role = document.getElementById('role').value;
    const alertBox = document.getElementById('register-alert');

    fetch('api/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ name: name, username: username, password: password, role: role })
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (res.status === 201) {
            alertBox.classList.add('alert-success');
            alertBox.innerText = 'Registration successful! Redirecting to login...';
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerText = res.body.message || 'Registration failed.';
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
