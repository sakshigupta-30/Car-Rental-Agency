<?php 
include '../includes/header.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'agency') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Access Denied. Only agencies can view this page.</div></div>";
    include '../includes/footer.php';
    exit();
}
?>

<div class="container mt-5 mb-5" style="min-height: 50vh;">
    <h3 class="fw-bold mb-4">Bookings for Your Fleet</h3>
    
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 px-4 border-bottom-0 rounded-top-start">Booking ID</th>
                            <th scope="col" class="py-3 border-bottom-0">Customer Name</th>
                            <th scope="col" class="py-3 border-bottom-0">Automobile Model</th>
                            <th scope="col" class="py-3 border-bottom-0">Vehicle No.</th>
                            <th scope="col" class="py-3 border-bottom-0">Start Date</th>
                            <th scope="col" class="py-3 px-4 border-bottom-0 rounded-top-end">Days</th>
                        </tr>
                    </thead>
                    <tbody id="bookings-table-body">
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="spinner-border text-dark" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('../api/get_agency_bookings.php')
    .then(res => res.json())
    .then(data => {
        const tbody = document.getElementById('bookings-table-body');
        tbody.innerHTML = '';
        
        if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No bookings found for your cars yet.</td></tr>';
            return;
        }

        data.forEach(booking => {
            tbody.innerHTML += `
                <tr>
                    <td class="px-4 py-3 fw-medium">#${booking.id}</td>
                    <td class="py-3">
                        <div class="d-flex flex-column">
                            <span class="fw-semibold">${booking.customer_name}</span>
                            <span class="text-muted small">@${booking.customer_username}</span>
                        </div>
                    </td>
                    <td class="py-3">${booking.model}</td>
                    <td class="py-3"><span class="badge bg-light text-dark border">${booking.vehicle_number}</span></td>
                    <td class="py-3">${booking.start_date}</td>
                    <td class="px-4 py-3 fw-semibold">${booking.days} Day(s)</td>
                </tr>
            `;
        });
    })
    .catch(err => {
        console.error('Error fetching bookings:', err);
        document.getElementById('bookings-table-body').innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Failed to load bookings.</td></tr>';
    });
});
</script>

<?php include '../includes/footer.php'; ?>
