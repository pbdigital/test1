triggerCompleteButton = () => {
  window.parent.document
    .querySelector(".learndash_mark_complete_button")
    .removeAttribute("disabled");

  // click on the .learndash_mark_complete_button button in the iframe parent window
  window.parent.document
    .querySelector(".learndash_mark_complete_button")
    .click();
    
}

let audioWrapperQuestionType = false;

document.addEventListener("DOMContentLoaded", function (event) {
  // define the handleXAPI function
  var handleXAPI = function(event) {
  
    // log the event data to the console
    //console.log(JSON.stringify(event));
    
    
    
    
    var contentType = null;
    // get the verb of the user's interaction
    var userInteractionVerb = event.data.statement.verb.display["en-US"];
  
    // set the initial value of the success flag to false
    var interactionSuccessful = false;
  
    // set the initial value of the expected verb to false
    var expectedVerb = false;
  
    // check if the success property exists in the event data
    if (typeof event?.data?.statement?.result?.success != "undefined") {
  
      // if the success property exists, set the success flag to its value
      interactionSuccessful = event.data.statement.result.success;
    }
  

    // check if the user is interacting with an H5P Question content type
    if (document.getElementsByClassName("h5p-question").length) {
        contentType = "h5p-question";
        expectedVerb = "answered";
    }
    // check if the user is interacting with an H5P Drag Text type
    if (document.getElementsByClassName("h5p-drag-text").length) {
        contentType = "h5p-drag-text";
        expectedVerb = "answered";
        // This question type doesn't pass event.data.statement.result.success when answered, so will use the scaled score to figure if they pass or not
        if (typeof event?.data?.statement?.result?.score.scaled != "undefined") {
          if (event.data.statement.result.score.scaled == 1){
            interactionSuccessful = true;
          }
        }
    }
    
    // check if the user is interacting with an H5P Memory Game content type
    if (document.getElementsByClassName("h5p-memory-game").length) {
        contentType = "h5p-memory-game";
        expectedVerb = "completed";
        interactionSuccessful = true;
    }
    
    // check if the user is interacting with an H5P Memory Game content type
    if (document.getElementsByClassName("h5p-image-hotspot-question").length) {
        contentType = "h5p-image-hotspot-question";
        expectedVerb = "answered";
        // This question type doesn't pass event.data.statement.result.success when answered, so will use the scaled score to figure if they pass or not
        if (typeof event?.data?.statement?.result?.score.scaled != "undefined") {
          if (event.data.statement.result.score.scaled == 1){
            interactionSuccessful = true;
          }
        }
    }
    
    
    // check if the user is interacting with an H5P Interactive content type
    if (document.getElementsByClassName("h5p-interactive-video").length) {
      contentType = "h5p-interactive-video";
      expectedVerb = "completed";
      interactionSuccessful = true;
    }
    
    //console.log('Content Type is ' + contentType + ' the verb is ' + userInteractionVerb + ' expected verb is ' + expectedVerb + ' interactionSuccessful is ' + interactionSuccessful );
    // check if the verb of the user's interaction matches the expected verb and if the success flag is true
    if (userInteractionVerb == expectedVerb && interactionSuccessful == true) {

        // check if h5p-question-check-answer button is still visible
        // thats means there is still page left waiting to be answered
        let h5pCheckButtonCount = document.querySelectorAll('.h5p-question-check-answer').length;
      
        let progressBarBackground = "";
        const progressBar = document.querySelector('.h5p-joubelui-progressbar');
        let progressBarBackgroundWidth = "";

        if( progressBar !== null && getComputedStyle(progressBar).display !== 'none' ){
          progressBarBackground = document.querySelector('.h5p-joubelui-progressbar .h5p-joubelui-progressbar-background');
          if (progressBarBackground !== null) {
              h5pCheckButtonCount = 1;
          }
        }

        // check if full score is visible if full score is passed
        // trigger the complete button
        /*
        if( h5pCheckButtonCount <= 0 ){ // only submit the form when there is no check button left
          
          // if the verb and success flag match the expected values, enable the .learndash_mark_complete_button button in the iframe parent window
          window.parent.document
            .querySelector(".learndash_mark_complete_button")
            .removeAttribute("disabled");
      
          // click on the .learndash_mark_complete_button button in the iframe parent window
          window.parent.document
            .querySelector(".learndash_mark_complete_button")
            .click();
            
        }
        */
          
    }

    var contentId = document.querySelector(".h5p-content.h5p-initialized.h5p-no-frame").getAttribute("data-content-id");
    // https://my.journey2jannah.com/courses/level-5a-short-%e1%b8%a5arakahs/lessons/level-5a-kasrah/topic/5-2-kasrah-letters/?dashboard=1
    var slideElements = document.querySelectorAll(".h5p-slides-wrapper .h5p-slide");

    console.log("test123", contentId, contentType, slideElements.length, event)

    /// is audio wrapper questio type
    if(audioWrapperQuestionType){
      audioWrapper();
    }


    if(contentId == 1475 || contentId == 1350){
      
      var slideElements = document.querySelectorAll(".h5p-slides-wrapper .h5p-slide");
      var lastSlideElement = slideElements[slideElements.length - 1];
      setTimeout( () => {
        console.log("test123", contentId, lastSlideElement, lastSlideElement.classList, lastSlideElement.classList.contains("h5p-current"));
        if (lastSlideElement.classList.contains("h5p-current")) {
          triggerCompleteButton();
        }
      }, 500)
    }

    // Custom Trigger Complete button for Memory Game but not under a slide or pages
    if( contentType == "h5p-memory-game" && slideElements.length == 0 ){
        var liElements = document.querySelectorAll('.ld-tabs-content .h5p-content .h5p-memory-game ul li');
        var liDisabled = document.querySelectorAll('.ld-tabs-content .h5p-content .h5p-memory-game ul li[aria-disabled="true"]');

        if(liDisabled.length >= liElements.length ){
          triggerCompleteButton();
        }
    }
  };
  
  // listen for the xAPI event and call the handleXAPI function when it is triggered
  H5P.externalDispatcher.on('xAPI', handleXAPI);

  
  
  
  
  
  if (
    document.getElementsByClassName("h5p-mark-the-words").length ||
    document.getElementsByClassName("h5p-memory-game").length ||
    document.getElementsByClassName("h5p-undefined").length ||
    document.getElementsByClassName("h5p-single-choice-set").length ||
    document.getElementsByClassName("h5p-text-scaling").length ||
    document.getElementsByClassName("h5p-find-the-words").length ||
    document.getElementsByClassName("h5p-image-sequencing").length ||
    document.getElementsByClassName("h5p-multichoice").length ||
    document.getElementsByClassName("h5p-image-hotspot-question").length ||
    document.getElementsByClassName("h5p-drag-text").length
  ) {
    // class name does not exist in the document
    let element = document.querySelector(".h5p-content");
    let elementRoot = document.querySelector(".h5p-iframe");
    element.classList.add("purple-bg");
    elementRoot.classList.add("purple");
  }

  if (document.getElementsByClassName("h5p-interactive-video").length) {
    let element = document.querySelector(".h5p-content");
    element.classList.add("interactive-video");
  }
  //on page load, is there an <a> link with href containing "quizzes"? if so, hide .learndash_mark_complete_button button in iframe parent window
  let links = document.querySelectorAll('a');
  //loop through all the links
  for (let i = 0; i < links.length; i++) {
      //add the class to the link
      links[i].classList.add("quiz-link");
     // console.log('adding quiz link');
  }
  
  document.addEventListener("click", function (e) {
    //console.log(e.target);
    
    let letsGoHref = e.target.getAttribute('href');
    let letsGoTarget = e.target.getAttribute('target');
    let letsGoStyle = e.target.getAttribute('style');
    //console.log("letsgo", letsGoHref, letsGoTarget, letsGoStyle);

    if( letsGoHref == "http://" && letsGoTarget == "_blank" && letsGoStyle == "cursor: pointer;"){
        // trigger Complpete
        e.preventDefault();
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .removeAttribute("disabled");

        // click on the .learndash_mark_complete_button button in the iframe parent window
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .click();
    }
    
    if( e.target.textContent == "Submit Answers"){
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .removeAttribute("disabled");

        // click on the .learndash_mark_complete_button button in the iframe parent window
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .click();

          window.parent.moveToNextTopic();
    }



    if (e.target.classList.contains("h5p-play")) {
       // window.parent.setVideoIsplaying(true);
    } 

    //was it an <a> tag?
    if (e.target.tagName == "A") {
      //Does href contain "quizzes"?
      if (e.target.href.indexOf("#j2jquiz") > -1) {
        
        //prevent the default action
        e.preventDefault();

        //remove disabled attribute on .learndash_mark_complete_button button in iframe parent window
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .removeAttribute("disabled");

        //click on .learndash_mark_complete_button button in iframe parent window
        window.parent.document
          .querySelector(".learndash_mark_complete_button")
          .click();

        
      }
    }
  });
});


