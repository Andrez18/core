<?php
require_once 'config/database.php';

class Expense {
    private $conn;
    private $table_name = "expenses";

    public $id;
    public $description;
    public $amount;
    public $category;
    public $expense_date;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los gastos
    public function getAll($month = null, $year = null, $category = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if ($month && $year) {
            $query .= " AND MONTH(expense_date) = :month AND YEAR(expense_date) = :year";
        }
        
        if ($category) {
            $query .= " AND category = :category";
        }
        
        $query .= " ORDER BY expense_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($month && $year) {
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year);
        }
        
        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear gasto
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (description, amount, category, expense_date) 
                  VALUES 
                  (:description, :amount, :category, :expense_date)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':expense_date', $this->expense_date);
        
        return $stmt->execute();
    }

    // Obtener estadísticas financieras
    public function getFinancialStats($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $stats = [];
        
        // Gastos totales del año
        $query = "SELECT SUM(amount) as total_expenses 
                  FROM " . $this->table_name . " 
                  WHERE YEAR(expense_date) = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $stats['total_expenses'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_expenses'] ?? 0;
        
        // Gastos por mes
        $query = "SELECT MONTH(expense_date) as month, SUM(amount) as total 
                  FROM " . $this->table_name . " 
                  WHERE YEAR(expense_date) = :year 
                  GROUP BY MONTH(expense_date) 
                  ORDER BY month";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $stats['monthly_expenses'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Gastos por categoría
        $query = "SELECT category, SUM(amount) as total 
                  FROM " . $this->table_name . " 
                  WHERE YEAR(expense_date) = :year 
                  GROUP BY category 
                  ORDER BY total DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        $stats['expenses_by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $stats;
    }

    // Obtener ingresos (basado en citas completadas)
    public function getIncomeStats($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $query = "SELECT 
                    SUM(p.price_per_session) as total_income,
                    COUNT(a.id) as completed_sessions
                  FROM appointments a
                  JOIN patients p ON a.patient_id = p.id
                  WHERE a.status = 'Completada' 
                  AND YEAR(a.appointment_date) = :year";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>