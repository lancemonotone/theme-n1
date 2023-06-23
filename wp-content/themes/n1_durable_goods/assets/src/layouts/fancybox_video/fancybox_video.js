// Vanilla JS version of FancyBox
// https://github.com/biati-digital/glightbox
import GLightbox from 'glightbox';
import 'glightbox/dist/css/glightbox.css';
import './fancybox_video.scss';

let customSlideHTML = `<div class="gslide">
    <div class="gslide-inner-content">
        <div class="ginner-container">
            <h4 class="gslide-title"></h4>
            <div class="gslide-media"></div>
            <div class="gdesc-inner">
                <div class="gslide-description">
                    <div class="gslide-desc"></div>
                </div>
            </div>
        </div>
    </div>
</div>`;

const lightbox = GLightbox({
  touchNavigation: true,
  loop           : false,
  autoplayVideos : false,
  slideHTML      : customSlideHTML,
});

lightbox.on('open', () => {
  // get the data-button_text attribute from the clicked element
  let button_text = lightbox.elements[lightbox.index].instance.element.getAttribute('data-button_link');

  if (button_text) {
    // wrap the .gslide-desc element of the current slide in an anchor tag with the href from button_text
    let gslide_desc = document.querySelector('.gslide-desc');
    gslide_desc.innerHTML = `<a href="${ button_text }">${ gslide_desc.innerHTML }</a>`;
  }
});
