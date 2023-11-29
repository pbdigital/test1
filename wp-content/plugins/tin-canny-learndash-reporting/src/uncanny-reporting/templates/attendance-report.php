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

<div class="uo-admin-reporting-tab-single" id="attendance-report" style="display: <?php echo $current_tab == 'attendance-report' ? 'block' : 'none'; ?>">

   
    
    <div class="tab attendance-logs active">

        

        <div class="sub-filter">
            <label>Attendance for</label>
            <div><input type="text" placeholder="Type/Search class" class="sub-filter-search-attendance search-class-text"/></div>
            <label>over the period</label>
            <div class="over-the-period-container-attendance dropdown-container filter-over-the-period">
                <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                <span class="selected">Last 7 Days</span>
                <div class="dropdown">
                    <div class="item">Last 7 Days</div>
                    <div class="item">Last 30 Days</div>
                    <div class="item">Last 90 Days</div>
                    <div class="item">This Academic Year</div>
                </div>
            </div>
        </div>

        <div class="attendance-logs-table-container dataTables_wrapper">
            <div id="practice-logs-table_processing" class="dataTables_processing" style="/* display: none; */">Processing...</div>
            <table id="attendance-logs-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th class="th-students">
                            
                            STUDENTS 
                            <div class="sub-filter student-sub-filter">
                                <div class="over-the-period-container-attendance dropdown-container students-sub-filter">
                                    <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 1.5L6 6.5L11 1.5" stroke="#6B6F72" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>

                                    <span class="selected">All attendance</span>
                                    <div class="dropdown">
                                        <div class="item">All attendance</div>
                                        <div class="item">Absent today</div>
                                        <div class="item">Late today</div>
                                        <div class="item">3 lates in a row</div>
                                        <div class="item">More than 5 lates</div>
                                        <div class="item">More than 10 lates</div>
                                        <div class="item">3 or more absences in a row</div>
                                    </div>
                                </div>
                            </div>

                        </th>
                        
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <div class="tab attendance-logs-details">

    </div>

</div>

