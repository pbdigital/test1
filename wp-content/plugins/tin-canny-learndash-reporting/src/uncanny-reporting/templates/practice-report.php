<?php
namespace uncanny_learndash_reporting;

if ( ! defined( 'WPINC' ) ) {
	die;
}

wp_enqueue_style('login-report-css', get_stylesheet_directory_uri() . '/assets/css/login-report.css', '', mt_rand()); //ENQUEUE_VERSION
wp_enqueue_style('pbd-sa-fullcalendar-css');
wp_enqueue_script('pbd-sa-fullcalendar-js');
wp_enqueue_script('pbd-sa-scripts', PBD_SA_URL . '/assets/js/scripts.js', array(), ENQUEUE_VERSION , true);

?>

<div class="uo-admin-reporting-tab-single" id="practice-report" style="display: <?php echo $current_tab == 'practice-report' ? 'block' : 'none'; ?>">

    <div class="heading heading-login-logs practice">
        <div class="breadcrumb">
            <span>Class Reports</span>
            <span class="spacer">
                <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="active">
                Practice Reports
            </span>
        </div>
        <div class="searchbox">
            <div class="search">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.5 17.5L13.875 13.875" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input type="text" class="search-input" placeholder="Search..."/>
                <button type="button" class="btn-export-csv">CSV Export</button>
            </div>
        </div>
    </div>
    
    <div class="tab practice-logs active">

        

        <div class="sub-filter">
            <label>Practice Read Reporting for</label>
            <div><input type="text" placeholder="Type/Search class" class="sub-filter-search-practice"/></div>
            <label>over the period</label>
            <div class="over-the-period-container-practice">
                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <span class="selected">This Academic Year</span>
                <div class="dropdown">
                    <div class="item">Last Week</div>
                    <div class="item">Last 30 Days</div>
                    <div class="item">Last 90 Days</div>
                    <div class="item">This Academic Year</div>
                </div>
            </div>
        </div>

        <div class="practice-logs-table-container">
            <table id="practice-logs-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th># Of Practice Reads</th>
                        <th>AvG Practice Length</th>
                        <th>Date/Time of last Practice</th>
                        <th>Current Streak</th>
                        <th>Highest Streak</th>
                        <th>Classrooms</th>
                        <!-- Add more table headers as needed -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="tab practice-logs-details">

    </div>

</div>


