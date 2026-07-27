<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
?>

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-[#D9D9D9] shadow-sm">

    <div class="w-full px-4 sm:px-6 md:px-10 lg:px-14 py-4">

        <div class="flex items-center justify-between">

            <!-- LOGO -->
            <a href="#"
               class="text-3xl md:text-4xl font-serif flex items-baseline">

                <span class="text-[#6B8E23] italic">Paw</span>
                <span class="text-[#2B4C7E] ml-1">Path</span>

            </a>

            <!-- MENU -->
            <div class="hidden md:flex items-center gap-8 text-[17px] font-medium">

                <!-- GUEST -->
                <?php if(!$user_id): ?>

                    <a href="/PawPath/index.php">Home</a>
                    <a href="/PawPath/auth/login.php">Login</a>
                    <a href="/PawPath/auth/register.php"
                       class="bg-[#2D6A4F] text-white px-4 py-2 rounded-full">
                        Get Started
                    </a>

                <?php endif; ?>


                <!-- USER -->
                <?php if($role === 'user'): ?>

                    <a href="/PawPath/user/dashboard.php">Dashboard</a>
                    <a href="/PawPath/user/adopt-pets.php">Adopt Pets</a>
                    <a href="/PawPath/user/foster-pets.php">Foster Pets</a>
                    <a href="/PawPath/user/my-applications.php">My Applications</a>

                <?php endif; ?>


                <!-- SHELTER -->
                <?php if($role === 'shelter'): ?>

                    <a href="/PawPath/shelter/dashboard.php">Dashboard</a>
                    <a href="/PawPath/shelter/manage-pets.php">Manage Pets</a>
                    <a href="/PawPath/shelter/pet-applications.php">Applications</a>

                <?php endif; ?>


                <!-- ADMIN -->
                <?php if($role === 'admin'): ?>

                    <a href="/PawPath/admin/dashboard.php">Admin Panel</a>
                    <a href="/PawPath/admin/manage-users.php">Users</a>
                    <a href="/PawPath/admin/manage-pets.php">Pets</a>
                    <a href="/PawPath/admin/manage-shelters.php">Shelters</a>

                <?php endif; ?>


                <!-- LOGOUT (ALL LOGGED USERS) -->
                <?php if($user_id): ?>
                    <a href="/PawPath/auth/logout.php"
                       class="text-red-600 font-semibold">
                        Logout
                    </a>
                <?php endif; ?>

            </div>

            <!-- MOBILE BUTTON -->
            <button id="menu-btn"
                class="md:hidden text-3xl text-[#2D6A4F]">
                ☰
            </button>

        </div>

        <!-- MOBILE MENU -->
        <div id="mobile-menu"
            class="hidden md:hidden flex flex-col gap-4 mt-5 pb-2 text-lg font-medium">

            <?php if(!$user_id): ?>

                <a href="/PawPath/index.php">Home</a>
                <a href="/PawPath/auth/login.php">Login</a>
                <a href="/PawPath/auth/register.php">Register</a>

            <?php elseif($role === 'user'): ?>

                <a href="/PawPath/user/dashboard.php">Dashboard</a>
                <a href="/PawPath/user/adopt-pets.php">Adopt Pets</a>
                <a href="/PawPath/user/foster-pets.php">Foster Pets</a>
                <a href="/PawPath/user/my-applications.php">My Applications</a>

            <?php elseif($role === 'shelter'): ?>

                <a href="/PawPath/shelter/dashboard.php">Dashboard</a>
                <a href="/PawPath/shelter/manage-pets.php">Manage Pets</a>

            <?php elseif($role === 'admin'): ?>

                <a href="/PawPath/admin/dashboard.php">Admin Panel</a>

            <?php endif; ?>

            <?php if($user_id): ?>
                <a href="/PawPath/auth/logout.php" class="text-red-600">
                    Logout
                </a>
            <?php endif; ?>

        </div>

    </div>

</nav>

<!-- MOBILE TOGGLE SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');

    if(btn && menu){
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            btn.textContent = menu.classList.contains('hidden') ? '☰' : '✖';
        });
    }

});
</script>