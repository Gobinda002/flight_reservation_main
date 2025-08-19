<nav class="fixed top-0 w-full z-50 bg-white/10 backdrop-blur-md text-black text-sm px-6 py-4 cursor-pointer">
  <div class="flex justify-between items-center">
    <div class="flex items-center space-x-2">
      <img src="assets/logo.png" alt="Fly High Logo" class="h-8 w-auto" />
      <span>Fly High</span>
    </div>

    <ul class="hidden md:flex space-x-6">
      <li><a href="../index.php">Home</a></li>
      <li><a href="#">About</a></li>
      <li><a href="pages/mybooking.php">My Bookings</a></li>
      <li><a href="#">Customer Supports</a></li>
    </ul>

    <!-- User Section -->
<div class="hidden md:flex items-center space-x-4">
  <?php if (isset($_SESSION['username'])): ?>
    <span class="font-bold text-black-500 text-m">
      <?= htmlspecialchars($_SESSION['username']); ?>
    </span>
    <a href="pages/logout.php" 
       class="flex items-center px-2 py-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition">
       <!-- Logout Icon -->
       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
       </svg>
       
    </a>
  <?php else: ?>
    <a href="pages/logreg.php" class="hover:text-teal-600 transition">
      Login / Signup
    </a>
  <?php endif; ?>
</div>

  </div>
</nav>