<?php 
add_action("wp_footer", function(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready( $ => {
            
            let browserTimezone = moment.tz.guess();
            let j2jLoginLogs = {

                details: (userId) => {
                    let detailsTpl = ``;
                    
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/users/practice_logs/${userId}?tz=${browserTimezone}`,
                        dataType: "json",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        success: (d) => {
                            console.log("practice_logs", d)

                            detailsTpl = `
                                
                            `;
                            
                            $(".heading-login-logs.practice .breadcrumb").html(`
                                <span>Class Reports</span>
                                <span class="spacer">
                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span>
                                    <a href="" class="a-login-report">Practice Report</a>
                                </span>
                                <span class="spacer">
                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span><a href="">${d.profile.first_name}</a></span>
                            `);

                            var dateLastPractice = d.summary.date_time_last_practice;
                            var formattedDate = moment(dateLastPractice).format('ddd, Do MMMM YYYY');

                            detailsTpl += `<div class="top">

                                <div class="left">
                                    <div class="card profile">
                                        <div class="label">Profile</div>
                                        <div class="profile-details content">
                                            <img src="${d.profile.avatar}" />
                                            <div>
                                                <div class="name">${d.profile.first_name} ${d.profile.last_name}</div>
                                                <div class="email">${d.profile.email}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="right">
                                    <div class="card practice-summary">
                                        <div class="label">Practice Summary</div>

                                        <div class="table content">
                                            <div class="table-head table-practice-log-head">
                                                <div class=""># of Practice 
                                                        Reads</div>
                                                <div class="">AvG # Practice
                                                        Length</div>
                                                <div class="">Date of last
                                                        Practice</div>
                                                <div class="">Current
                                                    Streak</div>
                                                <div class="">Highest
                                                        Streak</div>
                                            </div>
                                            <div class="table-body table-practice-log-body">
                                                <div class="">${d.summary.number_practice_reads}</div>
                                                <div class="">${d.summary.avg_practice_length} Min</div>
                                                <div class="">${formattedDate}</div>
                                                <div class="">${d.summary.current_streak}</div>
                                                <div class="">${d.summary.highest_streak}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`

                            let list = ``;
                            
                            d.logs.map( log => {
                                var loginDatetime = log.date;
                                var formattedDate = moment(loginDatetime).format('MMMM D, YYYY [at] h:mm a');

                                list += `<div class="list-item">
                                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="50" height="50" rx="14" fill="#5D53C0"/>
                                        <path d="M25.2822 15.4044C25.656 15.0544 26.0802 14.8582 26.6131 14.8609C30.3063 14.8715 33.9968 14.8768 37.69 14.8609C38.0718 14.8609 38.215 14.9722 38.1964 15.3328C38.3449 15.6642 38.3608 16.0168 38.3581 16.3721C38.3502 21.5314 38.3396 26.6907 38.3528 31.85C38.3528 32.2 38.4085 32.5685 38.1779 32.8814C37.9339 33.112 37.6264 33.0696 37.3348 33.0696C33.9783 33.0882 30.6218 33.0696 27.2653 33.0643C26.4912 33.0643 25.7594 33.0829 25.2424 33.7722C25.1947 33.8358 25.1072 33.8517 25.0277 33.8517C24.7307 33.6105 24.8315 33.2711 24.8288 32.9715C24.8023 29.5806 24.8368 26.1923 24.8156 22.8013C24.805 20.9296 24.8076 19.0551 24.8156 17.1834C24.8156 16.791 24.8076 16.3933 24.919 16.0089C24.9508 15.7543 25.0648 15.5475 25.2822 15.4044Z" fill="#F6F7E0"/>
                                        <path d="M23.7557 15.2929C23.7 18.7315 23.7451 22.1702 23.7292 25.6089C23.7212 27.7643 23.7637 29.9171 23.7292 32.0699C23.7212 32.3907 23.7106 32.7089 23.4269 32.9316C23.1989 33.0827 22.9391 33.0721 22.682 33.0721C19.3626 33.0801 16.0406 33.0642 12.7186 33.0536C12.3766 33.0536 11.9921 33.141 11.7456 32.7725C11.5573 32.1733 11.6554 31.5583 11.6554 30.9511C11.6554 26.0516 11.6448 21.1521 11.6607 16.2553C11.6607 15.9186 11.6395 15.5792 11.8278 15.277C11.8755 15.0967 11.7588 14.8475 12.138 14.8501C15.0914 14.8793 18.0396 14.8846 20.9878 14.8978C21.5393 14.8369 22.0881 14.8448 22.6369 14.8952C22.9762 14.8395 23.3182 14.8422 23.6576 14.9031C23.761 15.0145 23.7584 15.1524 23.7557 15.2929Z" fill="#F6F7E0"/>
                                        <path d="M11.8247 15.2742C11.8326 19.0363 11.8406 22.7984 11.8459 26.5605C11.8485 28.6099 11.8459 30.6593 11.8459 32.7088C11.8962 33.1807 12.2568 33.1727 12.5935 33.1807C13.7389 33.2098 14.8842 33.1886 16.0295 33.1913C18.1611 33.1913 20.2954 33.1913 22.427 33.1913C23.111 33.1913 23.6996 33.3663 24.2007 33.8567C24.6275 34.2756 25.4441 34.2597 25.8577 33.817C26.2845 33.361 26.7803 33.1939 27.3901 33.1939C30.6485 33.186 33.9069 33.2072 37.1652 33.186C37.5285 33.186 37.9394 33.2788 38.1409 32.8254C38.1488 28.1539 38.1541 23.4824 38.1648 18.8136C38.1674 17.6523 38.1886 16.4938 38.1992 15.3325C38.8064 15.2583 39.0052 15.5181 38.9999 16.1332C38.9734 21.8015 38.9734 27.4672 38.9654 33.1356C38.9654 33.8674 38.9098 33.9257 38.1462 33.923C34.7897 33.9204 31.4359 33.9204 28.0794 33.8965C27.5757 33.8939 27.2416 33.9707 27.0481 34.5143C26.8652 35.0286 26.4277 35.3176 25.8444 35.3176C25.2532 35.3176 24.6593 35.3441 24.0681 35.3202C23.5193 35.299 22.9122 34.8165 22.8353 34.2889C22.7822 33.9257 22.6179 33.8992 22.3289 33.8992C20.2954 33.9071 18.2619 33.9098 16.2284 33.9071C14.7463 33.9071 13.2669 33.8673 11.7875 33.8965C11.1804 33.9098 11.0054 33.6924 11.0134 33.1064C11.0372 30.9139 11.0187 28.7213 11.016 26.5314C11.0134 23.09 11.0293 19.6567 11.0001 16.2207C10.9948 15.5844 11.1645 15.2556 11.8247 15.2742Z" fill="#B0D178"/>
                                        <path d="M23.3496 32.8491C23.5193 32.6609 23.5405 32.4409 23.5431 32.1916C23.5617 29.3389 23.5087 26.4862 23.5564 23.6334C23.5988 21.1333 23.5299 18.6332 23.514 16.1331C23.5113 15.7168 23.5431 15.3059 23.6598 14.9029C24.1105 14.8525 24.4048 15.1335 24.7044 15.3987C25.0517 15.4331 24.9854 15.746 25.0756 15.9554C24.996 17.5859 25.0331 19.2165 25.0278 20.847C25.0199 22.6153 25.0358 24.3837 25.0172 26.1521C24.9934 28.4852 25.0172 30.8209 25.0172 33.154C25.0172 33.39 25.0305 33.6233 25.0385 33.8593C24.996 34.0846 24.8529 33.9547 24.8131 33.8937C24.487 33.3926 24.0045 33.1408 23.4504 32.987C23.3973 32.9711 23.3682 32.9101 23.3496 32.8544V32.8491Z" fill="#E0E3CD"/>
                                        <path d="M25.0733 15.9533C24.8135 15.8578 24.8559 15.5635 24.7021 15.3965C24.8983 15.2719 25.0892 15.3938 25.2828 15.4045C25.2138 15.5874 25.1449 15.7703 25.0733 15.9506V15.9533Z" fill="#B0D178"/>
                                        <path d="M23.35 32.8495C24.0818 32.884 24.6571 33.1623 24.9514 33.8782C24.9567 33.8888 24.9832 33.8967 24.9964 33.8941C25.0123 33.8888 25.0256 33.8702 25.0389 33.857C25.4074 33.1146 26.0013 32.8601 26.8258 32.8681C30.4209 32.8972 34.016 32.8813 37.6137 32.8787C37.7887 32.8787 37.9637 32.8442 38.1386 32.8256C38.2712 33.3638 37.9478 33.393 37.5421 33.393C34.1088 33.3851 30.6727 33.393 27.2394 33.3824C26.6959 33.3824 26.2876 33.5706 25.9615 33.9869C25.5824 34.4668 24.445 34.4774 24.0685 34.0107C23.7238 33.5839 23.2811 33.3851 22.727 33.3851C19.3122 33.3851 15.8974 33.3612 12.4826 33.3851C11.9205 33.3904 11.7217 33.2525 11.8463 32.709C11.9364 32.937 12.1432 32.8707 12.3049 32.8707C15.9292 32.8787 19.5534 32.8787 23.175 32.8787C23.2334 32.8787 23.2917 32.8601 23.35 32.8495Z" fill="#E0E3CD"/>
                                        <path d="M13.7073 17.2548C15.7806 17.2548 17.8512 17.2574 19.9245 17.276C20.3937 17.2813 20.3009 16.9976 20.285 16.759C20.2081 16.5257 20.0358 16.531 19.882 16.531C17.83 16.5283 15.7779 16.531 13.7258 16.531C13.4872 16.531 13.3069 16.5734 13.3096 16.8915C13.3096 17.1938 13.4475 17.2548 13.7099 17.2548H13.7073Z" fill="#E0E3CD"/>
                                        <path d="M13.8 30.2513C15.4756 30.2645 17.1511 30.2645 18.8267 30.2672C20.049 30.2672 21.2712 30.2619 22.496 30.2592C22.7108 30.2592 22.9521 30.3122 22.9468 29.9649C22.785 29.8058 22.5756 29.8403 22.39 29.8403C19.532 29.835 16.6713 29.8324 13.8132 29.8403C13.6542 29.8403 13.3599 29.7237 13.3546 30.0471C13.3493 30.3865 13.6409 30.2486 13.8026 30.2486L13.8 30.2513Z" fill="#E0E3CD"/>
                                        <path d="M22.5197 28.5516C20.237 28.5383 17.9516 28.5224 15.6689 28.5171C14.961 28.5171 14.2531 28.5356 13.4949 28.5436C13.4021 28.5595 13.3411 28.6523 13.357 28.7875C13.3756 28.9519 13.5161 28.9439 13.6274 28.9466C14.041 28.9545 14.4546 28.9545 14.8682 28.9545C17.4293 28.9545 19.9878 28.9572 22.5489 28.9545C22.7053 28.9545 22.9519 29.0208 22.9333 28.7292C22.9147 28.4402 22.6814 28.5542 22.5224 28.5516H22.5197Z" fill="#E0E3CD"/>
                                        <path d="M22.5007 31.1658C19.6373 31.1525 16.7767 31.1498 13.9133 31.1525C13.7171 31.1525 13.3963 31.065 13.4096 31.3858C13.4202 31.6589 13.7171 31.5555 13.8948 31.5581C15.6128 31.5687 17.3308 31.5714 19.0461 31.5767C20.1915 31.5793 21.3368 31.5847 22.4795 31.582C22.673 31.582 22.9488 31.6801 22.9488 31.301C22.8666 31.0624 22.6465 31.1711 22.498 31.1684L22.5007 31.1658Z" fill="#E0E3CD"/>
                                        <path d="M13.7228 26.0462C15.3984 26.0594 17.074 26.0594 18.7496 26.0621C19.9718 26.0621 21.194 26.0568 22.4189 26.0541C22.6337 26.0541 22.8749 26.1072 22.8696 25.7598C22.7079 25.6008 22.4984 25.6352 22.3129 25.6352C19.4548 25.6299 16.5941 25.6273 13.7361 25.6352C13.577 25.6352 13.2827 25.5186 13.2774 25.842C13.2721 26.1814 13.5637 26.0435 13.7255 26.0435L13.7228 26.0462Z" fill="#E0E3CD"/>
                                        <path d="M22.4426 24.347C20.1598 24.3337 17.8745 24.3178 15.5917 24.3125C14.8839 24.3125 14.176 24.3311 13.4177 24.339C13.3249 24.3549 13.264 24.4477 13.2799 24.5829C13.2984 24.7473 13.4389 24.7394 13.5503 24.742C13.9639 24.75 14.3775 24.75 14.7911 24.75C17.3522 24.75 19.9106 24.7526 22.4717 24.75C22.6281 24.75 22.8747 24.8162 22.8562 24.5246C22.8376 24.2356 22.6043 24.3496 22.4452 24.347H22.4426Z" fill="#E0E3CD"/>
                                        <path d="M13.8212 27.3533C15.5392 27.3639 17.2572 27.3666 18.9726 27.3719C20.1179 27.3746 21.2633 27.3799 22.406 27.3772C22.5995 27.3772 22.8752 27.4753 22.8752 27.0962C22.793 26.8576 22.573 26.9663 22.4245 26.9636C19.5612 26.9504 16.7005 26.9477 13.8371 26.9504C13.641 26.9504 13.3201 26.8629 13.3334 27.1837C13.344 27.4567 13.641 27.3533 13.8186 27.356L13.8212 27.3533Z" fill="#E0E3CD"/>
                                        <path d="M13.6486 21.9051C15.3242 21.9183 16.9998 21.9183 18.6754 21.921C19.8976 21.921 21.1198 21.9157 22.3447 21.913C22.5594 21.913 22.8007 21.966 22.7954 21.6187C22.6337 21.4597 22.4242 21.4941 22.2386 21.4941C19.3806 21.4888 16.5199 21.4862 13.6619 21.4941C13.5028 21.4941 13.2085 21.3775 13.2032 21.7009C13.1979 22.0403 13.4895 21.9024 13.6513 21.9024L13.6486 21.9051Z" fill="#E0E3CD"/>
                                        <path d="M22.3683 20.2054C20.0856 20.1921 17.8002 20.1762 15.5175 20.1709C14.8096 20.1709 14.1018 20.1868 13.3435 20.1974C13.2507 20.2133 13.1897 20.3061 13.2056 20.4413C13.2242 20.6057 13.3647 20.5977 13.4761 20.6004C13.8897 20.6084 14.3033 20.6084 14.7169 20.6084C17.278 20.6084 19.8364 20.611 22.3975 20.6084C22.5539 20.6084 22.8005 20.6746 22.7819 20.383C22.7634 20.094 22.5301 20.208 22.371 20.2054H22.3683Z" fill="#E0E3CD"/>
                                        <path d="M13.4471 19.1795C13.8606 19.1875 14.2742 19.1875 14.6878 19.1875C17.2489 19.1875 19.8074 19.1901 22.3685 19.1875C22.5249 19.1875 22.7715 19.2537 22.7529 18.9621C22.7344 18.6731 22.501 18.7871 22.342 18.7845C20.0593 18.7712 17.7739 18.7553 15.4912 18.75C14.7833 18.75 14.0754 18.7686 13.3171 18.7765C13.2243 18.7924 13.1634 18.8852 13.1793 19.0204C13.1978 19.1848 13.3384 19.1769 13.4497 19.1795H13.4471Z" fill="#E0E3CD"/>
                                        <path d="M13.7441 23.2122C15.4621 23.2228 17.1801 23.2255 18.8955 23.2308C20.0408 23.2334 21.1861 23.2387 22.3288 23.2361C22.5224 23.2361 22.7981 23.3342 22.7981 22.9551C22.7159 22.7164 22.4958 22.8251 22.3474 22.8225C19.484 22.8092 16.6233 22.8066 13.76 22.8092C13.5638 22.8092 13.243 22.7217 13.2563 23.0425C13.2669 23.3156 13.5638 23.2122 13.7414 23.2149L13.7441 23.2122Z" fill="#E0E3CD"/>
                                        <path d="M27.4319 17.2548C29.5052 17.2548 31.5758 17.2574 33.6491 17.276C34.1183 17.2813 34.0255 16.9976 34.0096 16.759C33.9327 16.5257 33.7604 16.531 33.6066 16.531C31.5546 16.5283 29.5025 16.531 27.4505 16.531C27.2118 16.531 27.0316 16.5734 27.0342 16.8915C27.0342 17.1938 27.1721 17.2548 27.4345 17.2548H27.4319Z" fill="#E0E3CD"/>
                                        <path d="M27.5246 30.2513C29.2002 30.2645 30.8758 30.2645 32.5513 30.2672C33.7736 30.2672 34.9958 30.2619 36.2207 30.2592C36.4354 30.2592 36.6767 30.3122 36.6714 29.9649C36.5096 29.8058 36.3002 29.8403 36.1146 29.8403C33.2566 29.835 30.3959 29.8324 27.5378 29.8403C27.3788 29.8403 27.0845 29.7237 27.0792 30.0471C27.0739 30.3865 27.3655 30.2486 27.5272 30.2486L27.5246 30.2513Z" fill="#E0E3CD"/>
                                        <path d="M36.2424 28.5516C33.9596 28.5383 31.6743 28.5224 29.3916 28.5171C28.6837 28.5171 27.9758 28.5356 27.2175 28.5436C27.1247 28.5595 27.0638 28.6523 27.0797 28.7875C27.0982 28.9519 27.2387 28.9439 27.3501 28.9466C27.7637 28.9545 28.1773 28.9545 28.5909 28.9545C31.152 28.9545 33.7104 28.9572 36.2715 28.9545C36.4279 28.9545 36.6745 29.0208 36.656 28.7292C36.6374 28.4402 36.4041 28.5542 36.245 28.5516H36.2424Z" fill="#E0E3CD"/>
                                        <path d="M36.2233 31.1658C33.36 31.1525 30.4993 31.1498 27.636 31.1525C27.4398 31.1525 27.119 31.065 27.1322 31.3858C27.1428 31.6589 27.4398 31.5555 27.6174 31.5581C29.3354 31.5687 31.0534 31.5714 32.7688 31.5767C33.9141 31.5793 35.0595 31.5847 36.2021 31.582C36.3957 31.582 36.6714 31.6801 36.6714 31.301C36.5892 31.0624 36.3692 31.1711 36.2207 31.1684L36.2233 31.1658Z" fill="#E0E3CD"/>
                                        <path d="M27.4484 26.0462C29.124 26.0594 30.7996 26.0594 32.4752 26.0621C33.6974 26.0621 34.9196 26.0568 36.1445 26.0541C36.3592 26.0541 36.6005 26.1072 36.5952 25.7598C36.4335 25.6008 36.224 25.6352 36.0384 25.6352C33.1804 25.6299 30.3197 25.6273 27.4617 25.6352C27.3026 25.6352 27.0083 25.5186 27.003 25.842C26.9977 26.1814 27.2893 26.0435 27.4511 26.0435L27.4484 26.0462Z" fill="#E0E3CD"/>
                                        <path d="M36.1652 24.347C33.8825 24.3337 31.5971 24.3178 29.3144 24.3125C28.6065 24.3125 27.8986 24.3311 27.1404 24.339C27.0476 24.3549 26.9866 24.4477 27.0025 24.5829C27.0211 24.7473 27.1616 24.7394 27.2729 24.742C27.6865 24.75 28.1001 24.75 28.5137 24.75C31.0748 24.75 33.6333 24.7526 36.1944 24.75C36.3508 24.75 36.5974 24.8162 36.5788 24.5246C36.5603 24.2356 36.3269 24.3496 36.1679 24.347H36.1652Z" fill="#E0E3CD"/>
                                        <path d="M27.5439 27.3533C29.2619 27.3639 30.9799 27.3666 32.6953 27.3719C33.8406 27.3746 34.9859 27.3799 36.1286 27.3772C36.3222 27.3772 36.5979 27.4753 36.5979 27.0962C36.5157 26.8576 36.2956 26.9663 36.1472 26.9636C33.2838 26.9504 30.4231 26.9477 27.5598 26.9504C27.3636 26.9504 27.0428 26.8629 27.0561 27.1837C27.0667 27.4567 27.3636 27.3533 27.5412 27.356L27.5439 27.3533Z" fill="#E0E3CD"/>
                                        <path d="M27.3742 21.9051C29.0498 21.9183 30.7254 21.9183 32.4009 21.921C33.6232 21.921 34.8454 21.9157 36.0703 21.913C36.285 21.913 36.5263 21.966 36.521 21.6187C36.3593 21.4597 36.1498 21.4941 35.9642 21.4941C33.1062 21.4888 30.2455 21.4862 27.3874 21.4941C27.2284 21.4941 26.9341 21.3775 26.9288 21.7009C26.9235 22.0403 27.2151 21.9024 27.3768 21.9024L27.3742 21.9051Z" fill="#E0E3CD"/>
                                        <path d="M36.091 20.2054C33.8083 20.1921 31.5229 20.1762 29.2402 20.1709C28.5323 20.1709 27.8244 20.1868 27.0662 20.1974C26.9734 20.2133 26.9124 20.3061 26.9283 20.4413C26.9469 20.6057 27.0874 20.5977 27.1987 20.6004C27.6123 20.6084 28.0259 20.6084 28.4395 20.6084C31.0006 20.6084 33.5591 20.611 36.1202 20.6084C36.2766 20.6084 36.5231 20.6746 36.5046 20.383C36.486 20.094 36.2527 20.208 36.0936 20.2054H36.091Z" fill="#E0E3CD"/>
                                        <path d="M27.1697 19.1795C27.5833 19.1875 27.9969 19.1875 28.4105 19.1875C30.9716 19.1875 33.53 19.1901 36.0911 19.1875C36.2476 19.1875 36.4941 19.2537 36.4756 18.9621C36.457 18.6731 36.2237 18.7871 36.0646 18.7845C33.7819 18.7712 31.4965 18.7553 29.2138 18.75C28.5059 18.75 27.7981 18.7686 27.0398 18.7765C26.947 18.7924 26.886 18.8852 26.9019 19.0204C26.9205 19.1848 27.061 19.1769 27.1724 19.1795H27.1697Z" fill="#E0E3CD"/>
                                        <path d="M27.4697 23.2122C29.1877 23.2228 30.9057 23.2255 32.621 23.2308C33.7664 23.2334 34.9117 23.2387 36.0544 23.2361C36.2479 23.2361 36.5237 23.3342 36.5237 22.9551C36.4415 22.7164 36.2214 22.8251 36.073 22.8225C33.2096 22.8092 30.3489 22.8066 27.4856 22.8092C27.2894 22.8092 26.9686 22.7217 26.9818 23.0425C26.9924 23.3156 27.2894 23.2122 27.467 23.2149L27.4697 23.2122Z" fill="#E0E3CD"/>
                                    </svg>

                                    <div class="date">
                                        ${formattedDate}
                                        <div class="mins">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 3.8V8L10.8 9.4M15 8C15 11.866 11.866 15 8 15C4.13401 15 1 11.866 1 8C1 4.13401 4.13401 1 8 1C11.866 1 15 4.13401 15 8Z" stroke="#B0D178" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            ${log.minutes}m
                                        </div>
                                    </div>
                                </div>`
                            })

                            detailsTpl += `
                            <div class="bottom">
                                <div class="card">
                                    <div class="label">Practice Logs</div>
                                    <div class="content">
                                        <div class="login-logs">
                                            <div class="lists">
                                                ${list}
                                            </div>
                                            <div class="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `

                            $(".tab.practice-logs-details").html(detailsTpl).addClass("active")
                            $(".tab.practice-logs").removeClass('active');

                            /* practice tracker calendar */
                            $.ajax({
                                url: safarObject.ajaxurl,
                                data: {
                                    action: "practice_tracker_calendar_report_practice_logs",
                                    userid: userId
                                },
                                beforeSend: () => {

                                },
                                success: (d) => {
                                    // popup full calendar
                                    $(".practice-logs-details .calendar").html(d).css("opacity",0)

                                    setTimeout( () => {
                                        $('.practice-logs-details .view-full-calendar').trigger('click');
                                    }, 100)

                                    setTimeout( () => {
                                        $(".practice-logs-details .calendar").css("opacity",1)
                                    }, 500)
                                }
                            });
                            /* end practice tracker calendar*/
                        }
                    });

                   
                }
            }

            function convertToCSV(jsonData) {
                // Extract the keys from the first object as column headers
                const keys = Object.keys(jsonData[0]);

                // Build the CSV content
                let csv = keys.map(key => `"${key}"`).join(",") + "\n";
                jsonData.forEach(obj => {
                    const row = keys.map(key => `"${obj[key]}"`);
                    csv += row.join(",") + "\n";
                });

                return csv;
            }

            function downloadCSV(csvContent) {
                // Create a temporary anchor element
                const link = document.createElement("a");
                link.href = "data:text/csv;charset=utf-8," + encodeURIComponent(csvContent);
                link.download = "practice_logs.csv";

                // Trigger the download
                link.click();
            }
            let csvContent = "";
            $(document).on('click','.btn-export-csv', function() {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/users/practice_logs`,
                    data: {
                        csv:1,
                        tz:browserTimezone
                    },
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    dataType: "json",
                    beforeSend: () => {

                    },
                    success: (d) => {
                        csvContent = convertToCSV(d.data);
                        downloadCSV(csvContent);
                    }
                    
                });

            });

            $(document).on("click",".heading.practice .breadcrumb a.a-login-report", e => {
                e.preventDefault();
                $(".tab.practice-logs-details").removeClass("active")
                $(".tab.practice-logs").addClass('active')

                $(".heading-login-logs.practice .breadcrumb").html(`
                    <span>Class Reports</span>
                    <span class="spacer">
                        <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="active">
                        Practice Report
                    </span>
                `);

            })

            $(document).on("click",".over-the-period-container-practice", e => {
                $(e.currentTarget).find(".dropdown").toggleClass("active");
            });

            $(document).on("click", e => {
                setTimeout( () => {
                    if(!$(e.target).hasClass("selected")){
                        if( $(".over-the-period-container-practice .dropdown").hasClass('active') ) {
                            $(".over-the-period-container-practice .dropdown").removeClass("active")
                        }
                    }
                }, 0)
                
            });

            $(document).on("click",".over-the-period-container-practice .dropdown .item", e => {
                let selected = $(e.currentTarget).text();
                $(".over-the-period-container-practice .selected").text(selected);
                $('#practice-logs-table').DataTable().ajax.url(`${safarObject.apiBaseurl}/users/practice_logs?period=${selected}&tz=${browserTimezone}`).load();
            });

           
            var selectedPeriod = $('.over-the-period').text();

            $('#practice-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${safarObject.apiBaseurl}/users/practice_logs?period=${selectedPeriod}&tz=${browserTimezone}`,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    }
                },
                columns: [
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'number_practice_reads' },
                    { data: 'avg_practice_length' },
                    { data: 'date_time_last_practice' },
                    { data: 'current_streak' },
                    { data: 'highest_streak' },
                    { data: 'classrooms' },
                    // Add more column definitions as needed
                ],
                order: [[4, 'desc']], // Set default sorting for the 4
                searching: true, // Hide the search box
                rowCallback: function(row, data) {
                    $(row).on('click', function() {
                        // Handle row click event
                        // Access the data for the clicked row using the 'data' parameter
                        console.log(data.user_id);

                        j2jLoginLogs.details(data.user_id);
                        
                    });

                    var classrooms = data.classrooms;

                    // Create an empty string to hold the classroom names
                    var classroomNames = '';

                    // Loop through each classroom in the array
                    for (var i = 0; i < classrooms.length; i++) {
                        // Append the classroom name to the string
                        if(classrooms[i].bg_color.length > 0 ){
                            classroomNames += `<span style='background-color:${classrooms[i].bg_color};
                                                        font-size: 11px;
                                                        border-radius: 5px;
                                                        margin-right: 5px;
                                                        color: #fff;
                                                        padding: 3px 5px;
                                                        display: -webkit-inline-box;
                                                        display: -ms-inline-flexbox;
                                                        display: inline-flex;
                                                        margin-bottom: 5px;
                                                        font-weight:normal;
                            '>${classrooms[i].name}</span>`;
                        }

                        // Add a comma after each classroom name, except for the last one
                        if (i < classrooms.length - 1) {
                            classroomNames += ' ';
                        }
                    }

                    //console.log("classroom names", classroomNames)
                    // Set the classroom names as the content of the corresponding cell in the DataTable
                    $('td:eq(7)', row).html(`<div style="max-width:200px">${classroomNames}<div>`);
                },
                initComplete: function() {
                    // Enclose info and pagination elements within a container
                    $('#practice-logs-table_wrapper .dataTables_info, #practice-logs-table_wrapper .dataTables_paginate')
                        .wrapAll('<div class="datatable-footer"></div>');

                    // Move the container to the desired location
                    $('#datatable-footer').appendTo('#datatable-pagination');

                    $('#practice-report .search-input').on('keyup', function() {
                        var search = $(this).val();
                        $('#practice-logs-table').DataTable().search(search).draw();
                    });
                    $('.sub-filter-search-practice').on('keyup', function() {
                        var search = $(this).val();
                        //$('#login-logs-table').DataTable().search(search).draw();
                        let selectedPeriod = $(".over-the-period-container-practice .dropdown .item").text();
                        $('#practice-logs-table').DataTable().ajax.url(`${safarObject.apiBaseurl}/users/practice_logs?period=${selectedPeriod}&search_class=${search}&tz=${browserTimezone}`).load();
                    });
                    
                    var lengthElement = $('.practice-logs-table-container .dataTables_length');
                    var paginateElement = $('.practice-logs-table-container .dataTables_info');
                    lengthElement.insertAfter(paginateElement);

                    $(".practice-logs-table-container .dataTables_wrapper > .dataTables_length").remove();
                }
                
            });
        })
    </script>
    <?php
}, 999);
?>