let barFullScoreInterval = setInterval( () => {
  const barFullScore = document.querySelector('.h5p-joubelui-score-bar-full-score');
  if( barFullScore !== null && getComputedStyle(barFullScore).display !== 'none' ){
    checkIfCompleted();
  }
}, 1000)

checkIfCompleted = () => {

    const barFullScore = document.querySelector('.h5p-joubelui-score-bar-full-score');
    let maxScore = "";
    let currentScore = "";
    if( barFullScore !== null && getComputedStyle(barFullScore).display !== 'none' ){
      const scoreNumberCounter = document.querySelector('.h5p-joubelui-score-bar-full-score .h5p-joubelui-score-number-counter');

      if (scoreNumberCounter !== null) {
          currentScore = scoreNumberCounter.innerHTML;
      }

      const maxNumberCounter = document.querySelector('.h5p-joubelui-score-bar-full-score .h5p-joubelui-score-max');

      if (maxNumberCounter !== null) {
        maxScore = maxNumberCounter.innerHTML;
      }

    }

    // not interactive video
    //h5p-container h5p-standalone h5p-interactive-video

    const containerH5p = document.querySelector('.h5p-container'); // Replace with your container selector

    if (!containerH5p.classList.contains('h5p-interactive-video') ){

      if (document.querySelector('.questionset')) {
          isQuestionSet = true;
      } else {
          isQuestionSet = false;
      }

      if(!isQuestionSet){
        if(maxScore == currentScore){
          clearInterval(barFullScoreInterval);
          // if the verb and success flag match the expected values, enable the .learndash_mark_complete_button button in the iframe parent window
          window.parent.document
            .querySelector(".learndash_mark_complete_button")
            .removeAttribute("disabled");

          // click on the .learndash_mark_complete_button button in the iframe parent window
          window.parent.document
            .querySelector(".learndash_mark_complete_button")
            .click();
        
        }
      }else{
        
        /*const questionContainers = document.querySelectorAll('.questionset .question-container');
        const lastQuestionContainer = questionContainers[questionContainers.length - 1];

        if (lastQuestionContainer.offsetParent !== null) {
          clearInterval(barFullScoreInterval);
            window.parent.document
              .querySelector(".learndash_mark_complete_button")
              .removeAttribute("disabled");

            // click on the .learndash_mark_complete_button button in the iframe parent window
            window.parent.document
              .querySelector(".learndash_mark_complete_button")
              .click();
            console.log("trigger submit", maxScore, currentScore);
        } 
        */

        const resultsElement = document.querySelector('.questionset-results');
        if(resultsElement !== null){
          console.log("question set result found")
          const resultsElementStyle = getComputedStyle(resultsElement);

          if (resultsElementStyle.display === 'none') {
          } else {
              clearInterval(barFullScoreInterval);
              window.parent.document
                .querySelector(".learndash_mark_complete_button")
                .removeAttribute("disabled");

              // click on the .learndash_mark_complete_button button in the iframe parent window
              window.parent.document
                .querySelector(".learndash_mark_complete_button")
                .click();
              console.log("h5p trigger submit questionset", maxScore, currentScore);
          }
        }
      }
      console.log("h5p status", maxScore, currentScore);
    }
}




