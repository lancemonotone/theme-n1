/*
 * Smooth scroll to anchors, vanilla JS, back button support
 */
// Call the smoothScroll function to execute it immediately when the module is loaded
smoothScroll();

// same code as above but with the comments ABOVE the lines instead of next to them
function smoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');

  for (const link of links) {
    addScrollTo(link);
  }
}

function addScrollTo(link) {
  link.addEventListener('click', (event) => {
    // prevent the default link behavior
    event.preventDefault();

    // get the target element id
    const targetId = link.getAttribute('href');
    // get the target element
    const targetElement = document.querySelector(targetId);

    if (targetElement) {
      // get the target element offsetTop
      const targetOffsetTop = targetElement.offsetTop;
      // set the duration of the scroll animation in milliseconds
      const duration = 1000;
      // set the easing function (cubic easing)
      const easing = (t) => t * t * t;

      // Save the current scroll position in the browser's session history
      history.pushState({scrollPosition: window.pageYOffset}, '');

      // scroll to the target element
      scrollTo(targetOffsetTop, duration, easing);
    }
  });
}

function scrollTo(targetOffsetTop, duration, easing) {
  // get the starting scroll position
  const start = window.pageYOffset;
  // get the starting time
  const startTime = 'now' in window.performance ? performance.now() : new Date().getTime();

  const animateScroll = (currentTime) => {
    // calculate the time elapsed
    const timeElapsed = currentTime - startTime;
    // calculate the scroll distance
    const scrollDistance = targetOffsetTop - start;
    // calculate the new scroll position
    const scrollPosition = easing(Math.min(timeElapsed / duration, 1)) * scrollDistance + start;

    // set the new scroll position
    window.scrollTo(0, scrollPosition);

    if (timeElapsed < duration) {
      // call animateScroll again on the next animation frame
      requestAnimationFrame(animateScroll);
    }
  };

  // start the animation
  requestAnimationFrame(animateScroll);
}

// Listen for the popstate event, which is triggered when the user clicks the back button
window.addEventListener('popstate', (event) => {
  if (event.state && event.state.scrollPosition) {
    const targetOffsetTop = event.state.scrollPosition;
    const duration = 1000;
    const easing = (t) => t * t * t;

    scrollTo(targetOffsetTop, duration, easing);
  }
});

