// Dark/Light Mode Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Verificar tema salvo
    const temaSalvo = localStorage.getItem('tema');
    if (temaSalvo === 'dark') {
        document.body.classList.add('dark-mode');
        atualizarIconeTema(true);
    }
    
    // Botão de toggle
    const btnToggle = document.getElementById('themeToggle');
    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('tema', isDark ? 'dark' : 'light');
            atualizarIconeTema(isDark);
        });
    }
    
    function atualizarIconeTema(isDark) {
        const btn = document.getElementById('themeToggle');
        if (btn) {
            btn.innerHTML = isDark ? '☀️' : '🌙';
            btn.title = isDark ? 'Modo Claro' : 'Modo Escuro';
        }
    }
    
    // Animação suave nos cards
    const cards = document.querySelectorAll('.card-noticia');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
});