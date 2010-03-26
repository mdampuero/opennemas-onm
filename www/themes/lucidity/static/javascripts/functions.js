

var min=8;
var max=18;
function increaseFontSize() {
   var p = document.getElementsByClassName('content-article');
   for(i=0;i<p.length;i++) {
      if(p[i].style.fontSize) {
         var s = parseInt(p[i].style.fontSize.replace("px",""));
      } else {
         var s = 12;
      }
      if(s!=max) {
         s += 1;
      }
      p[i].style.fontSize = s+"px"
   }
}
function decreaseFontSize() {
   var p = document.getElementsByClassName('content-article');
   for(i=0;i<p.length;i++) {
      if(p[i].style.fontSize) {
         var s = parseInt(p[i].style.fontSize.replace("px",""));
      } else {
         var s = 12;
      }
      if(s!=min) {
         s -= 1;
      }
      p[i].style.fontSize = s+"px"
   }   
}

function sendbyemail(title,url) {
  window.location="mailto:insert_here@your_friend.mail?subject=See this article '"+title+"' from Mabishu Studio&body=Please take a look at: "+url+" from Mabishu Blog.";
}

function showCommentForm(){
    $('.auth-selector').slideToggle();
    $('.form-comments .form').slideToggle();
}