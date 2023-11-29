$ = jQuery;
let j2jTincannyReporting = {};

j2jTincannyReporting = {
    helper: {
        generateUUID : () => {
            var d = new Date().getTime();
            var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
              var r = (d + Math.random()*16)%16 | 0;
              d = Math.floor(d/16);
              return (c=='x' ? r : (r&0x3|0x8)).toString(16);
            });
            return uuid;
        }
    }
}


// events listener
$(document).on("click","#coursesOverviewTable .course-name .expand", e => {
    e.preventDefault();

    let parentTr = $(e.currentTarget).parent().parent().parent();
    let uuid = j2jTincannyReporting.helper.generateUUID();

    if(!$(parentTr).hasClass("active")){
        $(parentTr).attr("data-uuid", uuid).addClass("active");
        let avgTimeComplete = $(parentTr).find("td:nth-child(10)").text();
        let avgTimeSpent = $(parentTr).find("td:nth-child(11)").text();
        let expandElements = `
            <tr class="children children-${uuid}" data-uuid="${uuid}">
                <td colspan="8">
                    <div class="row-avg-complete"><span>Average time to complete:</span> <span class="value">${avgTimeComplete}<span></div>
                    <div class="row-avg-spent"><span>Average time to spent:</span> <span class="value">${avgTimeSpent}<span></div>
                </td>
            </tr>
            
        `;
        parentTr.after(expandElements)
    }else{
        uuid = $(parentTr).attr("data-uuid");

        $(parentTr).removeClass("active");
        $(`#coursesOverviewTable .children-${uuid}`).remove();
    }
});
