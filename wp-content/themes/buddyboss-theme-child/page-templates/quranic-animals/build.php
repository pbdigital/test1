<?php 
wp_register_script('quranic-animals-js', get_stylesheet_directory_uri() . '/assets/js/quranic-animals.js', '', uniqid(), true);
wp_register_style('quranic-animals-css', get_stylesheet_directory_uri() . '/assets/css/quranic-animals.css', '', uniqid() );


add_action("wp_footer", function(){
    ?>
    <div id="quranic-animal-spin" class="modal">
        <style>
            .spin-animation {
              animation: spin .3s linear infinite;
              animation-play-state: running;
              
            }
            
            @keyframes spin {
              0% {
                transform: rotate(0deg);
              }
              100% {
                transform: rotate(360deg);
              }
            }
        </style>
        <!-- Modal content -->
        <div class="modal-content">            
            
            <h2>What’s the quranic animal you<br/>will unlock?</h2>
            <p>Tap on the mystery box to reveal the<br/>quranic animal you get.</p>

            <svg width="208" height="240" viewBox="0 0 208 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M103.924 240L0 179.999V60.0011L103.924 120V240Z" fill="#FF9500"/>
                <path d="M207.924 179.999L104 240V120L207.924 60.0011V179.999Z" fill="#FFAA00"/>
                <path d="M152.753 167.221V165.778C152.753 159.673 154.507 154.164 160.752 143.119C165.016 135.549 166.185 131.606 166.226 127.443C166.265 122.201 163.621 119.27 158.083 122.468C152.615 125.624 149.844 131.067 149.128 139.166L140.492 144.153C140.811 135.415 144.563 121.684 158.411 113.746C171.017 106.527 175.438 113.106 175.438 122.078C175.438 128.604 173.61 134.601 167.545 145.197C163.54 152.177 162.193 156.284 162.193 160.779V161.774L152.755 167.221H152.753ZM152.753 189.007V174.017L162.19 168.569V183.56L152.753 189.007Z" fill="white"/>
                <path d="M207.885 60.0012L103.963 120L0.0393066 59.9988L103.961 0L207.885 60.0012Z" fill="#FFBF00"/>
                <path d="M86.4436 62.0989L87.6934 61.3759C92.9794 58.3249 98.6281 57.0898 111.317 56.9722C120.005 56.879 124.004 55.9208 127.631 53.8746C132.192 51.2867 133.405 47.5323 127.869 44.3367C122.402 41.1803 116.304 41.5013 108.931 44.9298L100.295 39.9427C108.021 35.8477 121.789 32.2331 135.589 40.2564C148.146 47.5642 144.656 54.6808 136.885 59.1679C131.231 62.4322 125.124 63.8462 112.915 63.8927C104.87 63.9148 100.638 64.7995 96.7436 67.0492L95.8809 67.5466L86.4436 62.0989ZM67.5762 72.9919L80.5596 65.4955L89.997 70.9432L77.0135 78.4397L67.5762 72.9919Z" fill="white"/>
                <path d="M40.7515 159.205V157.762C40.7515 151.657 42.5061 148.175 48.7503 144.337C53.0144 141.69 54.1834 139.098 54.225 134.981C54.2642 129.783 51.62 123.801 46.0816 120.603C40.6142 117.446 37.8426 119.689 37.127 126.96L28.491 121.973C28.8095 113.601 32.5615 104.203 46.41 112.256C59.016 119.593 63.4369 131.275 63.4369 140.25C63.4369 146.776 61.6088 150.66 55.5435 154.255C51.5391 156.612 50.1913 159.161 50.1913 163.655V164.65L40.7539 159.203L40.7515 159.205ZM40.7515 180.991V166.001L50.1888 171.448V186.439L40.7515 180.991Z" fill="white"/>
            </svg>


        </div>

    </div>


    <div id="awarded-quranic-animal" class="modal" onclick="window.location.href='<?=site_url("quranic-animals")?>'">

        <!-- Modal content -->
        <div class="modal-content">            
            <span class="close">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="24" height="24" fill="#B0D178"/>
                    <path d="M18 6L6 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="quranic-animal-content">
                            
                            <div class="animal-image">
                                <div class="num">13</div>
                                <img src="https://journey2jannah.com/wp-content/uploads/2022/10/goat-unlocked.png">
                                <div class="title">Goat</div>
                                <div class="excerpt">Short description about the quranic animals goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit.	</div>
                            </div>

                            <h2>Congratulations!</h2>
                            <p>You have unlocked Goat. 
Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</p>

                        </div>
        </div>

    </div>
    <?php
});
?>