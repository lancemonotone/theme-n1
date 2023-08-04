(function () {
    const element = document.getElementById('site-header');

    // Get the value of the --header-max-height custom property
    const style = getComputedStyle(element);
    const initialHeight = parseInt(style.getPropertyValue('--header-max-height'), 10);

    let isCollapsed = false;

    window.addEventListener('scroll', () => {
        const scrollY = Math.floor(window.scrollY);

        // Inform the browser that the height property will change, optimizing rendering
        element.style.willChange = 'height';

        // Check if the user has scrolled down enough to trigger the animation
        if (scrollY >= initialHeight && !isCollapsed) {
            element.style.height = 'var(--header-min-height)';
            isCollapsed = true;
        } else if (scrollY < initialHeight && isCollapsed) {
            element.style.height = 'var(--header-max-height)';
            isCollapsed = false;
        }

        // Optionally, you can reset willChange after the animation to release optimization resources
        // setTimeout(() => { element.style.willChange = 'auto'; }, 300);
    });
})();

