<?php
class Car {
    private $conn;
    private $table_name = "cars";

    public $id;
    public $agency_id;
    public $model;
    public $vehicle_number;
    public $seating_capacity;
    public $rent_per_day;
    public $image_path;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                SET agency_id=:agency_id, model=:model, vehicle_number=:vehicle_number, seating_capacity=:seating_capacity, rent_per_day=:rent_per_day, image_path=:image_path";

        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->agency_id = htmlspecialchars(strip_tags($this->agency_id));
        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->vehicle_number = htmlspecialchars(strip_tags($this->vehicle_number));
        $this->seating_capacity = htmlspecialchars(strip_tags($this->seating_capacity));
        $this->rent_per_day = htmlspecialchars(strip_tags($this->rent_per_day));
        if ($this->image_path !== null) {
            $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        }

        // bind values
        $stmt->bindParam(":agency_id", $this->agency_id);
        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":vehicle_number", $this->vehicle_number);
        $stmt->bindParam(":seating_capacity", $this->seating_capacity);
        $stmt->bindParam(":rent_per_day", $this->rent_per_day);
        $stmt->bindParam(":image_path", $this->image_path);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch(PDOException $e) {
            // Might fail due to unique vehicle_number
            return false; 
        }
        return false;
    }

    public function readAll($search = "") {
        if (!empty($search)) {
            $query = "SELECT * FROM " . $this->table_name . " WHERE model LIKE :search OR vehicle_number LIKE :search ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
            $searchTerm = "%{$search}%";
            $stmt->bindParam(":search", $searchTerm);
        } else {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readByAgency($agency_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE agency_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $agency_id);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET model = :model, vehicle_number = :vehicle_number, seating_capacity = :seating_capacity, rent_per_day = :rent_per_day, image_path = :image_path
                WHERE id = :id AND agency_id = :agency_id";

        $stmt = $this->conn->prepare($query);

        $this->model = htmlspecialchars(strip_tags($this->model));
        $this->vehicle_number = htmlspecialchars(strip_tags($this->vehicle_number));
        $this->seating_capacity = htmlspecialchars(strip_tags($this->seating_capacity));
        $this->rent_per_day = htmlspecialchars(strip_tags($this->rent_per_day));
        if ($this->image_path !== null) {
            $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        }
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->agency_id = htmlspecialchars(strip_tags($this->agency_id));

        $stmt->bindParam(":model", $this->model);
        $stmt->bindParam(":vehicle_number", $this->vehicle_number);
        $stmt->bindParam(":seating_capacity", $this->seating_capacity);
        $stmt->bindParam(":rent_per_day", $this->rent_per_day);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":agency_id", $this->agency_id);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch(PDOException $e) {
            return false;
        }
        return false;
    }
}
?>
