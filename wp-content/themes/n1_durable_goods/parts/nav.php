<?php namespace N1_Durable_Goods; ?>

<nav class="sections">
    <a href="<?php echo home_url() ?>" class="mobile-logo" aria-label="n+1 Home"></a>

    <label for="menu-toggle" class="menu-icon"></label>
    <input type="checkbox" id="menu-toggle" class="menu-checkbox">

    <div class="overlay">
        <?php get_template_part( 'parts/nav/menu' ) ?>

        <?php get_template_part( 'parts/nav/search' ) ?>
    </div>

    <?php get_template_part( 'parts/nav/actions' ) ?>

</nav>

<script>
    document.getElementById('menu-toggle').addEventListener('change', function () {
        if (this.checked) {
            document.body.classList.add('menu-open')
        } else {
            document.body.classList.remove('menu-open')
        }
    })

    // If viewport width is >= 900px, uncheck the menu toggle
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 900) {
            document.getElementById('menu-toggle').checked = false
            document.body.classList.remove('menu-open')
        }
    })

    // Function to calculate the scrollbar width
    function getScrollbarWidth () {
        const outer = document.createElement('div')
        outer.style.visibility = 'hidden'
        outer.style.overflow = 'scroll'
        document.body.appendChild(outer)

        const inner = document.createElement('div')
        outer.appendChild(inner)

        const scrollbarWidth = outer.offsetWidth - inner.offsetWidth

        outer.parentNode.removeChild(outer)
        return scrollbarWidth
    }

    // Update the CSS variable with the calculated scrollbar width
    document.documentElement.style.setProperty('--scrollbar-width', getScrollbarWidth() + 'px')
</script>
