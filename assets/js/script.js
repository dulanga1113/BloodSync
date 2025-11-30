document.addEventListener("DOMContentLoaded", () => {

    console.log("BloodSync homepage loaded. Ready to connect donors!");

    // === COUNT UP ANIMATION ===
    const counters = document.querySelectorAll('.countup');
    const speed = 80; // smaller = faster

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;

            const increment = Math.ceil(target / speed);

            if (count < target) {
                counter.innerText = count + increment;
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target.toLocaleString();
            }
        };

        updateCount();
    });

});