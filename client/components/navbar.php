

<nav class="fixed top-0 w-full z-50 bg-white/10 backdrop-blur-md text-black text-sm px-6 py-4 cursor-pointer">
  <div class="flex justify-between items-center">
    <div class="flex items-center space-x-2">
      <img src="assets/logo.png" alt="Fly High Logo" class="h-8 w-auto" />
      <span>Fly High</span>
    </div>  

    <ul class="hidden md:flex space-x-6">
      <li><a href="../index.php">Home</a></li>
      <li><a href="#">About</a></li>
      <li><a href="#">Offers</a></li>
      <li><a href="#">Tickets</a></li>
      <li><a href="#">Customer Supports</a></li>
    </ul>

    <!-- User Section -->
    <div class="hidden md:flex items-center space-x-4">
      <?php if (isset($_SESSION['username'])): ?>
        <span class="font-semibold text-teal-600">
          <?= htmlspecialchars($_SESSION['username']); ?>
        </span>
        <a href="pages/logout.php" class="hover:text-red-600 transition">
          Logout
        </a>
      <?php else: ?>
        <a href="pages/logreg.php" class="hover:text-teal-600 transition">
          Login / Signup
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>
