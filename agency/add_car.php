<?php 
// Ensure session is started within header
include '../includes/header.php'; 

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agency') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Access Denied. Only agencies can view this page.</div></div>";
    include '../includes/footer.php';
    exit();
}
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Add New Car</h4>
                    <div id="add-car-alert" class="alert d-none"></div>
                    
                    <form id="addCarForm">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Vehicle Model</label>
                            <input type="text" class="form-control" id="model" required placeholder="e.g. Porsche 911 GT3">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Vehicle Number</label>
                            <input type="text" class="form-control" id="vehicle_number" required placeholder="e.g. AB12CD3456">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Seating Capacity</label>
                            <input type="number" class="form-control" id="seating_capacity" required min="1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Rent per Day ($)</label>
                            <input type="number" class="form-control" id="rent_per_day" required min="1" step="0.01">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-medium">Car Image (Optional)</label>
                            <input class="form-control" type="file" id="car_image" accept="image/jpeg, image/png, image/webp">
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill custom-btn-dark py-2">Add Car</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <h4 class="fw-bold mb-4">Your Fleet</h4>
            <div class="row row-cols-1 row-cols-md-2 g-4" id="agency-cars-list">
                <!-- Cars loaded dynamically -->
                <div class="col w-100 text-center py-5">
                    <div class="spinner-border text-dark" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('addCarForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('model', document.getElementById('model').value);
    formData.append('vehicle_number', document.getElementById('vehicle_number').value);
    formData.append('seating_capacity', document.getElementById('seating_capacity').value);
    formData.append('rent_per_day', document.getElementById('rent_per_day').value);
    
    const imageInput = document.getElementById('car_image');
    if (imageInput.files.length > 0) {
        formData.append('car_image', imageInput.files[0]);
    }
    
    const alertBox = document.getElementById('add-car-alert');

    fetch('../api/add_car.php', {
        method: 'POST',
        body: formData // Fetch automatically sets the multipart/form-data boundary
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (res.status === 201) {
            alertBox.classList.add('alert-success');
            alertBox.innerText = 'Car added successfully!';
            document.getElementById('addCarForm').reset();
            loadAgencyCars(); // Refresh list
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerText = res.body.message || 'Failed to add car.';
        }
    });
});

function loadAgencyCars() {
    fetch('../api/get_agency_cars.php')
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('agency-cars-list');
        list.innerHTML = '';
        if(data.length === 0) {
            list.innerHTML = '<div class="col w-100 text-center text-muted">No cars added yet.</div>';
            return;
        }
        
        data.forEach(car => {
            const imgSrc = car.image_path ? '../' + car.image_path : '../assets/img/White_Car.jpg';
            list.innerHTML += `
            <div class="col">
                <div class="card h-100 shadow-sm border-0 car-card">
                    <img src="${imgSrc}" class="card-img-top" alt="${car.model}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${car.model}</h5>
                        <p class="card-text text-muted mb-1"><small>Vehicle No: ${car.vehicle_number}</small></p>
                        <p class="card-text text-muted mb-1"><small>Capacity: ${car.seating_capacity} Seater</small></p>
                        <p class="card-text text-primary mb-3 fw-semibold"><small>$${car.rent_per_day} / day</small></p>
                        <!-- Edit feature can be added here -->
                        <button class="btn btn-outline-dark btn-sm rounded-pill w-100">Edit Details</button>
                    </div>
                </div>
            </div>
            `;
        });
    });
}

// Initial load
document.addEventListener('DOMContentLoaded', loadAgencyCars);
</script>

<?php include '../includes/footer.php'; ?>
