document.addEventListener('DOMContentLoaded', function() {
    // Add click animation to contact items
    const contactItems = document.querySelectorAll('.contact-item');
    
    contactItems.forEach(item => {
        item.addEventListener('click', function() {
            // Copy contact info to clipboard
            const text = this.querySelector('div').textContent;
            
            // Visual feedback
            this.style.transform = 'scale(0.98)';
            this.style.backgroundColor = 'rgba(96, 165, 250, 0.2)';
            
            setTimeout(() => {
                this.style.transform = '';
                this.style.backgroundColor = '';
            }, 300);
            
            // For phone number, suggest calling
            if (this.querySelector('.fa-phone-alt')) {
                if (confirm(`Call ${text}?`)) {
                    window.location.href = `tel:${text.replace(/\s+/g, '')}`;
                }
            }
            
            // For email, suggest composing email
            if (this.querySelector('.fa-envelope')) {
                if (confirm(`Send email to ${text}?`)) {
                    window.location.href = `mailto:${text}`;
                }
            }
        });
    });
    
    // WhatsApp button interaction
    const whatsappBtn = document.querySelector('.whatsapp-contact');
    
    whatsappBtn.addEventListener('mouseenter', function() {
        // Add a subtle animation
        this.style.animation = 'none';
        setTimeout(() => {
            this.style.animation = 'pulse-green 3s infinite';
        }, 50);
    });
    
    // Add a simple greeting when WhatsApp is clicked
    whatsappBtn.addEventListener('click', function(e) {
        // Optional: You could add tracking or confirmation here
        console.log('WhatsApp contact clicked');
    });
    
    // Animate contact info on scroll
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe contact items for animation
    contactItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(item);
    });
    
    // Observe the WhatsApp button
    whatsappBtn.style.opacity = '0';
    whatsappBtn.style.transform = 'translateY(20px)';
    whatsappBtn.style.transition = 'opacity 0.5s ease 0.3s, transform 0.5s ease 0.3s';
    observer.observe(whatsappBtn);
});