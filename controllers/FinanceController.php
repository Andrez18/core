<?php
require_once 'models/Expense.php';

class FinanceController {
    private $expense;

    public function __construct() {
        $this->expense = new Expense();
    }

    // Mostrar dashboard financiero
    public function index() {
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? null;
        
        $stats = $this->expense->getFinancialStats($year);
        $income = $this->expense->getIncomeStats($year);
        $expenses = $this->expense->getAll($month, $year);
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'stats' => $stats,
                'income' => $income,
                'expenses' => $expenses
            ]);
            return;
        }
        
        include 'views/finances/index.php';
    }

    // Crear gasto
    public function createExpense() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->expense->description = $_POST['description'] ?? '';
            $this->expense->amount = $_POST['amount'] ?? 0;
            $this->expense->category = $_POST['category'] ?? '';
            $this->expense->expense_date = $_POST['expense_date'] ?? date('Y-m-d');
            
            if ($this->expense->create()) {
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Gasto registrado exitosamente']);
                    return;
                }
                header('Location: /finances');
                return;
            } else {
                $error = 'Error al registrar el gasto';
            }
        }
        
        include 'views/finances/create_expense.php';
    }

    // Generar reporte
    public function generateReport() {
        $year = $_GET['year'] ?? date('Y');
        $format = $_GET['format'] ?? 'html';
        
        $stats = $this->expense->getFinancialStats($year);
        $income = $this->expense->getIncomeStats($year);
        
        if ($format === 'pdf') {
            $this->generatePDFReport($stats, $income, $year);
        } elseif ($format === 'csv') {
            $this->generateCSVReport($stats, $income, $year);
        } else {
            include 'views/finances/report.php';
        }
    }

    private function generatePDFReport($stats, $income, $year) {
        // Aquí implementarías la generación de PDF
        // Por simplicidad, devolvemos HTML que se puede imprimir
        header('Content-Type: text/html');
        include 'views/finances/pdf_report.php';
    }

    private function generateCSVReport($stats, $income, $year) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reporte_financiero_' . $year . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['Concepto', 'Monto']);
        
        // Income
        fputcsv($output, ['Ingresos Totales', $income['total_income'] ?? 0]);
        fputcsv($output, ['Sesiones Completadas', $income['completed_sessions'] ?? 0]);
        
        // Expenses
        fputcsv($output, ['Gastos Totales', $stats['total_expenses']]);
        
        // Monthly expenses
        fputcsv($output, ['']);
        fputcsv($output, ['Gastos por Mes']);
        foreach ($stats['monthly_expenses'] as $expense) {
            $monthName = date('F', mktime(0, 0, 0, $expense['month'], 1));
            fputcsv($output, [$monthName, $expense['total']]);
        }
        
        fclose($output);
    }
}
?>