<?php 
include 'includes/header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Access Denied. Only customers can view this page.</div></div>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="container mt-5 mb-5" style="min-height: 50vh;">
    <h3 class="fw-bold mb-4">My Bookings</h3>
    
    <div class="row row-cols-1 row-cols-md-2 g-4" id="my-bookings-list">
        <div class="col w-100 text-center py-5">
            <div class="spinner-border text-dark" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/get_customer_bookings.php')
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById('my-bookings-list');
        list.innerHTML = '';
        
        if (data.length === 0) {
            list.innerHTML = '<div class="col w-100 text-center text-muted py-4">You have not booked any cars yet.</div>';
            return;
        }

        data.forEach(booking => {
            const imgSrc = booking.image_path ? booking.image_path : 'assets/img/White_Car.jpg';
            list.innerHTML += `
            <div class="col">
                <div class="card h-100 shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="row g-0 h-100">
                        <div class="col-md-4">
                            <img src="${imgSrc}" class="img-fluid h-100 w-100" alt="${booking.model}" style="object-fit: cover; min-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body d-flex flex-column justify-content-between h-100">
                                <div>
                                    <h5 class="card-title fw-bold mb-1">${booking.model}</h5>
                                    <p class="text-muted small mb-2">Vehicle No: <span class="badge bg-light text-dark border">${booking.vehicle_number}</span></p>
                                    <p class="text-muted small mb-1"><i class="fa-solid fa-building me-2 text-secondary"></i> Agency: ${booking.agency_name}</p>
                                    <p class="text-muted small mb-1"><i class="fa-regular fa-calendar me-2 text-secondary"></i> Start Date: ${booking.start_date}</p>
                                    <p class="text-muted small mb-1"><i class="fa-solid fa-clock me-2 text-secondary"></i> Duration: ${booking.days} Day(s)</p>
                                    <p class="text-muted small mb-0"><i class="fa-solid fa-dollar-sign me-2 text-secondary"></i> Rate: $${booking.rent_per_day} / day</p>
                                </div>
                                <div class="mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Total Amount</span>
                                        <span class="fw-bold text-success fs-5">$${parseFloat(booking.total_amount).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
    })
    .catch(err => {
        console.error('Error fetching bookings:', err);
        document.getElementById('my-bookings-list').innerHTML = '<div class="col w-100 text-center text-danger py-4">Failed to load bookings.</div>';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
