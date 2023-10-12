<?php
    // Start session
    session_start();
    // DB connection
    try {
        $conn = new PDO('mysql:host=localhost;dbname=invoice_test', 'root', '');
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // echo "Connected successfully";
        }
    catch(PDOException $e)
        {
        echo "Connection failed: " . $e->getMessage();
        }