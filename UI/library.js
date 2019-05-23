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
                showExistingAuthors(obj.authors);
            }
        };
        xmlhttp.open("GET", "http://localhost/bookworm-middleware/code/v1/get_book_data_from_isbn.php?isbn=" + str, true);
        xmlhttp.setRequestHeader("Content-type", "application/json");
        xmlhttp.send();
    }



}
function showExistingAuthors(str) {
    var obj;
    var xmlhttp = new XMLHttpRequest();
    var eAuthors = [];
    var eAuthorsAdd = [];
    var eAuthorsLS = [];
    var nAuthors = [];
    var nAuthorsAdd = [];
    var nAuthorsLS = [];

    if(localStorage.getItem("eAuthors") === null){
        localStorage.setItem("eAuthors",eAuthors);
    }else{
        eAuthorsLS = localStorage.getItem("eAuthors");
        eAuthors = eAuthorsLS.split(",");
    }

    if(localStorage.getItem("nAuthors") === null){
        localStorage.setItem("nAuthors",nAuthors);
    }else{
        nAuthorsLS = localStorage.getItem("nAuthors");
        nAuthors = nAuthorsLS.split(",");
    }
    xmlhttp.onreadystatechange = function() {

        obj = JSON.parse(this.response);
        console.log(obj.data.new_authors);
        localStorage.setItem("nAuthors",obj.data.new_authors);

        for(author in obj.data.exist_authors){

            if(!(eAuthors.includes(obj.data.exist_authors[author])) && !(nAuthors.includes(obj.data.exist_authors[author])) ){


                if(confirm("Following author found in the system: \n" + obj.data.exist_authors[author] + "\nDo you want to use these ?")){

                    eAuthorsAdd.push(obj.data.exist_authors[author]);

                }else{
                    console.log("cancel clicked");
                    nAuthorsAdd.push(obj.data.exist_authors[author]);
                    console.log(nAuthorsAdd);
                }
            }

        }

        eAuthorsLS = localStorage.getItem("eAuthors");
        eAuthors = eAuthorsLS.split(",");
        eAuthors.push(eAuthorsAdd);
        localStorage.setItem("eAuthors",eAuthors.join(","));



        nAuthorsLS = localStorage.getItem("nAuthors");
        nAuthors = nAuthorsLS.split(",");
        nAuthors.push(nAuthorsAdd);
        localStorage.setItem("nAuthors",nAuthors.join(","));




    };
    xmlhttp.open("GET", "http://localhost/bookworm-middleware/code/v1/check_author_exist.php?authors=" + str, false);
    xmlhttp.setRequestHeader("Content-type", "application/json");
    xmlhttp.send();
}
