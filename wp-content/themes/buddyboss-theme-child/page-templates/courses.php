<?php

/** Template Name: Courses */
wp_enqueue_style( 'courses-css', get_stylesheet_directory_uri() .'/assets/css/courses.css',array(), time() );
wp_enqueue_script("courses-js");

get_header();
$user = wp_get_current_user();
 

?>
<main id="main" class="site-main">
    <div class="courses-main">
        <div class="courses-filter">
            <div class="courses-filter__inner">
            <div class="searchbar">        
                <input type="text" name="search" class="input-search" placeholder="Search..." autocomplete="off" />
                <div class="filters">
                    <a href="#" class="filter-menu status">
                        <span data-val="">Status</span>
                        <ul>
                            <li class="disabled optstatus" data-val="">Status</li>
                            <li class="optstatus" data-val="not_started">Not Yet Started</li>
                            <li class="optstatus" data-val="in_progress">In Progress</li>
                            <li class="optstatus" data-val="completed">Complete</li>
                        </ul> 
                    </a> 
                   
                    <a href="#" class="filter-menu subject">
                        <span data-val="">Subject</span>
                        <ul class="categories-dp">
                            
                        </ul>
                    </a>
                   
                </div>
            </div>
            <!-- end searchbar -->
               
            </div>
        </div>

        <div class="courses-container">
        <?php if ($_GET['result']): ?>
            <div class="courses-query">
                Showing results for “<span class="courses-query__string">Islamic</span>”
            </div>
            <?php if ($_GET['noresult']): ?>
                <div class="noresult">
                    <img src="<?= get_stylesheet_directory_uri(); ?>/assets/img/no-result.png" alt="No result">
                    <h3>No results found</h3>
                    <p>Try adjusting your search or filter to find what you’re looking for.</p>
                </div>
            <?php else: ?>
            <div class="courses-result">
                
                <div class="courses-result__items">
                    <?php for($x=0;$x<2;$x++): ?>
                    <div class="courses-result__item">
                        <div class="courses-result__tag"><span class="complete">Complete</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/Textbook-1.png" alt="">
                        </a>
                    </div>
                    <div class="courses-result__item">
                        <div class="courses-result__tag"><span class="inprogress">In Progress</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-2.png" alt="">
                        </a>
                    </div>
                    <div class="courses-result__item">
                        <div class="courses-result__tag"><span class="start">Start Course</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-3.png" alt="">
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
        <div class="courses-wrapper">
            <div class="courses-list">
                <h2>Islamic Studies</h2>
                <div class="courses__slider">
                    <?php for($x=0;$x<5;$x++): ?>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="complete">Complete</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/Textbook-1.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="inprogress">In Progress</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-2.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="start">Start Course</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-3.png" alt="">
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="courses-list">
                <h2>Learn To Read</h2>
                <div class="courses__slider">
                    <?php for($x=0;$x<5;$x++): ?>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="complete">Complete</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/Textbook-1.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="inprogress">In Progress</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-2.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="start">Start Course</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-3.png" alt="">
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="courses-list">
                <h2>Learn By Heart</h2>
                <div class="courses__slider">
                    <?php for($x=0;$x<5;$x++): ?>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="complete">Complete</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/Textbook-1.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="inprogress">In Progress</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-2.png" alt="">
                        </a>
                    </div>
                    <div class="courses__slider-item">
                        <div class="courses__slider-tag"><span class="start">Start Course</span></div>
                        <a href="#"><svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="30" fill="#fff"/><path d="m25.714 22.286 12 7.714-12 7.714V22.286Z" fill="#5D53C0"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.893 20.78c.55-.3 1.22-.276 1.748.064l12 7.714a1.714 1.714 0 0 1 0 2.884l-12 7.714A1.714 1.714 0 0 1 24 37.714V22.286c0-.628.342-1.205.893-1.505Zm.821 1.506v15.428l12-7.714-12-7.714Z" fill="#5D53C0"/></svg>
                            <img src="<?=site_url();?>/wp-content/uploads/2022/07/TextBook-3.png" alt="">
                        </a>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        </div>
    </div>
    
