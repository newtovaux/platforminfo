var coll = document.getElementsByClassName("platforminfo_collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("platforminfo_active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}

function clipboard(obj) {
  var tocopy = obj.getAttribute("data-item");
  navigator.clipboard.writeText(tocopy);
  alert('Copied to clipboard: ' + tocopy);
}