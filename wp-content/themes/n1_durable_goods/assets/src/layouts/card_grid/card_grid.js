import './card_grid.scss'

window.addEventListener('load', function() {
  const cardGroups = document.querySelectorAll('.card-container')

  let maxHeightHeader
  let maxHeightBody

  function syncHeights(card) {
    let header = card.querySelector('.card-header')
    let body = card.querySelector('.card-body')

    let headerHeight = header.offsetHeight
    let bodyHeight = body.offsetHeight

    if (headerHeight > maxHeightHeader) {
      maxHeightHeader = headerHeight
    }

    if (bodyHeight > maxHeightBody) {
      maxHeightBody = bodyHeight
    }
  }

  function toggleAriaExpanded(card) {
    const accordionToggle = card.querySelector('.accordion-toggle')
    const accordionContent = card.querySelector('.accordion-content')
    const accordionButton = card.querySelector('.card-button a')

    accordionToggle.addEventListener('change', function() {
      if (accordionToggle.checked) {
        // if the accordion is expanded
        accordionToggle.setAttribute('aria-expanded', 'true')
        // Focus on the first element in the expanded content
        accordionContent.querySelector('p').
                         focus()
      } else {
        // if the accordion is collapsed
        accordionToggle.setAttribute('aria-expanded', 'false')
        // Return focus back to the button
        accordionButton.focus()
      }
    })
  }

  cardGroups.forEach(function(cardGroup) {
    maxHeightHeader = 0
    maxHeightBody = 0

    let cards = cardGroup.querySelectorAll('.card')

    cards.forEach(function(card) {
      toggleAriaExpanded(card)
      syncHeights(card)
    })

    // Set CSS variables on the cardGroup container
    cardGroup.style.setProperty('--card-height-header', maxHeightHeader + 'px')
    cardGroup.style.setProperty('--card-height-body', maxHeightBody + 'px')
  })

  // Then use these CSS variables in your stylesheet like this:
  // .accordion-header { height: var(--max-header-height) }
  // .content { height: var(--max-content-height) }
})
