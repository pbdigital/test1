$ = jQuery;
let SafarCoursePathway = {}
SafarCoursePathway = {
    courseId: 210075, // set to 0 after testing
    api: {
        getCoursePathWay: async ( data ) => {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: `${safarObject.apiBaseurl}/course/pathway/${data.courseId}`,
                    headers: {
                        "X-WP-Nonce": safarObject.wpnonce
                    },
                    beforeSend: () => {
    
                    },
                    success: (d) => {
                        
                        resolve(d);
                    },
                    error: (d) => {
                        reject(`error /user/${userId}`);
                    }
                });
            });
        }
    },

    loadCoursePathWay : (data) => {
        SafarCoursePathway.api.getCoursePathWay( data )
            .then( d => {

            })
            .catch( e => {
                console.log("error course/pathway ", e )
            })
    },

    init: () => {
        SafarCoursePathway.loadCoursePathWay({courseId:SafarCoursePathway.courseId});
    }

}

SafarCoursePathway.init();