let currentPage = 1;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const firstPage = document.querySelectorAll('.page')[0];
        firstPage.classList.add('active');
    }, 100);
});

function navigatePages(direction) {
    const pages = document.querySelectorAll('.page');
    const totalPages = pages.length;

    const currentElement = pages[currentPage - 1];
    currentElement.classList.remove('active');
    currentElement.classList.add('inactive');

    currentPage += direction;

    if (currentPage < 1) {
        currentPage = totalPages;
    } else if (currentPage > totalPages) {
        currentPage = 1;
    }

    const nextElement = pages[currentPage - 1];
    nextElement.classList.remove('inactive');
    nextElement.classList.add('active');

    updatePagination(currentPage - 1);
}

function showPage(pageNumber) {
    const pages = document.querySelectorAll('.page');

    pages.forEach(page => page.classList.remove('active', 'inactive'));
    
    const activePage = pages[pageNumber - 1];
    activePage.classList.add('active');

    currentPage = pageNumber;
    updatePagination(pageNumber - 1);
}

function updatePagination(activeIndex) {
    const dots = document.querySelectorAll('.dot');
    dots.forEach((dot, index) => {
        if (index === activeIndex) {
            dot.classList.add('active');
        } else {
            dot.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => showPage(1));
