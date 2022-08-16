document.getElementById("save").addEventListener("click", function () {
    let title = document.getElementById("title").value;
    let content = document.getElementById("paste").value;
    var httpRequest = new XMLHttpRequest()
    httpRequest.onreadystatechange = function (data) {
        if (httpRequest.readyState === 4) {
            let resp = JSON.parse(httpRequest.response);
            document.getElementById("pbList").innerHTML = resp.newList;
            document.getElementById("title").value = resp.title;
            document.getElementById("copyLink").innerHTML = resp.dUrl;
            document.getElementById("copyLink").href = resp.dUrl;
            document.getElementById("copy").classList.remove("hidden");
            let p = document.createElement("p");
            p.textContent = title;
            let htmlTitle = 'Simplest Pastebin : ' + p.innerText;
            history.replaceState('', htmlTitle, resp.url);
            document.title = htmlTitle;

            let pbs = document.querySelectorAll("#pbList a.g");
            for (let a of pbs) {
                addGetListener(a);
            }
            pbs = document.querySelectorAll("#pbList a.d");
            for (let a of pbs) {
                addDeleteListener(a);
            }
        } else if (httpRequest.readyState < 0 || httpRequest.readyState > 4) {
            alert("An error occurred.");
        }
    }
    httpRequest.open('POST', 'index.php')
    httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
    httpRequest.send('action=save&title=' + encodeURIComponent(title) + '&content=' + content);
    document.getElementById('paste').focus();
});
document.getElementById("clear").addEventListener("click", function () {
    document.getElementById("title").value = '';
    document.getElementById("paste").value = '';
    document.getElementById("copyLink").innerHTML = '';
    document.getElementById("copyLink").href = '';
    document.getElementById("copy").classList.add("hidden");
    document.getElementById('paste').focus();
});

document.getElementById("title").addEventListener("keyup", function (e) {

    let key = e.which || e.keyCode;
    if (key === 13) {
        let clickEvent = new Event('click');
        document.getElementById("save").dispatchEvent(clickEvent);

    }
});
document.getElementById("searchVal").addEventListener("keyup", function () {
    let search = document.getElementById("search");
    if (this.value.length > 0) {
        if (search.classList.contains("empty")) {
            search.classList.remove("empty");
        }
    } else {
        search.classList.add("empty");
    }
    let pastes = document.querySelectorAll("a.g");
    for (let p of pastes) {
        let li = p.parentElement;
        if (p.innerHTML.toLowerCase().includes(this.value.toLowerCase())) {
            li.classList.remove("hidden");
        } else {
            li.classList.add("hidden");
        }
    }
});


let changeEvent = new Event('keyup');
document.getElementById("searchVal").dispatchEvent(changeEvent);

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
            if (httpRequest.readyState === 4) {
                let resp = JSON.parse(httpRequest.response);
                if (resp.status === 'success') {
                    document.getElementById("title").value = title;
                    document.getElementById("paste").value = resp.content;
                    document.getElementById("copyLink").innerHTML = resp.dUrl;
                    document.getElementById("copyLink").href = resp.dUrl;
                    document.getElementById("copy").classList.remove("hidden");
                    let p = document.createElement("p");
                    p.textContent = title;
                    let htmlTitle = 'Simplest Pastebin : ' + p.innerText;
                    history.replaceState('', htmlTitle, resp.url);
                    document.title = htmlTitle;
                    document.getElementById('paste').focus();
                } else {
                    alert(resp.message);
                }
            } else if (httpRequest.readyState < 0 || httpRequest.readyState > 4) {
                alert("An error occurred.");
            }
        }
        httpRequest.open('GET', 'index.php?action=get&file=' + encodeURIComponent(title));
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
                if (httpRequest.readyState === 4) {
                    let resp = JSON.parse(httpRequest.response);
                    if (resp.status === 'success') {
                        me.parentNode.remove();
                    } else {
                        alert(resp.message);
                    }
                }
                if (httpRequest.readyState < 0 || httpRequest.readyState > 4) {
                    alert("An error occurred.");
                }
            }
            httpRequest.open('POST', 'index.php');
            httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            httpRequest.send('action=delete&file=' + encodeURIComponent(title));
        }
    });
}

function copyTextToClipboard(element, textToCopy) {
    document.querySelector(element).insertAdjacentHTML('afterend', "<span class='copied' id='TEMPcopyCopied'>Copied</span><textarea id='TEMPcopy' style='display:block;position:fixed;top:-5000px;left:0;' >" + htmlEntities(textToCopy) + "</textarea>");
    setTimeout(function () {
        var copyText = document.getElementById("TEMPcopy");
        if (copyText.value.length > 0) {
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/
            document.execCommand("copy");
        }
        document.getElementById("TEMPcopy").remove();
        setTimeout(function () {
            document.getElementById("TEMPcopyCopied").remove();
        }, 1500);
    }, 50);
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&apos;');
}

document.getElementById('paste').focus();
