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

<div class="uo-admin-reporting-tab-single" id="login-report" style="display: <?php echo $current_tab == 'login-report' ? 'block' : 'none'; ?>">

    <div class="heading heading-login-logs login">
        <div class="breadcrumb">
            <span>Class Reports</span>
            <span class="spacer">
                <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="active">
                Login Reports
            </span>
        </div>
        <div class="searchbox">
            <div class="search">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.16667 15.8333C12.8486 15.8333 15.8333 12.8486 15.8333 9.16667C15.8333 5.48477 12.8486 2.5 9.16667 2.5C5.48477 2.5 2.5 5.48477 2.5 9.16667C2.5 12.8486 5.48477 15.8333 9.16667 15.8333Z" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17.5 17.5L13.875 13.875" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input type="text" class="search-input" placeholder="Search..."/>
                <button type="button" class="btn-export-csv-login">CSV Export</button>
            </div>
        </div>
    </div>
    
    <div class="tab login-logs <?=(!isset($_GET["uid"])) ? "active":""?> ">

        

        <div class="sub-filter">
            <label>Login Reporting for</label>
            <div><input type="text" placeholder="Type/Search class" class="sub-filter-search"/></div>
            <label>over the period</label>
            <div class="over-the-period-container">
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

        <div class="login-logs-table-container ">
            <table id="login-logs-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th># Of Logins</th>
                        <th>Date/Time of last Login</th>
                        <th>Current Streak</th>
                        <th>HighestStreak</th>
                        <th>Classrooms</th>
                        <!-- Add more table headers as needed -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="tab login-logs-details">

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
                        url: `${safarObject.apiBaseurl}/users/login_logs/${userId}?tz=${browserTimezone}`,
                        dataType: "json",
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        success: (d) => {
                            console.log("login_logs", d)

                            detailsTpl = `
                                
                            `;
                            
                            $(".heading-login-logs.login .breadcrumb").html(`
                                <span>Class Reports</span>
                                <span class="spacer">
                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span>
                                    <a href="" class="a-login-report">Login Reports</a>
                                </span>
                                <span class="spacer">
                                    <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span><a href="">${d.profile.first_name}</a></span>
                            `);

                            var loginDatetime = d.summary.login_datetime;
                            if(d.summary.login_datetime == null){
                                var formattedDate = "Never Logged In";
                            }else{
                                var formattedDate = moment(loginDatetime).format('ddd, Do MMMM YYYY');
                            }


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
                                        <div class="label">Login Summary</div>

                                        <div class="table content">
                                            <div class="table-head">
                                                <div class=""># of Logins</div>
                                                <div class="">Date/Time of last Login</div>
                                                <div class="">Current Streak</div>
                                                <div class="">Highest Streak</div>
                                            </div>
                                            <div class="table-body">
                                                <div class="">${d.summary.login_count}</div>
                                                <div class="">${formattedDate}</div>
                                                <div class="">${d.summary.current_login_streak}</div>
                                                <div class="">${d.summary.highest_login_streak}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`

                            let list = ``;
                            
                            d.logs.map( log => {
                                var loginDatetime = log.login_datetime;
                                var formattedDate = moment(loginDatetime).format('MMMM D, YYYY [at] h:mm a');

                                list += `<div class="list-item">
                                    <img src="${d.profile.avatar}"/>
                                    <div class="date">${formattedDate}</div>
                                </div>`
                            })

                            detailsTpl += `
                            <div class="bottom">
                                <div class="card">
                                    <div class="label">Login Logs</div>
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

                            $(".tab.login-logs-details").html(detailsTpl).addClass("active")
                            $(".tab.login-logs").removeClass('active');

                            /* practice tracker calendar */
                            $.ajax({
                                url: safarObject.ajaxurl,
                                data: {
                                    action: "practice_tracker_calendar_report",
                                    userid: userId
                                },
                                beforeSend: () => {

                                },
                                success: (d) => {
                                    // popup full calendar
                                    $(".login-logs-details .calendar").html(d).css("opacity",0)

                                    setTimeout( () => {
                                        $('.view-full-calendar').trigger('click');
                                    }, 100)

                                    setTimeout( () => {
                                        $(".login-logs-details .calendar").css("opacity",1)
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
                link.download = "login_logs.csv";

                // Trigger the download
                link.click();
            }
            let csvContent = "";
            $(document).on('click','.btn-export-csv-login', function() {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/users/login_logs`,
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

            $(document).on("click",".heading.login .breadcrumb a.a-login-report", e => {
                e.preventDefault();
                $(".tab.login-logs-details").removeClass("active")
                $(".tab.login-logs").addClass('active')

                $(".heading-login-logs.login .breadcrumb").html(`
                    <span>Class Reports</span>
                    <span class="spacer">
                        <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1.5 11L6.5 6L1.5 1" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="active">
                        Login Reports
                    </span>
                `);

            })

            $(document).on("click",".over-the-period-container", e => {
                $(e.currentTarget).find(".dropdown").toggleClass("active");
            });

            $(document).on("click", e => {
                setTimeout( () => {
                    if(!$(e.target).hasClass("selected")){
                        if( $(".over-the-period-container .dropdown").hasClass('active') ) {
                            $(".over-the-period-container .dropdown").removeClass("active")
                        }
                    }
                }, 0)
                
            });

            $(document).on("click",".over-the-period-container .dropdown .item", e => {
                let selected = $(e.currentTarget).text();
                $(".over-the-period-container .selected").text(selected);
                $('#login-logs-table').DataTable().ajax.url(`${safarObject.apiBaseurl}/users/login_logs?period=${selected}&tz=${browserTimezone}`).load();
            });

           
            var selectedPeriod = $('.over-the-period').text();

            $('#login-logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: `${safarObject.apiBaseurl}/users/login_logs?period=${selectedPeriod}&tz=${browserTimezone}`,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    }
                },
                columns: [
                    { data: 'first_name' },
                    { data: 'last_name' },
                    { data: 'login_count' },
                    { data: 'login_datetime' },
                    { data: 'current_login_streak' },
                    { data: 'highest_login_streak' },
                    { data: 'classrooms' },
                    // Add more column definitions as needed
                ],
                order: [[3, 'desc']], // Set default sorting for the 4
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
                    $('td:eq(6)', row).html(`<div style="max-width:200px">${classroomNames}<div>`);
                    
                },
                initComplete: function() {
                    // Enclose info and pagination elements within a container
                    $('#login-logs-table_wrapper .dataTables_info, #login-logs-table_wrapper .dataTables_paginate')
                        .wrapAll('<div class="datatable-footer"></div>');

                    // Move the container to the desired location
                    $('#datatable-footer').appendTo('#datatable-pagination');

                    $('#login-report .search-input').on('keyup', function() {
                        var search = $(this).val();
                        $('#login-logs-table').DataTable().search(search).draw();
                    });
                    $('.sub-filter-search').on('keyup', function() {
                        var search = $(this).val();
                        //$('#login-logs-table').DataTable().search(search).draw();
                        let selectedPeriod = $(".over-the-period-container .dropdown .item").text();
                        $('#login-logs-table').DataTable().ajax.url(`${safarObject.apiBaseurl}/users/login_logs?period=${selectedPeriod}&search_class=${search}&tz=${browserTimezone}`).load();
                    });
                    
                    var lengthElement = $('.login-logs-table-container .dataTables_length');
                    var paginateElement = $('.login-logs-table-container .dataTables_info');
                    lengthElement.insertAfter(paginateElement);

                    $(".login-logs-table-container .dataTables_wrapper > .dataTables_length").remove();


                    // load user if uid is provided on the url
                    <?php 
                    if(isset($_GET["uid"])){
                        ?>
                        setTimeout( () => { j2jLoginLogs.details(<?=$_GET["uid"]?>), 100 });
                        <?php
                    }
                    ?>
                }
                
            });
        })
    </script>
    <?php
}, 999);
?>