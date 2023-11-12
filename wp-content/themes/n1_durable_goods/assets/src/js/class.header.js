(function () {
    const $siteHeader = document.getElementById('site-header')

    if (!$siteHeader) {
        console.error('Element with ID \'site-header\' not found.')
        return
    }

    const body = document.body
    const documentElement = document.documentElement

    const headerStyle = getComputedStyle($siteHeader)

    let headerInitialHeight = headerStyle.getPropertyValue('--header-max-height').trim()
    if (headerInitialHeight === 'auto') {
        headerInitialHeight = $siteHeader.clientHeight
    } else if (headerInitialHeight.endsWith('rem')) {
        headerInitialHeight = parseFloat(headerInitialHeight) * parseFloat(getComputedStyle(document.documentElement).fontSize)
    } else {
        console.error('Unsupported value for --header-max-height: ', headerInitialHeight)
        return
    }

    // Define a buffer zone around initialHeight
    const bufferZone = 50  // 10 pixels as an example, you can adjust this value

    $siteHeader.style.paddingBlock = 'var(--header-max-padding-block)'

    let lastScrollTop = 0
    let throttleTimeout

    window.addEventListener('scroll', () => {
        if (throttleTimeout) {
            return
        }

        throttleTimeout = setTimeout(() => {
            throttleTimeout = null

            const scrollTop = body.scrollTop > documentElement.scrollTop ? body.scrollTop : documentElement.scrollTop

            // Check if scrollTop is within the buffer zone
            if (Math.abs(scrollTop - headerInitialHeight) <= bufferZone) {
                return
            }

            if (scrollTop > headerInitialHeight) {
                $siteHeader.style.paddingBlock = 'var(--header-min-padding-block)'
                $siteHeader.classList.add('header-collapsed')
            } else {
                $siteHeader.style.paddingBlock = 'var(--header-max-padding-block)'
                $siteHeader.classList.remove('header-collapsed')
            }
        }, 200)
    })
})()

// (function () {
//     const element = document.getElementById('site-header');
//     const body = document.body;
//
//     // Get the value of the --header-max-height custom property
//     const style = getComputedStyle(element);
//     const initialHeight = parseInt(style.getPropertyValue('--header-max-height'), 10);
//
//     let isCollapsed = false;
//
//     window.addEventListener('scroll', () => {
//         const scrollY = Math.floor(window.scrollY);
//
//         // Inform the browser that the height property will change, optimizing rendering
//         element.style.willChange = 'height';
//
//         // Check if the user has scrolled down enough to trigger the animation
//         if (scrollY >= initialHeight && !isCollapsed) {
//             element.style.height = 'var(--header-min-height)';
//             body.classList.add('header-collapsed'); // Add class to the body
//             isCollapsed = true;
//         } else if (scrollY < initialHeight && isCollapsed) {
//             element.style.height = 'var(--header-max-height)';
//             body.classList.remove('header-collapsed'); // Remove class from the body
//             isCollapsed = false;
//         }
//
//         // Optionally, you can reset willChange after the animation to release optimization resources
//         // setTimeout(() => { element.style.willChange = 'auto'; }, 300);
//     });
// })();


