document.getElementById("save").addEventListener("click", function () {
    let title = document.getElementById("title").value;
    let content = document.getElementById("paste").value;
    var httpRequest = new XMLHttpRequest()
    httpRequest.onreadystatechange = function (data) {
        switch (httpRequest.readyState) {
            case 0: // UNINITIALIZED
            case 1: // LOADING
            case 2: // LOADED
            case 3: // INTERACTIVE
                break;
            case 4: // COMPLETED
                let resp = JSON.parse(httpRequest.response);
                document.getElementById("pbList").innerHTML = resp.newList;

                let pbs = document.querySelectorAll("#pbList a.g");
                for (let a of pbs) {
                    addGetListener(a);
                }
                pbs = document.querySelectorAll("#pbList a.d");
                for (let a of pbs) {
                    addDeleteListener(a);
                }

                break;
            default:
                alert("An error occurred.");
        }

    }
    httpRequest.open('POST', 'pastebin.php')
    httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    httpRequest.send('action=save&title=' + encodeURIComponent(title) + '&content=' + content);
});
document.getElementById("clear").addEventListener("click", function () {
    document.getElementById("title").value = '';
    document.getElementById("paste").value = '';
    document.getElementById("copyLink").innerHTML = '';
    document.getElementById("copyLink").href = '';
});
let pbs = document.querySelectorAll("#pbList a.g");
for (let a of pbs) {
    addGetListener(a);
}
pbs = document.querySelectorAll("#pbList a.d");
for (let a of pbs) {
    addDeleteListener(a);
}


function addGetListener(el) {
    el.addEventListener("click", function () {
        var httpRequest = new XMLHttpRequest()
        var title = this.innerHTML;
        httpRequest.onreadystatechange = function (data) {
            switch (httpRequest.readyState) {
                case 0: // UNINITIALIZED
                case 1: // LOADING
                case 2: // LOADED
                case 3: // INTERACTIVE
                    break;
                case 4: // COMPLETED
                    let resp = JSON.parse(httpRequest.response);
                    if (resp.status === 'success') {
                        document.getElementById("title").value = title;
                        document.getElementById("paste").value = resp.content;
                        document.getElementById("copyLink").innerHTML = resp.dUrl;
                        document.getElementById("copyLink").href = resp.dUrl;
                        let p = document.createElement("p");
                        p.textContent = title;
                        let htmlTitle='Simplest Pastebin : '+p.innerText;
                        history.replaceState('', htmlTitle, resp.url);
                        document.title=htmlTitle;


                    } else {
                        alert(resp.message);
                    }
                    break;
                default:
                    alert("An error occurred.");
            }

        }
        httpRequest.open('GET', 'pastebin.php?action=get&file=' + encodeURIComponent(title));
        httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        httpRequest.send();

    });
}

function addDeleteListener(el) {
    el.addEventListener("click", function () {
        var me = this;
        var title = this.previousElementSibling.innerHTML;

        if (confirm("Are you sure you want to delete " + title + "?")) {
            var httpRequest = new XMLHttpRequest();

            httpRequest.onreadystatechange = function (data) {
                switch (httpRequest.readyState) {
                    case 0: // UNINITIALIZED
                    case 1: // LOADING
                    case 2: // LOADED
                    case 3: // INTERACTIVE
                        break;
                    case 4: // COMPLETED
                        let resp = JSON.parse(httpRequest.response);
                        if (resp.status === 'success') {
                            me.parentNode.remove();
                        } else {
                            alert(resp.message);
                        }
                        break;
                    default:
                        alert("An error occurred.");
                }

            }
            httpRequest.open('POST', 'pastebin.php');
            httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            httpRequest.send('action=delete&file=' + encodeURIComponent(title));
        }
    });


}
