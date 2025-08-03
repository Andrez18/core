<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Melon Mind'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 70px;
            height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            padding: 0;
            overflow: visible;
            transition: width 0.3s ease;
            position: relative;
            z-index: 1000;
            flex-shrink: 0;
        }

        .sidebar:hover {
            width: 250px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 20px 16px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 8px;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-header i {
            font-size: 18px;
            color: #6c757d;
            margin-right: 8px;
            min-width: 20px;
        }

        .sidebar-header span {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .sidebar:hover .sidebar-header span {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 8px;
        }

        .nav-list {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 2px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            text-decoration: none;
            color: #6c757d;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            position: relative;
        }

        .nav-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .nav-link.active {
            background-color: #212529;
            color: white;
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
            min-width: 20px;
            text-align: center;
        }

        .nav-link span {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .sidebar:hover .nav-link span {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-section {
            padding: 16px 8px;
            border-top: 1px solid #e9ecef;
        }

        .section-title {
            font-size: 12px;
            font-weight: 600;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            padding: 0 16px;
            white-space: nowrap;
            overflow: hidden;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .sidebar:hover .section-title {
            opacity: 1;
            transform: translateX(0);
        }

        .options-list {
            list-style: none;
        }

        .option-item {
            margin-bottom: 2px;
        }

        .option-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            text-decoration: none;
            color: #6c757d;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
        }

        .option-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .option-link i {
            width: 18px;
            margin-right: 12px;
            font-size: 14px;
            min-width: 18px;
            text-align: center;
        }

        .option-link span {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .sidebar:hover .option-link span {
            opacity: 1;
            transform: translateX(0);
        }

        .sidebar-bottom {
            padding: 16px 8px;
            border-top: 1px solid #e9ecef;
            margin-top: auto;
        }

        .bottom-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            text-decoration: none;
            color: #6c757d;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
        }

        .bottom-link:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .bottom-link i {
            width: 18px;
            margin-right: 12px;
            font-size: 14px;
            min-width: 18px;
            text-align: center;
        }

        .bottom-link span {
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .sidebar:hover .bottom-link span {
            opacity: 1;
            transform: translateX(0);
        }

        .pro-link {
            font-weight: 500;
        }

        .pro-link i {
            color: #fd7e14;
        }

        /* Tooltips */
        .nav-link::after,
        .option-link::after,
        .bottom-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: #212529;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 1001;
        }

        .nav-link::before,
        .option-link::before,
        .bottom-link::before {
            content: '';
            position: absolute;
            left: 65px;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #212529;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
            z-index: 1001;
        }

        .sidebar:not(:hover) .nav-link:hover::after,
        .sidebar:not(:hover) .option-link:hover::after,
        .sidebar:not(:hover) .bottom-link:hover::after,
        .sidebar:not(:hover) .nav-link:hover::before,
        .sidebar:not(:hover) .option-link:hover::before,
        .sidebar:not(:hover) .bottom-link:hover::before {
            opacity: 1;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            background-color: #ffffff;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .content-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #212529;
        }

        .header-actions {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container i {
            position: absolute;
            left: 12px;
            color: #6c757d;
            font-size: 14px;
        }

        .search-input {
            padding: 8px 12px 8px 36px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 14px;
            width: 200px;
            background-color: #f8f9fa;
        }

        .search-input:focus {
            outline: none;
            border-color: #0d6efd;
            background-color: white;
        }

        .btn-primary {
            background-color: #212529;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #495057;
        }

        .btn-secondary {
            background-color: #f8f9fa;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-secondary:hover {
            background-color: #e9ecef;
        }

        .btn-icon {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .btn-icon:hover {
            background-color: #f8f9fa;
        }

        .btn-primary-small {
            background-color: #212529;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            text-align: center;
        }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 600;
            color: #212529;
        }

        /* Filters */
        .filters-container {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
            align-items: center;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-group label {
            font-size: 14px;
            color: #495057;
            font-weight: 500;
        }

        .filter-select {
            padding: 6px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: #0d6efd;
        }

        /* Patient List */
        .patient-list {
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .patient-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f3f4;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .patient-item:last-child {
            border-bottom: none;
        }

        .patient-item:hover {
            background-color: #f8f9fa;
        }

        .patient-item.active {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
        }

        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            color: #6c757d;
        }

        .patient-info {
            flex: 1;
        }

        .patient-name {
            font-size: 16px;
            font-weight: 500;
            color: #212529;
            margin-bottom: 4px;
        }

        .patient-status {
            font-size: 14px;
            color: #28a745;
            font-weight: 500;
        }

        .patient-status.activo {
            color: #28a745;
        }

        .patient-status.inactivo {
            color: #dc3545;
        }

        .patient-status.de.alta {
            color: #6c757d;
        }

        .load-more-container {
            text-align: center;
            margin-top: 24px;
        }

        /* Patient Profile Panel */
        .patient-profile-panel {
            width: 400px;
            height: 100vh;
            background-color: white;
            border-left: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8f9fa;
        }

        .close-panel {
            background: none;
            border: none;
            font-size: 16px;
            color: #6c757d;
            cursor: pointer;
            padding: 4px;
        }

        .panel-header span {
            font-size: 16px;
            font-weight: 500;
            color: #212529;
        }

        .panel-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .patient-profile {
            padding: 20px;
            flex: 1;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 24px;
            background-color: #212529;
            color: white;
            padding: 24px;
            border-radius: 12px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 16px;
            overflow: hidden;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .profile-details {
            display: flex;
            justify-content: center;
            gap: 16px;
            font-size: 14px;
            opacity: 0.9;
        }

        .profile-tabs {
            display: flex;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 12px 16px;
            font-size: 12px;
            color: #6c757d;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .tab-button.active {
            color: #212529;
            border-bottom-color: #212529;
        }

        .tab-button:hover {
            color: #495057;
        }

        .profile-content {
            flex: 1;
        }

        .info-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row label {
            font-size: 14px;
            color: #495057;
            font-weight: 500;
        }

        .info-row span {
            font-size: 14px;
            color: #212529;
        }

        .text-muted {
            color: #6c757d !important;
            font-style: italic;
        }

        .text-error {
            color: #dc3545 !important;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.activo {
            background-color: #d4edda;
            color: #155724;
        }

        .status-badge.inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-badge.de.alta {
            background-color: #e2e3e5;
            color: #383d41;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 0;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: #212529;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal form {
            padding: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 4px;
            font-weight: 500;
            color: #495057;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 8px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0d6efd;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        /* Appointments List */
        .appointments-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .appointment-item {
            padding: 12px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            background-color: #f8f9fa;
        }

        .appointment-date {
            font-weight: 500;
            color: #212529;
            margin-bottom: 4px;
        }

        .appointment-status {
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 3px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .appointment-status.programada {
            background-color: #cce5ff;
            color: #004085;
        }

        .appointment-status.completada {
            background-color: #d4edda;
            color: #155724;
        }

        .appointment-status.cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }

        .appointment-notes {
            font-size: 14px;
            color: #6c757d;
        }

        /* Notification Styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            color: white;
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .notification.info {
            background: #17a2b8;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .patient-profile-panel {
                width: 350px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                order: 2;
            }
            
            .main-content {
                order: 1;
            }
            
            .patient-profile-panel {
                width: 100%;
                height: auto;
                order: 3;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filters-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .sidebar-header span,
            .nav-link span,
            .option-link span,
            .bottom-link span,
            .section-title {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar,
        .main-content::-webkit-scrollbar,
        .patient-profile-panel::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track,
        .main-content::-webkit-scrollbar-track,
        .patient-profile-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .main-content::-webkit-scrollbar-thumb,
        .patient-profile-panel::-webkit-scrollbar-thumb {
            background: #dee2e6;
            border-radius: 2px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover,
        .main-content::-webkit-scrollbar-thumb:hover,
        .patient-profile-panel::-webkit-scrollbar-thumb:hover {
            background: #adb5bd;
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .d-none {
            display: none !important;
        }

        .d-block {
            display: block !important;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-center {
            justify-content: center;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-1 {
            margin-bottom: 0.25rem !important;
        }

        .mb-2 {
            margin-bottom: 0.5rem !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mb-4 {
            margin-bottom: 1.5rem !important;
        }

        .mt-0 {
            margin-top: 0 !important;
        }

        .mt-1 {
            margin-top: 0.25rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .p-1 {
            padding: 0.25rem !important;
        }

        .p-2 {
            padding: 0.5rem !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        /* Print Styles */
        @media print {
            .sidebar,
            .header-actions,
            .panel-actions,
            .btn-primary,
            .btn-secondary,
            .btn-icon {
                display: none !important;
            }

            .main-content {
                padding: 0;
            }

            .patient-profile-panel {
                width: 100%;
                border: none;
            }
        }
    </style>
</head>