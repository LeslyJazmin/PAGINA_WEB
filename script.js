// Modal functionality
const modal = document.getElementById('loginModal');
const btnLogin = document.getElementById('btnLogin');
const span = document.getElementsByClassName('close')[0];

function showLoginModal() {
    modal.style.display = "block";
}

function hideModal() {
    modal.style.display = "none";
}

btnLogin.onclick = showLoginModal;
span.onclick = hideModal;

window.onclick = function(event) {
    if (event.target == modal) {
        hideModal();
    }
}

// Redirect to login page
function redirectToLogin(userType) {
    window.location.href = `login.php?type=${userType}`;
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add scroll effect to header
window.addEventListener('scroll', function() {
    const header = document.querySelector('header');
    if (window.scrollY > 50) {
        header.style.background = 'rgba(255,255,255,0.95)';
    } else {
        header.style.background = 'white';
    }
});

class LoginManager {
    constructor() {
        this.modal = document.getElementById('loginModal');
        this.closeBtn = document.querySelector('.close');
        this.setupEventListeners();
    }

    setupEventListeners() {
        this.closeBtn.onclick = () => this.hideModal();
        window.onclick = (e) => {
            if (e.target === this.modal) this.hideModal();
        };

        // Manejar envío de formularios
        document.querySelectorAll('form').forEach(form => {
            form.onsubmit = this.handleSubmit.bind(this);
        });
    }

    showModal() {
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    hideModal() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch('login_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text().then(text => {
                    throw new Error(text);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error en el inicio de sesión. Por favor, intente nuevamente.');
        });
    }
}

function toggleForm(type) {
    const studentForm = document.getElementById('studentForm');
    const adminForm = document.getElementById('adminForm');
    const buttons = document.querySelectorAll('.type-btn');

    if (type === 'student') {
        studentForm.style.display = 'block';
        adminForm.style.display = 'none';
    } else {
        studentForm.style.display = 'none';
        adminForm.style.display = 'block';
    }

    buttons.forEach(btn => {
        btn.classList.toggle('active', btn.onclick.toString().includes(type));
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    const loginManager = new LoginManager();
    window.showLoginModal = () => loginManager.showModal();

    // Remover mensajes de error al escribir
    const inputs = document.querySelectorAll('.form-group.error input');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.parentElement.classList.remove('error');
            const errorHint = this.parentElement.querySelector('.error-hint');
            if (errorHint) {
                errorHint.style.display = 'none';
            }
        });
    });

    // Auto-ocultar mensaje de error después de 5 segundos
    const errorMessage = document.querySelector('.error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.opacity = '0';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 300);
        }, 5000);
    }

    // Efecto de entrada suave
    document.querySelector('.login-form').style.opacity = '0';
    setTimeout(() => {
        document.querySelector('.login-form').style.opacity = '1';
    }, 100);

    // Animación de los campos al escribir
    const inputsFocused = document.querySelectorAll('.form-group input');
    inputsFocused.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });

        // Si ya tiene valor al cargar
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });

    // Efecto de ripple en botones
    const buttons = document.querySelectorAll('.type-btn, .submit-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            let ripple = document.createElement('div');
            ripple.className = 'ripple';
            this.appendChild(ripple);

            let rect = this.getBoundingClientRect();
            let x = e.clientX - rect.left;
            let y = e.clientY - rect.top;

            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        button.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});