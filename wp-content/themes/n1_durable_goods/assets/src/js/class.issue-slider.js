import 'glider-js/glider.min.js';
import 'glider-js/glider.min.css';

document.addEventListener('DOMContentLoaded', function () {
    (function () {
        const issueSlider = document.querySelector('#issue-slider .glider')
        if (issueSlider) {
            new Glider(issueSlider, {
                slidesToShow  : 2,
                slidesToScroll: 1,
                scrollLock    : true,
                // dots          : '#issue-slider .glider-dots',
                arrows        : {
                    prev: '#prev3',
                    next: '#next3',
                },
                pauseOnHover  : false,
                responsive    : [
                    {
                        breakpoint: 1030,
                        settings  : {
                            slidesToShow  : 8,
                            slidesToScroll: 7,
                        },
                    },
                    {
                        breakpoint: 600,
                        settings  : {
                            slidesToShow  : 4,
                            slidesToScroll: 3,
                        },
                    },
                ],
            })
        }
    })()
})