</main>
<?php 
add_action("wp_footer", function(){
    
    ?>
    
    <script>
        jQuery(document).ready(function () {
            $(document).on("click", ".filter-menu", e => {
                e.preventDefault();
                e.stopPropagation();
                let $this = $(e.currentTarget);       

                // hide all open dropdowns      
                $('.filter-menu ul').hide();
                $('.filter-menu').not($this).removeClass('active');

                $this.toggleClass('active');
                if($this.hasClass('active')) {    
                    
                    $this.find('ul').show();

                } else {                     
                    $this.find('ul').hide();
                }     
                
                


            });   
            $(document).on("click", ".filter-menu li", e => {
            
                let $this = $(e.currentTarget);     
                let option = $this.attr('data-val');
                let option_name = $this.text();

                page = 1;
                
                console.log($this)
                
                if($this.hasClass('optstatus')) {            
                
                    status = option;
                    $(".filter-menu.status span").text(option_name).attr("data-val",$this.attr("data-val"));
                }            
                else if($this.hasClass('optsubject')) {            
                    resource_type = option;
                    $(".filter-menu.subject span").text(option_name).attr("data-val",$this.attr("data-val"));
                }

                SafarCourses.filters.status = $("a.filter-menu.status span").attr("data-val");
                SafarCourses.filters.subject = $("a.filter-menu.subject span").attr("data-val");
                SafarCourses.displayCourses();
                
            });  
            

            $(document).on("click", e => {
                // hide all open dropdowns      
                $('.filter-menu').removeClass('active');
                $('.filter-menu ul').hide();        
            });

            
            let slickLoaded = "";
            coursesSliderSlick = () => {
                 
                slickLoaded = $('.courses__slider').slick({
                    dots: false,
                    infinite: false,
                    speed: 300,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    prevArrow: '<button class="slick-prev"><svg width="40" height="40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle r="18.5" transform="matrix(-1 0 0 1 20 20)" fill="#F2A952" stroke="#EBCE99" stroke-width="3"/><path d="m22.5 25-5-5 5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
                    nextArrow: '<button class="slick-next"><svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="20" cy="20" r="18.5" fill="#F2A952" stroke="#EBCE99" stroke-width="3"/><path d="m17.5 25 5-5-5-5" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
                    responsive: [
                        {
                        breakpoint: 901,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                        },
                        {
                        breakpoint: 601,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                        }
                    ]
                });
            }

            
            // coures.js
            let subjectsInterval = "";
            SafarCourses.loadCategories();

            <?php 
            if(!empty($_GET["subject"])){
                ?>
                let urlSubject = "<?=$_GET["subject"]?>";
                subjectsInterval = setInterval( e => {
                    console.log("SafarCourses.loadedSubjects", SafarCourses.loadedSubjects)
                    if( SafarCourses.loadedSubjects ){
                        clearInterval(subjectsInterval)
                        
                        $(".optsubject").each( function(){

                            if( $(this).text().toLowerCase() == urlSubject.toLowerCase() ){
                                
                                $(".filter-menu.subject span").text( $(this).text() ).attr("data-val",$(this).attr("data-val"));
                                SafarCourses.filters.subject = $(this).attr("data-val");
                                SafarCourses.displayCourses();
                                
                            }
                        });
                    }
                }, 1000)
                <?php
            }else{
                ?>SafarCourses.displayCourses();<?php
            }
            ?>
            
            let searchTimeout = "";
            $(document).on("keyup",".input-search", e => {
                if(searchTimeout!="") clearTimeout(searchTimeout)
                sk = $(e.currentTarget).val();
                searchTimeout = setTimeout( () => {
                    SafarCourses.filters.searchkey = sk;
                    SafarCourses.displayCourses();
                }, 500)

            });

        });
    </script>
    <?php
}, 999);

get_footer();

?>