<?php include('partials/header.php'); ?>

<style>
/* Smooth fade-in animation */
@keyframes fadeInSlow {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.fade-in-slow {
    animation: fadeInSlow 0.8s ease-out forwards;
}
</style>

<section class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 py-12">

    <div class="bg-white shadow-2xl rounded-3xl p-8 md:p-10 w-full max-w-3xl opacity-0 fade-in-slow">

        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900">Join BloodSync</h2>
            <p class="text-gray-500 mt-2">Become a donor and save lives today.</p>
        </div>

        <form id="registerForm" method="POST" action="register_handler.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Full Name</label>
                    <input type="text" name="fullname" placeholder="John Doe" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">NIC / ID Number</label>
                    <input type="text" name="nic" placeholder="National ID" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="example@email.com" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Phone Number</label>
                    <input type="tel" name="phone" placeholder="07X XXX XXXX" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Blood Type</label>
                    <div class="relative">
                        <select name="blood_type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all appearance-none bg-white">
                            <option value="" disabled selected>Select Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                        <i class="ri-arrow-down-s-line absolute right-4 top-3.5 text-gray-500 pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Date of Birth</label>
                    <input type="date" name="dob" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="relative">
                    <label class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input id="regPassword" type="password" name="password" placeholder="Create Password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>

                <div class="relative">
                    <label class="block text-gray-700 font-semibold mb-2">Confirm Password</label>
                    <input id="regConfirmPassword" type="password" name="confirm_password" placeholder="Confirm Password" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition-all">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold text-lg py-4 rounded-xl shadow-lg transition-transform transform hover:-translate-y-1 hover:shadow-red-500/30">
                Register as Donor
            </button>

            <p class="text-center text-gray-600 mt-6">
                Already have an account? 
                <a href="login.php" class="text-red-600 font-bold hover:underline">Log In</a>
            </p>

        </form>
    </div>
</section>

<?php include('partials/footer.php'); ?>