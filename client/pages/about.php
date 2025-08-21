<?php
session_start();
include '../components/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - FlyHigh</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-sky-200 text-gray-800">


  <div class="max-w-6xl mx-auto px-6 py-16">
    <!-- Heading -->
    <h1 class="text-4xl font-bold text-blue-600 mb-6">About Our Flight Reservation System</h1>
    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
      Welcome to our <span class="font-semibold text-blue-600">Flight Reservation System(FlyHigh)</span>, 
      a modern and streamlined platform designed to make booking flights simple, fast.  
      Whether you're traveling for business, leisure, or emergencies, our system ensures that you can 
      find the right flight, reserve your seats instantly, and manage your bookings with ease.
    </p>

    <!-- Mission Section -->
    <div class="bg-white rounded-2xl shadow-md p-8 mb-10">
      <h2 class="text-2xl font-semibold text-blue-500 mb-4">Our Mission</h2>
      <p class="text-gray-600 leading-relaxed">
        Our mission is to deliver a hassle-free experience for passengers by providing a digital-first
        solution to flight reservations. We focus on speed, reliability, and user-friendly design, 
        ensuring that travelers spend less time booking and more time preparing for their journeys.
      </p>
    </div>

    <!-- Features Section -->
    <div class="grid md:grid-cols-2 gap-8 mb-12">
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-xl font-semibold text-blue-500 mb-3">Key Features</h3>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>Search and book flights instantly</li>
          <li>Real-time seat availability with auto-assignment</li>
          <li>Secure passenger information storage</li>
          <li>Easy booking management & cancellation</li>
          <li>Admin tools for managing flights and passengers</li>
        </ul>
      </div>
      <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-xl font-semibold text-blue-500 mb-3">Why Choose Us?</h3>
        <ul class="list-disc pl-6 text-gray-700 space-y-2">
          <li>User-friendly interface with smooth navigation</li>
          <li>Fast and reliable booking confirmation</li>
          <li>Transparent policies and secure transactions</li>
          <li>24/7 accessibility from any device</li>
          <li>Efficient seat allocation using greedy algorithm</li>
        </ul>
      </div>
    </div>

    <!-- Closing -->
    <div class="text-center">
      <p class="text-gray-700 text-lg leading-relaxed">
        We believe air travel should be accessible and stress-free.  
        With our <span class="font-semibold text-blue-600">Flight Reservation System</span>, 
        you’re only a few clicks away from your next journey.  
      </p>
    </div>
  </div>

</body>
</html>
