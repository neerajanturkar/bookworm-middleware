window.onscroll = function() {
    scrollFunction()
};
    
function scrollFunction() {
var obj = document.getElementById('nav-header');
var obj1 = document.getElementById("navbar-default");

    if (document.body.scrollTop > 30 || document.documentElement.scrollTop > 30) {
        obj.className = "fixedHeaderClass";
        obj1.style.paddingLeft = "16px";
        obj1.style.paddingRight = "25px";
        } 
        else
        {
        obj.className = " ";
        obj1.style.paddingLeft = " ";
        obj1.style.paddingRight = " ";
        }
    }