import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.log('Return home controller connected!'); // Debug
        
        // Attendre un court instant pour être sûr que le DOM est prêt
        setTimeout(() => {
            this.setupScrollButton();
            this.setupSmoothScrollLinks();
            this.setupCardAnimations();
        }, 100);
    }

    setupScrollButton() {
        const scrollTopBtn = document.getElementById('scrollTopBtn');
        
        if (!scrollTopBtn) {
            console.error('Bouton scroll to top introuvable !');
            return;
        }

        console.log('Bouton trouvé !', scrollTopBtn); // Debug

        // Fonction pour gérer l'affichage du bouton
        const handleScroll = () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                scrollTopBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                scrollTopBtn.classList.add('opacity-0', 'pointer-events-none');
                scrollTopBtn.classList.remove('opacity-100', 'pointer-events-auto');
            }
        };

        // Écouter le scroll
        window.addEventListener('scroll', handleScroll);

        // Clic sur le bouton
        scrollTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Clic détecté, scroll vers le haut...'); // Debug
            
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Vérifier l'état initial
        handleScroll();
    }

    setupSmoothScrollLinks() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && href !== '') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    setupCardAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        entry.target.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 100);

                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });
    }
}