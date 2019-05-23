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

function showHint(str) {
    var obj;
    if (str.length != 13) {
        document.getElementById("title").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log(this.response);
                obj = JSON.parse(this.response);

                document.getElementById("title").value = obj.title;
                document.getElementById("subtitle").value = obj.subtitle;
                document.getElementById("publication").value = obj.publication;
                document.getElementById("start").value = obj.publishedDate;
                document.getElementById("publication").value = obj.publication;
                document.getElementById("thumbnail").src = obj.thumbnail;
                document.getElementById("language").value = obj.language;
                document.getElementById("authors").value = obj.authors;
                document.getElementById("discription").value = obj.description;

            }
        };
        xmlhttp.open("GET", "http://localhost/bookworm-middleware/code/v1/get_book_data_from_isbn.php?isbn=" + str, true);
        xmlhttp.setRequestHeader("Content-type", "application/json");
        xmlhttp.send();
    }

}
