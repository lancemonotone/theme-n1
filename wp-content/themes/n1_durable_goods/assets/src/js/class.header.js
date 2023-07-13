class Header {
  constructor() {
    this.resizeOperations = [];
    // Throttle scroll events to run at most once every 100ms
    this.scrollHandler = this.throttle(this.handleScroll.bind(this), 100);
  }

  resizeOnScroll(targetSelector, propertyToResize, minSize, maxSize, maxScroll, easing = '0.1s cubic-bezier(1, -0.02, 1, 1)') {
    let targetElement = document.querySelector(targetSelector);
    let minSizeFloat = parseFloat(minSize);
    let maxSizeFloat = parseFloat(maxSize);
    let unit = maxSize.replace(maxSizeFloat.toString(), '');

    if (targetElement) {
      targetElement.style.transition = `${propertyToResize} ${easing}`;
      // Inform the browser that this element will animate
      targetElement.style.willChange = propertyToResize;
      this.resizeOperations.push({
        targetElement: targetElement,
        propertyToResize: propertyToResize,
        minSize: minSizeFloat,
        maxSize: maxSizeFloat,
        unit: unit,
        maxScroll: maxScroll,
        easing: easing
      });
      window.addEventListener('scroll', this.scrollHandler);
    }
  }

  handleScroll(event) {
    // Request an animation frame to delay handling of the scroll event until the next repaint
    window.requestAnimationFrame(() => {
      let scrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop;
      for (let operation of this.resizeOperations) {
        let newSize = this.calculateSize(scrollPosition, operation.minSize, operation.maxSize, operation.maxScroll);
        this.resize(newSize, operation);
      }
    });
  }

  // Your other methods here...

  // Throttle function from: https://lodash.com/docs/4.17.15#throttle
  throttle(func, minWait, maxWait) {
    let context, args, result;
    let timeout = null;
    let previous = 0;
    let previousScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop;
    let later = function() {
      previous = new Date;
      previousScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop;
      timeout = null;
      result = func.apply(context, args);
      if (!timeout) context = args = null;
    };
    return function() {
      let now = new Date;
      let currentScrollPosition = window.scrollY || window.scrollTop || document.getElementsByTagName('html')[0].scrollTop;
      let scrollDifference = Math.abs(currentScrollPosition - previousScrollPosition);
      if (!previous) previous = now;
      let wait = minWait + (maxWait - minWait) * Math.exp(-scrollDifference);
      let remaining = wait - (now - previous);
      context = this;
      args = arguments;
      if (remaining <= 0 || remaining > wait) {
        if (timeout) {
          clearTimeout(timeout);
          timeout = null;
        }
        previous = now;
        previousScrollPosition = currentScrollPosition;
        result = func.apply(context, args);
        if (!timeout) context = args = null;
      } else if (!timeout) {
        timeout = setTimeout(later, remaining);
      }
      return result;
    };
  }


  calculateSize(scrollPosition, minSize, maxSize, maxScroll) {
    let newSize = maxSize - ((maxSize - minSize) * Math.min(1, scrollPosition / maxScroll));
    // If newSize is within 1px of minSize or maxSize, return minSize or maxSize respectively.
    if (Math.abs(newSize - minSize) < 1) {
      return minSize;
    } else if (Math.abs(newSize - maxSize) < 5) {
      return maxSize;
    } else {
      return newSize;
    }
  }

  resize(newSize, operation) {
    // Check if the new size is the same as the existing size
    let existingSize = parseFloat(operation.targetElement.style[operation.propertyToResize]);
    if (existingSize !== newSize) {
      operation.targetElement.style[operation.propertyToResize] = `${newSize}${operation.unit}`;
      // Add or remove the 'height-at-minimum' class depending on whether the new size is equal to minSize
      if (newSize === operation.minSize) {
        operation.targetElement.classList.add('height-at-minimum');
      } else {
        operation.targetElement.classList.remove('height-at-minimum');
      }
    }
  }

}

// Create an instance of the Header class and initialize the resize on scroll with the desired selector and parameters
let header = new Header();
header.resizeOnScroll('#site-header', 'height', '3.2rem', '10rem', 200, '0.1s ease-out 0s');
