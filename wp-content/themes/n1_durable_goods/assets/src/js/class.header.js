class Header {
    constructor () {
        this.resizeOperations = []
        // Throttle scroll events to run at most once every 100ms
        this.scrollHandler = this.throttle(this.handleScroll.bind(this), 100)
    }

    resizeOnScroll (targetSelector, propertyToResize, minSize, maxSize, maxScroll, easing = '0.1s cubic-bezier(1, -0.02, 1, 1)') {
        let targetElement = document.querySelector(targetSelector)
        let minSizeFloat = parseFloat(minSize)
        let maxSizeFloat = parseFloat(maxSize)
        let unit = maxSize.replace(maxSizeFloat.toString(), '')

        if (targetElement) {
            targetElement.style.transition = `${ propertyToResize } ${ easing }`
            // Inform the browser that this element will animate
            targetElement.style.willChange = propertyToResize
            this.resizeOperations.push({
                targetElement   : targetElement,
                propertyToResize: propertyToResize,
                minSize         : minSizeFloat,
                maxSize         : maxSizeFloat,
                unit            : unit,
                maxScroll       : maxScroll,
                easing          : easing,
            })
            window.addEventListener('scroll', this.scrollHandler)
        }
    }

    handleScroll (event) {
        // Request an animation frame to delay handling of the scroll event until the next repaint
        window.requestAnimationFrame(() => {
            let scrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop
            for (let operation of this.resizeOperations) {
                let newSize = this.calculateSize(scrollPosition, operation.minSize, operation.maxSize, operation.maxScroll)
                this.resize(newSize, operation)
            }
        })
    }

    // Throttle function from: https://lodash.com/docs/4.17.15#throttle
    throttle (func, minWait, maxWait) {
        let context, args, result
        let timeout = null
        let previous = 0
        let previousScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop
        let later = function () {
            previous = new Date
            previousScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop
            timeout = null
            result = func.apply(context, args)
            if (!timeout) {
                context = args = null
            }
        }
        return function () {
            let now = new Date
            let currentScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop
            let scrollDifference = Math.abs(currentScrollPosition - previousScrollPosition)
            if (!previous) {
                previous = now
            }
            let wait = minWait + (maxWait - minWait) * Math.exp(-scrollDifference)
            let remaining = wait - (now - previous)
            context = this
            args = arguments
            if (remaining <= 0 || remaining > wait) {
                if (timeout) {
                    clearTimeout(timeout)
                    timeout = null
                }
                previous = now
                previousScrollPosition = currentScrollPosition
                result = func.apply(context, args)
                if (!timeout) {
                    context = args = null
                }
            } else if (!timeout) {
                timeout = setTimeout(later, remaining)
            }
            return result
        }
    }

    calculateSize (scrollPosition, minSize, maxSize, maxScroll) {
        let newSize = maxSize - ((maxSize - minSize) * Math.min(1, scrollPosition / maxScroll))
        // If newSize is within 1px of minSize or maxSize, return minSize or maxSize respectively.
        if (Math.abs(newSize - minSize) < 1) {
            return minSize
        } else if (Math.abs(newSize - maxSize) < 5) {
            return maxSize
        } else {
            return newSize
        }
    }

    resize (newSize, operation) {
        // Check if the new size is the same as the existing size
        let existingSize = parseFloat(operation.targetElement.style[operation.propertyToResize])
        if (existingSize !== newSize) {
            operation.targetElement.style[operation.propertyToResize] = `${ newSize }${ operation.unit }`
            // Add or remove the 'height-at-minimum' class depending on whether the new size is equal to minSize
            if (newSize === operation.minSize) {
                operation.targetElement.classList.add('height-at-minimum')
            } else {
                operation.targetElement.classList.remove('height-at-minimum')
            }
        }
    }

}

// Create an instance of the Header class and initialize the resize on scroll with the desired selector and parameters
// let header = new Header();
// header.resizeOnScroll('#site-header', 'max-height', '3.2rem', '11rem', 200, '0.1s ease-out 0s');

// (function () {
//     const element = document.getElementById('site-header');
//
//     // Get the initial height of the element
//     const initialHeight = element.offsetHeight;
//
//     window.addEventListener('scroll', () => {
//         // Get the current scroll position
//         const scrollY = window.scrollY;
//
//         // Calculate the new height by subtracting the scroll position from the initial height
//         const newHeight = Math.max(initialHeight - scrollY, 0); // Ensure it doesn't go negative
//
//         // Set the height of the element to the new height
//         element.style.height = newHeight + 'px';
//     });
// })();

// (function () {
//     const element = document.getElementById('site-header');
//
//     // Get the value of the --header-max-height custom property
//     const style = getComputedStyle(element);
//     const initialHeight = parseInt(style.getPropertyValue('--header-max-height'), 10);
//
//     let isCollapsed = false;
//
//     window.addEventListener('scroll', () => {
//         const scrollY = window.scrollY;
//
//         // Check if the user has scrolled down enough to trigger the animation
//         if (scrollY >= initialHeight && !isCollapsed) {
//             element.style.height = 'var(--header-min-height)';
//             isCollapsed = true;
//         } else if (scrollY < initialHeight && isCollapsed) {
//             element.style.height = 'var(--header-max-height)';
//             isCollapsed = false;
//         }
//     });
// })();

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

