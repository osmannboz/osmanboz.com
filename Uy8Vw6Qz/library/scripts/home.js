document.addEventListener('DOMContentLoaded', () => {

    // --- HEADER ve HERO SLIDER + MOBİL MENÜ --- //
    const header = document.querySelector("header");
    const nav = document.querySelector("header nav");
    const menuToggle = document.querySelector("header button");
    const menuIcon = menuToggle?.querySelector("i");
    const heroSection = document.querySelector(".hero-slider-section");
    const slides = document.querySelectorAll(".hero-slider-section .slide");

    // Mobil Menü İşlevi
    if (menuToggle && nav) {
        menuToggle.addEventListener("click", () => {
            const isActive = nav.classList.toggle("active");
            menuIcon?.classList.toggle("bi-list", !isActive);
            menuIcon?.classList.toggle("bi-x", isActive);
            document.body.classList.toggle("body-no-scroll", isActive);
        });
    }

    // Hero Slider (Otomatik + Dot Navigasyon)
    if (slides.length > 0 && heroSection) {
        let currentSlide = 0;
        slides[currentSlide].classList.add("active");

        const intervalTime = 5000; // 5 saniye
        let slideInterval;

        let dotsContainer = document.querySelector(".slider-dots");
        if (!dotsContainer) {
            dotsContainer = document.createElement("div");
            dotsContainer.classList.add("slider-dots");
            heroSection.appendChild(dotsContainer);
        }

        slides.forEach((_, index) => {
            const dot = document.createElement("button");
            dot.classList.add("dot");
            if (index === 0) dot.classList.add("active");
            dot.addEventListener("click", () => {
                currentSlide = index;
                showSlide(currentSlide);
                resetInterval();
            });
            dotsContainer.appendChild(dot);
        });

        const dots = dotsContainer.querySelectorAll("button");

        const showSlide = (index) => {
            slides.forEach(slide => slide.classList.remove("active"));
            slides[index].classList.add("active");
            dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
        };

        const nextSlide = () => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        };

        const resetInterval = () => {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, intervalTime);
        };

        slideInterval = setInterval(nextSlide, intervalTime);
    }

    // Header Scroll (Throttle + Daha Stabil)
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
    handleScroll(); // Sayfa yüklendiğinde kontrol et

    // --- ALT JS (Accordion, Testimonial Slider, Counter) --- //

    const accordionHeaders = document.querySelectorAll('.accordion-header');

    if (accordionHeaders.length > 0) {
        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const isExpanded = header.getAttribute('aria-expanded') === 'true';
                const content = header.nextElementSibling;
                const icon = header.querySelector('.icon');

                accordionHeaders.forEach(otherHeader => {
                    if (otherHeader !== header) {
                        const otherContent = otherHeader.nextElementSibling;
                        const otherIcon = otherHeader.querySelector('.icon');
                        
                        otherHeader.setAttribute('aria-expanded', 'false');
                        otherContent.classList.remove('show');
                        if (otherIcon) {
                            otherIcon.textContent = '+';
                        }
                    }
                });

                header.setAttribute('aria-expanded', !isExpanded);
                content.classList.toggle('show');
                if (icon) {
                    icon.textContent = isExpanded ? '+' : '−';
                }
            });
        });
    }

    const slider = document.querySelector('.testimonial-slider');

    if (slider) {
        const testimonials = slider.querySelectorAll('.testimonial');
        const intervalTime = 5000;
        let currentIndex = 0;
        let slideInterval;

        if (testimonials.length > 0) {
            const dotContainer = document.createElement('div');
            dotContainer.classList.add('testimonial-dots');
            dotContainer.setAttribute('aria-label', 'Slider navigasyon');
            slider.appendChild(dotContainer);

            testimonials.forEach((testimonial, index) => {
                const dot = document.createElement('button');
                dot.classList.add('dot');
                dot.setAttribute('aria-label', `${index + 1}. yorum`);
                dot.addEventListener('click', () => {
                    showTestimonial(index);
                    resetInterval();
                });
                dotContainer.appendChild(dot);
            });

            const dots = dotContainer.querySelectorAll('.dot');

            function showTestimonial(index) {
                testimonials[currentIndex].classList.remove('active');
                dots[currentIndex].classList.remove('active');
                currentIndex = index;
                testimonials[currentIndex].classList.add('active');
                dots[currentIndex].classList.add('active');
            }

            function startInterval() {
                slideInterval = setInterval(() => {
                    let nextIndex = (currentIndex + 1) % testimonials.length;
                    showTestimonial(nextIndex);
                }, intervalTime);
            }

            function resetInterval() {
                clearInterval(slideInterval);
                startInterval();
            }

            showTestimonial(0);
            startInterval();
        }
    }

    const statusSection = document.querySelector('.status-section');

    if (statusSection) {
        const counters = statusSection.querySelectorAll('.stat-number');

        const animateCounter = (counter) => {
            const target = +counter.dataset.target;
            const duration = 2000;
            let startTime = null;

            const step = (timestamp) => {
                if (!startTime) startTime = timestamp;
                const progress = timestamp - startTime;
                const current = Math.min(Math.floor(progress / duration * target), target);
                
                counter.textContent = current.toLocaleString('en-US');

                if (progress < duration) {
                    requestAnimationFrame(step);
                } else {
                    counter.textContent = target.toLocaleString('en-US');
                }
            };
            requestAnimationFrame(step);
        };

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    counters.forEach(animateCounter);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        observer.observe(statusSection);
    }
});
