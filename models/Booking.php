<?php
class Booking {
    private $conn;
    private $table_name = "bookings";

    public $id;
    public $car_id;
    public $customer_id;
    public $start_date;
    public $days;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET car_id=:car_id, customer_id=:customer_id, start_date=:start_date, days=:days";

        $stmt = $this->conn->prepare($query);

        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->customer_id = htmlspecialchars(strip_tags($this->customer_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->days = htmlspecialchars(strip_tags($this->days));

        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":customer_id", $this->customer_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":days", $this->days);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readByAgency($agency_id) {
        $query = "SELECT b.id, b.start_date, b.days, c.model, c.vehicle_number, u.name as customer_name, u.username as customer_username
                  FROM " . $this->table_name . " b
                  JOIN cars c ON b.car_id = c.id
                  JOIN users u ON b.customer_id = u.id
                  WHERE c.agency_id = ?
                  ORDER BY b.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $agency_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
