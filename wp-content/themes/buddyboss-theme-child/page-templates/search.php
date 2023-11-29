<?php

/** Template Name: Search */
wp_enqueue_style( 'search-css', get_stylesheet_directory_uri() .'/assets/css/search.css',array(), time() );
wp_enqueue_script( 'search-js');

get_header();
$user = wp_get_current_user();
?>
<main class="search">
    <div class="search-inner">
        <div class="search-keyword">
            <?php 
            if(isset($_GET["search"])){
            ?>
            Showing lesson results for “<span class="word"><?=$_GET["search"]?></span>”
            <?php 
            }
            ?>
        </div>
     
        <div class="noresult" style="display:none">
            <img src="<?=get_stylesheet_directory_uri();?>/assets/img/no-result.png" alt="No result">
            <h2>Sorry, there were no results found.</h2>
            <p>Try adjusting your search and use a different keyword.</p>
        </div>
       
        <div class="search-results">
            <div class="search-items">
                <?php /* for($x=1;$x<=9;$x++): ?>
                <div class="search-item">
                    <a href="#">
                        <span class="search-item__title">Textbook 1 - part <?=$x;?></span>
                        <img src="https://journey2jannah.com/wp-content/uploads/2022/09/image.png" alt="">
                    </a>
                </div>
                <?php endfor; */ ?>
            </div>
        </div>
         
    </div>
</main>
<?php 
add_action("wp_footer", function(){
    
    ?>
    
    <script>
    jQuery(document).ready( e => {
        SafarSearch.searchKey = "<?=$_GET["search"]?>";
        SafarSearch.init();

        $(window).scroll(function() {
            if($(window).scrollTop() + $(window).height() == $(document).height()) {
                if(!SafarSearch.xhrLoading){
                    if(SafarSearch.loadedAll) SafarSearch.loadSearchResult();
                }
            }
        });

    })
        
    </script>
    <?php
}, 999);

get_footer();

?>