/*
const interactionInner = document.querySelector('.h5p-interaction .h5p-interaction-inner');

if (interactionInner !== null) {
    const link = interactionInner.querySelector('a');
    if (link !== null) {
        const href = link.getAttribute('href');
        console.log(`.h5p-interaction .h5p-interaction-inner a href: ${href}`);
        
        link.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('The link was clicked!');
            // Add your code to handle the click event here
        });
    }
}
*/


document.addEventListener('DOMContentLoaded', function() {
  const container = document.querySelector('.h5p-container'); // Replace with your container selector
  if (container.classList.contains('h5p-interactive-video') 
      || container.classList.contains('h5p-course-presentation') 
      || container.classList.contains('h5p-dragquestion') 
      || container.classList.contains('h5p-question')  
      || container.classList.contains('h5p-find-the-words')   
      
      ) {
      //console.log("has h5p-interactive-video")
      window.parent.addFullScreenClass();
  }
})

document.addEventListener('DOMContentLoaded', function() {
  const container = document.querySelector('.h5p-container'); // Replace with your container selector
  if (container.classList.contains('h5p-find-the-words')   
      
      ) {
      //console.log("has h5p-interactive-video")
      document.documentElement.classList.add('h5p-find-the-words');
  }
})


document.addEventListener('DOMContentLoaded', function() {
  const container = document.querySelector('.h5p-container'); // Replace with your container selector
  if (container.classList.contains('h5p-interactive-video') 
      || container.classList.contains('h5p-course-presentation') 
      || container.classList.contains('h5p-dragquestion') 
      || container.classList.contains('h5p-question') 

      
      ) {
      //console.log("has h5p-interactive-video")
      window.parent.addClassToIframe(container.classList.toString());
      
      const classes = container.classList;

      for (let i = 0; i < classes.length; i++) {
        if( classes[i] == "h5p-dragquestion" || classes[i] == "h5p-question"){
          document.body.classList.add("allow-overflow");
          document.documentElement.classList.add('allow-overflow');

        }
      }

  }
  

  audioWrapper = function() { // https://my.journey2jannah.com/courses/level-5a-short-%E1%B8%A5arakahs/lessons/level-5a-fat%E1%B8%A5ah/topic/5-1-fat%E1%B8%A5ah-activity/
    // Get the elements with the specified classes
    setTimeout( e => {
      // Get all elements with the class .h5p-multi-media-choice
      var multiMediaChoiceElements = document.querySelectorAll(".h5p-multi-media-choice");

      // Iterate through each .h5p-multi-media-choice element
      multiMediaChoiceElements.forEach(function(multiMediaChoice) {
          // Get the elements within this specific .h5p-multi-media-choice container
          var audioWrapper = multiMediaChoice.querySelector(".h5p-audio-wrapper");
          var questionContent = multiMediaChoice.querySelector(".h5p-question-content");

          // Check if both elements exist within this container
          if (audioWrapper && questionContent) {
              // Get the parent element
              var parentElement = questionContent.parentElement;

              // Move audioWrapper before questionContent within this container
              parentElement.insertBefore(audioWrapper, questionContent);
          }
      });
      
    }, 100)
  }

  // Get all elements with the specified class name
  var elementsWithClass = document.getElementsByClassName("h5p-multi-media-choice-content");
  if (elementsWithClass.length > 0) {
      console.log("h5p-multi-media-choice-content is present")
      audioWrapper();
      audioWrapperQuestionType = true;
  } 

})