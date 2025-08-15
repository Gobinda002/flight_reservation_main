
<?php session_start(); ?>
<nav class="fixed top-0 w-full z-50 bg-white/10 backdrop-blur-md text-black text-sm px-6 py-4 cursor-pointer">
  <div class="flex justify-between items-center">
    <div class="flex items-center space-x-2">
      <img src="assets/logo.png" alt="Fly High Logo" class="h-8 w-auto" />
      <span>Fly High</span>
    </div>  

    <ul class="hidden md:flex space-x-6">
      <li><a href="index.php">Home</a></li>
      <li><a href="#">About</a></li>
      <li><a href="#">Offers</a></li>
      <li><a href="#">Tickets</a></li>
      <li><a href="#">Customer Supports</a></li>
    </ul>

    <?php if (isset($_SESSION['user'])): ?>
      <div class="hidden md:flex items-center space-x-4 relative">
        <button id="userMenuBtn" class="text-teal-600 hover:text-teal-800 transition-colors duration-300 cursor-pointer flex items-center space-x-1">
          <span><?= htmlspecialchars($_SESSION['user']) ?></span>
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <!-- Dropdown Menu -->
        <div id="userDropdown" class="absolute top-full right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
          <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100">
            <span class="font-medium"><?= htmlspecialchars($_SESSION['user']) ?></span>
          </div>
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            Profile
          </a>
          <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            My Bookings
          </a>
          <a href="pages/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
            Logout
          </a>
        </div>
      </div>
    <?php else: ?>
      <a href="pages/logreg.php" class="hidden md:block cursor-pointer text-teal-600 hover:text-teal-800 transition-colors duration-300">
        Login/Signup
      </a>
    <?php endif; ?>
  </div>
</nav>

<script>
  // User dropdown menu functionality
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');
  
  if (userMenuBtn && userDropdown) {
    // Toggle dropdown when username is clicked
    userMenuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      userDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.classList.add('hidden');
      }
    });
    
    // Close dropdown when pressing Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        userDropdown.classList.add('hidden');
      }
    });
  }
</script>
