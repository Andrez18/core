<?php
$title = 'Finanzas - Melon Mind';
include 'views/layouts/header.php';

// Require auth
require_once 'controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAuth();
?>

<body>
    <!-- Sidebar -->
    <?php include 'views/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1>Finanzas</h1>
            <div class="header-actions">
                <button class="btn-secondary" onclick="generateReport()">
                    <i class="fas fa-file-pdf"></i>
                    Generar Reporte
                </button>
                <button class="btn-primary" onclick="openExpenseModal()">
                    <i class="fas fa-plus"></i>
                    Registrar Gasto
                </button>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Ingresos Totales</div>
                <div class="stat-value" style="color: #28a745;">$<?php echo number_format($income['total_income'] ?? 0, 2); ?></div>
                <small style="color: #6c757d;"><?php echo $income['completed_sessions'] ?? 0; ?> sesiones completadas</small>
            </div>
            <div class="stat-card">
                <div class="stat-label">Gastos Totales</div>
                <div class="stat-value" style="color: #dc3545;">$<?php echo number_format($stats['total_expenses'] ?? 0, 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Balance</div>
                <?php 
                $balance = ($income['total_income'] ?? 0) - ($stats['total_expenses'] ?? 0);
                $balanceColor = $balance >= 0 ? '#28a745' : '#dc3545';
                ?>
                <div class="stat-value" style="color: <?php echo $balanceColor; ?>;">$<?php echo number_format($balance, 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Promedio por Sesión</div>
                <?php 
                $avgPerSession = ($income['completed_sessions'] ?? 0) > 0 ? 
                    ($income['total_income'] ?? 0) / $income['completed_sessions'] : 0;
                ?>
                <div class="stat-value">$<?php echo number_format($avgPerSession, 2); ?></div>
            </div>
        </div>

        <!-- Charts Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <!-- Monthly Income vs Expenses Chart -->
            <div style="background: white; padding: 24px; border-radius: 8px; border: 1px solid #e9ecef;">
                <h3 style="margin-bottom: 16px; color: #212529;">Ingresos vs Gastos por Mes</h3>
                <canvas id="monthlyChart" width="400" height="200"></canvas>
            </div>

            <!-- Expenses by Category -->
            <div style="background: white; padding: 24px; border-radius: 8px; border: 1px solid #e9ecef;">
                <h3 style="margin-bottom: 16px; color: #212529;">Gastos por Categoría</h3>
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div style="background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #212529;">Gastos Recientes</h3>
                <div style="display: flex; gap: 12px;">
                    <select id="monthFilter" style="padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 14px;">
                        <option value="">Todos los meses</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="yearFilter" style="padding: 6px 12px; border: 1px solid #dee2e6; border-radius: 4px; font-size: 14px;">
                        <?php for ($year = date('Y'); $year >= date('Y') - 5; $year--): ?>
                            <option value="<?php echo $year; ?>" <?php echo $year == date('Y') ? 'selected' : ''; ?>><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div id="expensesList">
                <?php if (empty($expenses)): ?>
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay gastos registrados</p>
                        <button class="btn-primary" onclick="openExpenseModal()" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i>
                            Registrar Primer Gasto
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                        <div class="expense-item" style="padding: 16px 20px; border-bottom: 1px solid #f1f3f4; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 500; color: #212529; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($expense['description']); ?>
                                </div>
                                <div style="font-size: 14px; color: #6c757d;">
                                    <?php echo date('d M Y', strtotime($expense['expense_date'])); ?>
                                    <?php if ($expense['category']): ?>
                                        • <span style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 12px;"><?php echo htmlspecialchars($expense['category']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div style="font-weight: 600; color: #dc3545;">
                                -$<?php echo number_format($expense['amount'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div id="expenseModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Registrar Gasto</h2>
                <span class="close" onclick="closeExpenseModal()">&times;</span>
            </div>
            <form id="expenseForm" onsubmit="submitExpenseForm(event)">
                <div class="form-group">
                    <label for="description">Descripción *</label>
                    <input type="text" id="description" name="description" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="amount">Monto *</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Categoría</label>
                        <select id="category" name="category">
                            <option value="">Seleccionar categoría</option>
                            <option value="Oficina">Oficina</option>
                            <option value="Equipamiento">Equipamiento</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Formación">Formación</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Servicios">Servicios</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expense_date">Fecha del Gasto</label>
                    <input type="date" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeExpenseModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Registrar Gasto</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let monthlyChart, categoryChart;

        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            setupFilters();
        });

        function initializeCharts() {
            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyData = <?php echo json_encode($stats['monthly_expenses'] ?? []); ?>;
            
            monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => {
                        const date = new Date(2024, item.month - 1, 1);
                        return date.toLocaleDateString('es-ES', { month: 'short' });
                    }),
                    datasets: [{
                        label: 'Gastos',
                        data: monthlyData.map(item => item.total),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });

            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryData = <?php echo json_encode($stats['expenses_by_category'] ?? []); ?>;
            
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category || 'Sin categoría'),
                    datasets: [{
                        data: categoryData.map(item => item.total),
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40',
                            '#FF6384'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        function setupFilters() {
            document.getElementById('monthFilter').addEventListener('change', filterExpenses);
            document.getElementById('yearFilter').addEventListener('change', filterExpenses);
        }

        function filterExpenses() {
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            
            const params = new URLSearchParams({
                ajax: '1',
                month: month,
                year: year
            });

            fetch(`/finances?${params}`)
                .then(response => response.json())
                .then(data => {
                    updateExpensesList(data.expenses);
                    updateCharts(data.stats);
                })
                .catch(error => console.error('Error:', error));
        }

        function updateExpensesList(expenses) {
            const expensesList = document.getElementById('expensesList');
            
            if (expenses.length === 0) {
                expensesList.innerHTML = `
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay gastos para el período seleccionado</p>
                    </div>
                `;
                return;
            }

            let html = '';
            expenses.forEach(expense => {
                html += `
                    <div class="expense-item" style="padding: 16px 20px; border-bottom: 1px solid #f1f3f4; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 500; color: #212529; margin-bottom: 4px;">
                                ${expense.description}
                            </div>
                            <div style="font-size: 14px; color: #6c757d;">
                                ${formatDate(expense.expense_date)}
                                ${expense.category ? `• <span style="background: #e9ecef; padding: 2px 6px; border-radius: 3px; font-size: 12px;">${expense.category}</span>` : ''}
                            </div>
                        </div>
                        <div style="font-weight: 600; color: #dc3545;">
                            -$${parseFloat(expense.amount).toFixed(2)}
                        </div>
                    </div>
                `;
            });
            
            expensesList.innerHTML = html;
        }

        function updateCharts(stats) {
            // Update monthly chart
            if (monthlyChart && stats.monthly_expenses) {
                monthlyChart.data.labels = stats.monthly_expenses.map(item => {
                    const date = new Date(2024, item.month - 1, 1);
                    return date.toLocaleDateString('es-ES', { month: 'short' });
                });
                monthlyChart.data.datasets[0].data = stats.monthly_expenses.map(item => item.total);
                monthlyChart.update();
            }

            // Update category chart
            if (categoryChart && stats.expenses_by_category) {
                categoryChart.data.labels = stats.expenses_by_category.map(item => item.category || 'Sin categoría');
                categoryChart.data.datasets[0].data = stats.expenses_by_category.map(item => item.total);
                categoryChart.update();
            }
        }

        function openExpenseModal() {
            document.getElementById('expenseModal').style.display = 'block';
        }

        function closeExpenseModal() {
            document.getElementById('expenseModal').style.display = 'none';
            document.getElementById('expenseForm').reset();
        }

        function submitExpenseForm(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('ajax', '1');

            fetch('/finances/expense', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeExpenseModal();
                    showNotification(data.message, 'success');
                    // Reload page to update data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Error al registrar el gasto', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al registrar el gasto', 'error');
            });
        }

        function generateReport() {
            const year = document.getElementById('yearFilter').value;
            window.open(`/finances/report?year=${year}&format=pdf`, '_blank');
        }

        function exportToCSV() {
            const year = document.getElementById('yearFilter').value;
            window.location.href = `/finances/report?year=${year}&format=csv`;
        }
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>