<?php 
add_action("wp_footer", function(){
    ?>
    <script type="text/javascript">
        jQuery(document).ready( $ => {
            
            let browserTimezone = moment.tz.guess();

            $('.attendance-logs-table-container').scroll(function() {
                $("#attendance-logs-table").removeClass("dropdown-active")
                $(".over-the-period-container-attendance.students-sub-filter .dropdown").removeClass("active")
            });
            
            $(document).on("click",".over-the-period-container-attendance", e => {
                $(e.currentTarget).find(".dropdown").toggleClass("active");
                if( $(e.currentTarget).find(".dropdown").hasClass("active")){
                    
                    var container = $('.attendance-logs-table-container');
                    var scrollLeftValue = 0; // Adjust this value as needed
                    container.animate({
                        scrollLeft: scrollLeftValue
                    }, 0); // Adjust the duration as needed
                    
                    setTimeout(() => {
                        $("#attendance-logs-table").addClass("dropdown-active")
                    }, 30);
                }
            });

            $(document).on("click", e => {
                setTimeout( () => {
                    if(!$(e.target).hasClass("selected")){
                        if( $(".over-the-period-container-attendance .dropdown").hasClass('active') ) {
                            $(".over-the-period-container-attendance .dropdown").removeClass("active");
                            
                        }
                    }
                     
                }, 0)
            });


            

            $(document).on("click",".over-the-period-container-attendance .dropdown .item", e => {
                let selected = $(e.currentTarget).text();
                let parentElement = $(e.currentTarget).parent().parent();
                parentElement.find(".selected").text(selected);
                j2jAttendanceReport.tableList();
            });

            let searchTimeout = "";
            $(document).on("keyup",".search-class-text", e=> {
                if(searchTimeout !="") clearTimeout(searchTimeout)
                searchTimeout = setTimeout( () => {
                    j2jAttendanceReport.tableList();
                },1000)
            });

            let j2jAttendanceReport = {

                tableList : () => {
                    let selectedPeriod = $('.filter-over-the-period > .selected').text();
                    let subFilter = $('.students-sub-filter > .selected').text();
                    let searchClass = $('.search-class-text').val()
                    $.ajax({
                        url: `${safarObject.apiBaseurl}/attendance_report/?period=${selectedPeriod}&tz=${browserTimezone}&subfilter=${subFilter}&searchclass=${searchClass}`,
                        headers: {
                            "X-WP-Nonce": safarObject.wpnonce
                        },
                        dataType: "json",
                        beforeSend:() => {
                            $(".th-date-cell, #attendance-logs-table tr td.status").css({"opacity":0,"visibility":"hidden"});
                            $(".attendance-logs-table-container #practice-logs-table_processing").show();
                        },
                        success:(response) => {
                            let tpl = ``;
                            $(".attendance-logs-table-container #practice-logs-table_processing").hide();

                            $(".th-date-cell").remove();
                            let thFivedays = ``;
                            response.all_dates.map( d => { 
                                const momentDate = moment(d);
                                const formattedDate = momentDate.format("dddd,")+"<br/>"+momentDate.format("Do MMMM YYYY");
                                thFivedays += `<th class="th-date-cell" data-date="${d}">${formattedDate}</th>`
                            })

                            $('.th-students').after(thFivedays);

                            if(response.data.length > 0 ){
                                response.data.map( student => {
                                    let present = `<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="18" cy="18" r="18" fill="#98C03D"/>
                                            <path d="M25.5455 13L15.5455 23L11 18.4545" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>              
                                            `     
                                    let late = `<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="18" cy="18" r="18" fill="#F2A952"/>
                                                <path d="M13.0147 17.8129C13.044 13.3673 13.0954 10.8143 13.1687 10.1541C13.2127 10.0954 13.7116 10.0514 14.6653 10.022C15.0174 10.0147 15.3072 10.0073 15.5346 10C15.9234 10 16.1252 10.0147 16.1398 10.044C16.1545 10.1614 16.1619 10.6382 16.1619 11.4746C16.1619 12.5163 16.1508 14.1229 16.1288 16.2944C16.0922 20.2118 16.0591 22.5374 16.0298 23.271C17.1229 23.249 18.4177 23.2306 19.9143 23.216C20.5158 23.2086 21.022 23.205 21.4328 23.205C22.0564 23.205 22.4782 23.2123 22.6983 23.227C22.7863 23.4617 22.8377 23.9386 22.8524 24.6575C22.8597 24.8482 22.8597 25.017 22.8524 25.1637C22.8524 25.5745 22.834 25.8239 22.7973 25.912C22.79 25.9413 22.0087 25.9633 20.4535 25.978C19.4851 25.9927 18.5057 26 17.5154 26C16.9211 26 16.3159 26 15.6997 26C14.0857 25.9853 13.1981 25.956 13.0367 25.912C13.0147 25.3691 13.0037 24.254 13.0037 22.5667C12.9963 21.3049 13 19.7203 13.0147 17.8129Z" fill="white"/>
                                                </svg>
                                            `              
                                    let absent = `<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="18" cy="18" r="18" fill="#EF746F"/>
                                        <path d="M24 12L12 24M12 12L24 24" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        `;
                                    let norecord = `<svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="18" cy="18" r="18" fill="#BFBFBF"/>
                                    <path d="M10.929 18H25.0711" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    `;
                                    let tdFiveDays = ``;

                                    student.cellattendance.map( e => {
                                        let status = "";
                                        switch(e.status){
                                            case "late":
                                                status = late;
                                                break;
                                            case "present":
                                                status = present;
                                                break;
                                            case "absent":
                                                status = absent;
                                                break;
                                            default: 
                                                status = norecord;
                                                break;
                                        }

                                        tdFiveDays += `<td class="status">${status}</td>`
                                    })
                                    tpl += `
                                        <tr>
                                            <td class="avatar-container">
                                                <div  class="avatar">
                                                    <div> 
                                                        <img src="${student.avatar}"/>
                                                    </div>
                                                    <div>
                                                        <div class="name">${student.name}</div>
                                                        <div class="text">${student.attendance_percent}% Attendance . ${student.late_count} Late</div>
                                                    </div>
                                                </div>
                                            </td>

                                            ${tdFiveDays}
                                        </tr>
                                    `;
                                })
                            }else{
                                tpl = `<tr><td colspan="${response.all_dates.length + 1}" style="text-align:center">No records found</td></tr>`
                            }

                            $("#attendance-logs-table tbody").html(tpl);
                        }
                        
                    });
                }
            }

            j2jAttendanceReport.tableList();
            
        })
    </script>
    <?php
}, 999);
?>