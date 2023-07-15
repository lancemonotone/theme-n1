<?php namespace N1_Durable_Goods;

$value       = $_GET['s'] ?? '';
$home_url    = esc_url( home_url( '/' ) );
$search_icon = /** @lang HTML */
	<<<EOD
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
    <path d="M507.3 474.7L377.1 344.5c-1.8-1.8-4.3-2.9-6.9-2.9h-11.1c21.5-26.9 34.5-60.7 34.5-97.5C393.6 113.9 307.7 28 201.8 28S10 113.9 10 219.8s85.9 191.8 191.8 191.8c36.8 0 70.6-10.3 97.5-34.5v11.1c0 2.6 1.1 5.1 2.9 6.9l130.2 130.2c7.6 7.6 19.8 7.6 27.4 0l27.4-27.4c7.6-7.6 7.6-19.8 0-27.4zM201.8 383.6c-88.9 0-161.8-72.9-161.8-161.8S112.9 60 201.8 60s161.8 72.9 161.8 161.8-72.9 161.8-161.8 161.8z"/>
</svg>
EOD;
?>

<div class="nav-search">
    <form class="search toggle-section" action="<?php echo $home_url ?>">
        <label class="visually-hidden" for="s">Search n+1</label>
        <input name="s"
               id="s"
               type="text"
               data-swplive="true"
               value="<?php echo $value ?>"
               placeholder="Search n+1"
               autocomplete="off"
               autocapitalize="off"
               spellcheck="false"/>
        <button type="submit" aria-label="Submit Search">
            <?php echo $search_icon ?>
        </button>
    </form>
</div>
