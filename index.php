<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container mt-5">
        <h1 class="display-3 fw-bold mb-3 hero-title">Find and Rent Cars Near You</h1>
        <p class="lead text-muted mb-5 hero-subtitle">A platform connecting customers with trusted rental agencies for easy car bookings.</p>
        
        <div class="d-flex justify-content-center gap-3 mb-5">
            <a href="#fleet" class="btn btn-dark rounded-pill px-4 py-2 custom-btn-dark">Rent a Car</a>
            <a href="#fleet" class="btn btn-outline-dark rounded-pill px-4 py-2 custom-btn-outline">View Available Cars</a>
        </div>
    </div>
    
    <!-- Hero Image -->
    <div class="hero-image-container">
        <img src="assets/img/White_Car.jpg" alt="White Sports Car" class="img-fluid hero-car-img" style="mix-blend-mode: darken;">
    </div>
</section>

<!-- Available Cars Section -->
<section id="fleet" class="py-5 bg-light mt-5">
    <div class="container">
    <div class="container">
        <h2 class="text-center mb-5 fw-semibold" id="fleet-title">Available Cars to Rent</h2>
        
        <div class="row row-cols-1 row-cols-md-3 g-4" id="car-list">
            <!-- Cars will be dynamically loaded here by PHP/JS -->
            <div class="col w-100 text-center py-5">
                <div class="spinner-border text-dark" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="bookingModalLabel">Book Your Ride</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div id="booking-alert" class="alert d-none"></div>
        <form id="bookingForm">
          <input type="hidden" id="book_car_id" value="">
          
          <?php if(isset($_SESSION['user_id'])): ?>
              <div class="mb-3">
                  <label class="form-label fw-medium">Start Date</label>
                  <input type="date" class="form-control" id="start_date" required min="<?= date('Y-m-d') ?>">
              </div>
              <div class="mb-4">
                  <label class="form-label fw-medium">Number of Days</label>
                  <select class="form-select" id="days" required>
                      <option value="" selected disabled>Select duration</option>
                      <option value="1">1 Day</option>
                      <option value="2">2 Days</option>
                      <option value="3">3 Days</option>
                      <option value="5">5 Days</option>
                      <option value="7">1 Week</option>
                      <option value="14">2 Weeks</option>
                      <option value="30">1 Month</option>
                  </select>
              </div>
              <button type="submit" class="btn btn-dark w-100 rounded-pill custom-btn-dark py-2">Confirm Booking</button>
          <?php else: ?>
              <div class="alert alert-warning text-center">
                  You must be logged in to book a car.
              </div>
              <a href="login.php" class="btn btn-dark w-100 rounded-pill custom-btn-dark py-2">Login to Continue</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
const userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

document.addEventListener('DOMContentLoaded', function() {
    loadCars();
});

function loadCars() {
    fetch('api/get_available_cars.php')
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('car-list');
        list.innerHTML = '';
        
        if (data.length === 0) {
            list.innerHTML = '<div class="col w-100 text-center text-muted">No cars available right now.</div>';
            return;
        }

        data.forEach(car => {
            const imgSrc = car.image_path ? car.image_path : 'assets/img/White_Car.jpg';
            // Add data attributes for filtering
            list.innerHTML += `
            <div class="col car-item" data-model="${car.model.toLowerCase()}" data-number="${car.vehicle_number.toLowerCase()}">
                <div class="card h-100 shadow-sm border-0 car-card">
                    <img src="${imgSrc}" class="card-img-top" alt="Car Image" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">${car.model}</h5>
                        <p class="card-text text-muted mb-2"><i class="fa-solid fa-hashtag text-secondary me-2"></i> Num: ${car.vehicle_number}</p>
                        <p class="card-text text-muted mb-2"><i class="fa-solid fa-users text-secondary me-2"></i> Capacity: ${car.seating_capacity} Seater</p>
                        <p class="card-text text-primary mb-3 fw-semibold"><i class="fa-solid fa-tag text-secondary me-2"></i> $${car.rent_per_day} / day</p>
                        <button onclick="handleRentClick(${car.id})" class="btn btn-dark rounded-pill w-100">Rent Car</button>
                    </div>
                </div>
            </div>
            `;
        });
        
        // If there's an existing search value in the header input, apply it immediately
        const searchInput = document.getElementById('searchInput');
        if (searchInput && searchInput.value) {
            window.filterCarsFrontend(searchInput.value);
        }
    })
    .catch(err => {
        console.error('Error fetching cars:', err);
    });
}

function handleRentClick(carId) {
    if (!isLoggedIn) {
        window.location.href = 'login.php';
        return;
    }
    
    if (userRole === 'agency') {
        alert("Agencies are not allowed to rent cars.");
        return;
    }
    
    // Show modal for customer
    document.getElementById('book_car_id').value = carId;
    const bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    bookingModal.show();
}

window.filterCarsFrontend = function(query) {
    const q = query.toLowerCase().trim();
    const items = document.querySelectorAll('#car-list .car-item');
    let visibleCount = 0;
    
    // Auto-scroll to the fleet section if typing
    if (q.length > 0) {
        const fleetSection = document.getElementById('fleet');
        // Check if we are already scrolled past the top of the fleet section
        if (window.scrollY < fleetSection.offsetTop - 100) {
            fleetSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    items.forEach(item => {
        const model = item.getAttribute('data-model');
        const num = item.getAttribute('data-number');
        
        if (model.includes(q) || num.includes(q)) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    const title = document.getElementById('fleet-title');
    if (q) {
        title.innerText = "Search Results for '" + query + "'";
    } else {
        title.innerText = "Available Cars to Rent";
    }
    
    // Check if no results found message exists
    const noResultsMsg = document.getElementById('no-results-msg');
    if (visibleCount === 0 && items.length > 0) {
        if (!noResultsMsg) {
            const list = document.getElementById('car-list');
            list.innerHTML += '<div id="no-results-msg" class="col w-100 text-center text-muted py-4">No cars match your search.</div>';
        } else {
            noResultsMsg.style.display = '';
        }
    } else if (noResultsMsg) {
        noResultsMsg.style.display = 'none';
    }
}

document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const data = {
        car_id: document.getElementById('book_car_id').value,
        start_date: document.getElementById('start_date').value,
        days: document.getElementById('days').value
    };
    
    const alertBox = document.getElementById('booking-alert');

    fetch('api/book_car.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json().then(data => ({ status: response.status, body: data })))
    .then(res => {
        alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
        if (res.status === 201) {
            alertBox.classList.add('alert-success');
            alertBox.innerText = 'Booking confirmed successfully!';
            
            // Optionally close modal and reset after a delay
            setTimeout(() => {
                const modalEl = document.getElementById('bookingModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
                document.getElementById('bookingForm').reset();
                alertBox.classList.add('d-none');
            }, 2000);
        } else {
            alertBox.classList.add('alert-danger');
            alertBox.innerText = res.body.message || 'Failed to book car.';
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alertBox.classList.remove('d-none');
        alertBox.classList.add('alert-danger');
        alertBox.innerText = 'An error occurred. Please try again.';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
