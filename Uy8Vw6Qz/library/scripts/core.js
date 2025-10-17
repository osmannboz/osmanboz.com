document.addEventListener('DOMContentLoaded', () => {

    const header = document.querySelector("header");
    const nav = document.querySelector("header nav");
    const menuToggle = document.querySelector("header button");
    const menuIcon = menuToggle?.querySelector("i");
    const heroSection = document.querySelector(".hero-section");

    if (menuToggle && nav) {
        menuToggle.addEventListener("click", () => {
            const isActive = nav.classList.toggle("active");
            menuIcon?.classList.toggle("bi-list", !isActive);
            menuIcon?.classList.toggle("bi-x", isActive);
            document.body.classList.toggle("body-no-scroll", isActive);
        });
    }

    let lastScrollY = 0;
    let ticking = false;

    const handleScroll = () => {
        lastScrollY = window.scrollY;
        if (!ticking) {
            window.requestAnimationFrame(() => {
                const heroHeight = heroSection?.offsetHeight || 100;
                header?.classList.toggle("scrolled", lastScrollY > heroHeight - 80);
                ticking = false;
            });
            ticking = true;
        }
    };

    window.addEventListener("scroll", handleScroll);
    handleScroll();
});