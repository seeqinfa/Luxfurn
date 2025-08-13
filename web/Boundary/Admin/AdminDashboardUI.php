<?php
require_once '../../header.php';
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-container {
            margin-top: 140px;
            max-width: 1200px;
            width: 100%;
            padding: 0 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-container {
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
        }

        .search-container input {
            padding: 10px;
            flex: 1;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .search-container button {
            padding: 10px 20px;
            background-color: #e67e22;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .search-container button:hover {
            background-color: #d35400;
        }

        .section-title {
            font-size: 24px;
            margin: 30px 0 20px;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: rgba(255,255,255,0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }

        .card-header {
            background-color: #e67e22;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .card-header i {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .card-text {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .btn-container {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            flex: 1;
        }

        .btn-primary {
            background-color: #e67e22;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #d35400;
        }

        .btn-secondary {
            background-color: #3498db;
            color: white;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #e67e22;
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        textarea.form-control {
            min-height: 100px;
        }

        .pagination {
            text-align: center;
            margin-top: 30px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            background-color: #f4f4f4;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a.active {
            background-color: #e67e22;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }

        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
            
            .btn-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <h1 class="section-title">Admin Dashboard</h1>
    
<!-- Quick Actions Section -->
<div class="grid-container">
    <!-- Manage Products Card -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-box-open"></i>
            <h3>Manage Products</h3>
        </div>
        <div class="card-body">
            <div class="card-title">Product Management</div>
            <div class="card-text">Add, edit, or remove products from your inventory</div>
            <div class="btn-container">
                <a href="AdminAddProduct.php" class="btn btn-primary">Add Product</a>
                <a href="AdminManageProduct.php" class="btn btn-secondary">View All</a>
            </div>
        </div>
    </div>

    <!-- Manage Users Card -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users"></i>
            <h3>Manage Users</h3>
        </div>
        <div class="card-body">
            <div class="card-title">User Management</div>
            <div class="card-text">View and manage all user accounts</div>
            <div class="btn-container">
                <a href="AdminManageUsers.php" class="btn btn-primary">View Users</a>
            </div>
        </div>
    </div>

    <!-- Manage Ratings & Reviews Card -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-star"></i>
            <h3>Ratings & Reviews</h3>
        </div>
        <div class="card-body">
            <div class="card-title">Review Management</div>
            <div class="card-text">View customer ratings and reviews</div>
            <div class="btn-container">
                <a href="AdminRatingandReviewsUI.php" class="btn btn-primary">View Reviews</a>
            </div>
        </div>
    </div>

<!-- Assign Support Ticket-->
<div class="card">
    <div class="card-header">
        <i class="fas fa-ticket-alt"></i>
        <h3>Support Tickets</h3>
    </div>
    <div class="card-body">
        <div class="card-title">Ticket Assignment</div>
        <div class="card-text">Manage and assign support tickets to agents</div>
        <div class="btn-container">
            <a href="AdminSupportTicketsUI.php" class="btn btn-primary">Manage Tickets</a>
            <a href="AdminAssignRoleUI.php" class="btn btn-secondary">Assign Tickets</a>
        </div>
    </div>
</div>

<!-- Chatbot Management Card -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-robot"></i>
        <h3>Chatbot</h3>
    </div>
    <div class="card-body">
        <div class="card-title">Chatbot Management</div>
        <div class="card-text">Monitor and review customer interactions with LuxBot</div>
        <div class="btn-container">
            <a href="AdminViewChatbotUI.php" class="btn btn-primary">View Chatbot Chat</a>
        </div>
    </div>
</div>


        </tbody>
    </table>
    </div>
</div>
</body>
</html>