document.addEventListener('DOMContentLoaded', function() {
    const carrusel = document.querySelector('.carrusel');
    const carruselItems = document.querySelectorAll('.carrusel-item');
    const prevButton = document.querySelector('.carrusel-controles .prev');
    const nextButton = document.querySelector('.carrusel-controles .next');

    const itemsPerPage = 4; // Mostrar 4 ítems a la vez
    const totalItems = carruselItems.length;
    let currentIndex = 0; // Índice del primer ítem visible

    // Calcula el ancho de un solo ítem incluyendo el margen
    // Esto es crucial para un desplazamiento preciso
    const itemWidth = carruselItems[0].offsetWidth + (parseFloat(getComputedStyle(carruselItems[0]).marginRight) || 0) + (parseFloat(getComputedStyle(carruselItems[0]).marginLeft) || 0);


    // Función para actualizar la posición del carrusel
    function updateCarousel() {
        // Calculamos el desplazamiento necesario para mostrar los ítems correctamente
        // Si el número de ítems no es múltiplo de itemsPerPage y llegamos al final,
        // ajustamos para mostrar los últimos ítems.
        const maxOffset = (totalItems - itemsPerPage) * itemWidth;
        let offset = currentIndex * itemWidth;

        // Asegurarse de que el offset no exceda el límite cuando hay pocos ítems restantes
        if (offset > maxOffset && totalItems > itemsPerPage) {
            offset = maxOffset;
        } else if (totalItems <= itemsPerPage) {
            offset = 0; // Si hay 4 o menos ítems, no hay necesidad de desplazamiento
        }

        carrusel.style.transform = `translateX(${-offset}px)`;
    }

    // Navegación a la izquierda
    prevButton.addEventListener('click', function() {
        currentIndex -= itemsPerPage;
        if (currentIndex < 0) {
            currentIndex = Math.max(0, totalItems - itemsPerPage); // Vuelve al final o al inicio si no hay suficientes
             // Ajuste para el desplazamiento circular si se va al final
            if (totalItems > itemsPerPage) {
                currentIndex = totalItems - itemsPerPage;
            } else {
                currentIndex = 0;
            }
        }
        updateCarousel();
    });

    // Navegación a la derecha
    nextButton.addEventListener('click', function() {
        currentIndex += itemsPerPage;
        if (currentIndex >= totalItems) {
            currentIndex = 0; // Vuelve al inicio
        }
        updateCarousel();
    });

    // Auto-giro del carrusel
    const autoScrollInterval = 3000; // 3 segundos
    let autoScrollTimer = setInterval(function() {
        currentIndex += itemsPerPage;
        if (currentIndex >= totalItems) {
            currentIndex = 0; // Reinicia al principio
        }
        updateCarousel();
    }, autoScrollInterval);

    // Pausar auto-giro al pasar el ratón por encima del carrusel
    carrusel.parentElement.addEventListener('mouseenter', function() {
        clearInterval(autoScrollTimer);
    });

    // Reanudar auto-giro al quitar el ratón del carrusel
    carrusel.parentElement.addEventListener('mouseleave', function() {
        autoScrollTimer = setInterval(function() {
            currentIndex += itemsPerPage;
            if (currentIndex >= totalItems) {
                currentIndex = 0;
            }
            updateCarousel();
        }, autoScrollInterval);
    });

    // Ajustar el carrusel en caso de redimensionamiento de la ventana
    window.addEventListener('resize', updateCarousel);

    // Inicializar el carrusel en la carga de la página
    updateCarousel();
});