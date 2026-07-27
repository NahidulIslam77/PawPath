<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- =========================
        HERO SECTION
========================== -->
<section class="grid grid-cols-1 lg:grid-cols-2 items-center gap-14 px-6 md:px-10 lg:px-14 py-16 bg-white">

    <!-- LEFT SIDE -->
    <div>

        <span class="inline-block bg-green-100 text-[#2D6A4F] px-5 py-2 rounded-full text-sm font-semibold mb-6">
            Find Your Perfect Companion
        </span>

        <h1 class="text-5xl md:text-7xl font-extrabold text-[#2D6A4F] leading-tight mb-6">
            Find your Perfect <br> pet companion
        </h1>

        <p class="text-gray-600 text-lg mb-8 max-w-xl">
            Adopt or foster pets and give them a loving, caring and safe home.
        </p>

        <div class="flex gap-5 flex-wrap">

            <a href="auth/register.php"
                class="bg-[#2D6A4F] text-white px-8 py-4 rounded-full font-semibold hover:bg-[#40916c] transition">
                Get Started
            </a>

            <a href="#features"
                class="border-2 border-[#2D6A4F] text-[#2D6A4F] px-8 py-4 rounded-full font-semibold hover:bg-[#2D6A4F] hover:text-white transition">
                Explore Features
            </a>

        </div>
        <div
                class="bg-[#2D6A4F] text-white rounded-3xl p-8 flex flex-wrap justify-between gap-8 mt-5 shadow-2xl max-w-2xl">

                <div class="text-center flex-1 min-w-[120px]">

                    <h3 class="text-4xl font-bold mb-1">
                        3200+
                    </h3>

                    <p class="text-sm opacity-90">
                        Pets adopted
                    </p>

                </div>

                <div class="text-center flex-1 min-w-[120px]">

                    <h3 class="text-4xl font-bold mb-1">
                        480+
                    </h3>

                    <p class="text-sm opacity-90">
                        Active Shelters
                    </p>

                </div>

                <div class="text-center flex-1 min-w-[120px]">

                    <h3 class="text-4xl font-bold mb-1">
                        1500+
                    </h3>

                    <p class="text-sm opacity-90">
                        Happy Families
                    </p>

                </div>

            </div>

    </div>

    <!-- RIGHT IMAGE -->
    <div>

        <img src="assets/images/hero.jpg"
            class="w-full h-[400px] md:h-[500px] object-cover rounded-3xl shadow-xl">

    </div>

</section>

<!-- =========================
        FEATURES
========================== -->
<!-- =========================
        FEATURES
========================== -->
<section id="features"
    class="py-20 px-4 sm:px-6 md:px-10 lg:px-14 bg-white">

    <div class="text-center mb-16">

        <h2 class="text-4xl md:text-5xl font-extrabold text-[#2D6A4F] mb-5">
            Core Features
        </h2>

        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
            PawPath provides a complete adoption and fostering experience
            for users and shelters.
        </p>

    </div>

    <!-- CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <!-- ================= ADOPT ================= -->
        <div
            class="group relative h-[350px] md:h-[500px] rounded-[30px] overflow-hidden shadow-2xl cursor-pointer">

            <img src="assets/images/adopt.jpg"
                class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">

            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>

            <div class="absolute bottom-0 p-8 text-white">

                <div
                    class="bg-white/20 backdrop-blur-md w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-5">

                    <i class="fa-solid fa-heart"></i>

                </div>

                <h3 class="text-4xl font-bold mb-3">
                    Adopt Pets
                </h3>

                <p
                    class="text-lg text-gray-200 opacity-0 group-hover:opacity-100 transition duration-500">

                    Give a forever loving home to pets in need.

                </p>

            </div>

        </div>

        <!-- ================= FOSTER ================= -->
        <div
            class="group relative h-[350px] md:h-[500px] rounded-[30px] overflow-hidden shadow-2xl cursor-pointer">

            <img src="assets/images/foster.jpg"
                class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110">

            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent"></div>

            <div class="absolute bottom-0 p-8 text-white">

                <div
                    class="bg-white/20 backdrop-blur-md w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mb-5">

                    <i class="fa-solid fa-house"></i>

                </div>

                <h3 class="text-4xl font-bold mb-3">
                    Foster Pets
                </h3>

                <p
                    class="text-lg text-gray-200 opacity-0 group-hover:opacity-100 transition duration-500">

                    Provide temporary care and support for pets.

                </p>

            </div>

        </div>

    </div>

</section>

<!-- =========================
        CTA SECTION
========================== -->
<section class="bg-[#2D6A4F] text-white text-center py-20 px-6">

    <h2 class="text-4xl md:text-5xl font-bold mb-6">
        Adopt love. Change a life.
    </h2>

    <p class="mb-8 text-lg opacity-90">
        Every pet deserves a caring home ❤️
    </p>

    <a href="auth/register.php"
        class="bg-white text-[#2D6A4F] px-8 py-4 rounded-full font-bold hover:scale-105 transition inline-block">
        Create Account
    </a>

</section>

<?php include 'includes/footer.php'; ?>