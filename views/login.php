<?php include('partials/header.php'); ?>

<style>
/* Reusing the smooth fade-in animation */
@keyframes fadeInSlow {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.fade-in-slow {
    animation: fadeInSlow 0.8s ease-out forwards;
}
</style>

<section class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-6 py-20">

    <div class="bg-white shadow-2xl rounded-3xl p-10 w-full max-w-md opacity-0 fade-in-slow">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900">Welcome Back</h2>
            <p class="text-gray-500 mt-2">Log in to manage your donations.</p>
        </div>

        <form id="loginForm" method="POST" action="login_handler.php">

            <div class="mb-6">
                <label class="block text-gray-800 font-semibold mb-2">Email Address</label>
                <div class="relative">
                    <input id="email" name="email" type="email" placeholder="name@example.com" required
                        class="w-full pl-4 pr-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-800 font-semibold mb-2">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" placeholder="Enter your password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                    
                    <i id="togglePassword" 
                       class="ri-eye-line absolute right-4 top-3.5 text-gray-400 text-xl cursor-pointer hover:text-red-600 transition">
                    </i>
                </div>
            </div>

            <div class="flex justify-between items-center mb-8">
                <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                    <input type="checkbox" class="mr-2 text-red-600 focus:ring-red-500 rounded">
                    Remember me
                </label>
                <a href="#" class="text-sm font-semibold text-red-600 hover:text-red-800 transition">Forgot Password?</a>
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-lg py-3 rounded-xl shadow-lg transition-transform transform hover:-translate-y-1 hover:shadow-red-500/30">
                Log In
            </button>

            <div class="text-center mt-8">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="register.php" class="text-red-600 font-bold hover:underline">Register Now</a>
                </p>
            </div>

        </form>
    </div>
</section>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Toggle the eye icon
        this.classList.toggle('ri-eye-line');
        this.classList.toggle('ri-eye-off-line');
    });
</script>

<?php include('partials/footer.php'); ?>