function scrapeUdemy(argument) {
  var courses = [];
  var ul = document.getElementsByClassName('courses')[0];
  for (var i = 0; i < ul.children.length; i++) {
    var c = {};
    var course = ul.children[i];
    var courseLink = course.children[0];
    c.link = courseLink.href;
    var cIWr = courseLink.children[1];
    c.title = cIWr.children[0].children[0].children[0].textContent;
    var author = cIWr.children[1].children[0].textContent;
    c.author = author.replace(/\r?\n|\r/g, '');
    var price = cIWr.children[2].children[1].children[0].textContent;
    price = price.replace(/\r?\n|\r/g, '');
    c.price = price;
    var ratingBlock = cIWr.children[2].children[3].children[0].children[0];
    c.rating = ratingBlock.style.width;
    c.timesRated = cIWr.children[2].children[3].children[1].textContent
    c.level = cIWr.children[2].children[7].textContent
    courses[courses.length] = c;
  }
  var json = JSON.stringify(courses);
  var url = window.location.href;
  var currentPage = "";
  if (url.indexOf("?p=") !== -1) {
    currentPage = getJsonFromUrl();
    currentPage = currentPage["p"];
  }
  var saveUrl = "http://localhost/sprint1/save.php";
  send(json, saveUrl, url, currentPage, udemy_last_page());
}

function scrapeEdx() {
  var courses = [];
  var co = document.getElementsByClassName('result-count');
  var count = co[0].textContent.match(/\d+/)[0];
  var courseNodes = document.getElementsByClassName('course-card');

  var trigger = setInterval(function() {
    if (courseNodes.length > parseInt(count)) {
      clearInterval(trigger);
      for (var i = 0; i < courseNodes.length; i++) {
        var c = {};
        var course = courseNodes[i];
        if (course.children[0].className == "discovery-card-inner-wrapper") {
          course = course.children[0];
        }
        c.author = course.children[1].textContent;
        c.title = course.children[2].textContent + course.children[3].textContent;
        c.link = course.children[0].href;
        c.availability = course.children[4].textContent;
        c.date = course.children[5].textContent;
        courses[courses.length] = c;
      }
      var json = JSON.stringify(courses);
      var saveUrl = "http://localhost/sprint1/edxSave.php";
      send(json, saveUrl, '', '', '');
    } else {
      window.scrollBy(0, 10000)
    }
  }, 700);
}

function udemy_last_page(){
  var pagi = document.getElementsByClassName('pagi')[0];
  var num = pagi.lastChild.previousElementSibling.previousElementSibling.children[0].innerHTML;
  num = parseInt(num)
  return num
}

function getJsonFromUrl() {
  var query = location.search.substr(1);
  var result = {};
  query.split("&").forEach(function(part) {
    var item = part.split("=");
    result[item[0]] = decodeURIComponent(item[1]);
  });
  return result;
}

function extractDomain(url) {
    var domain;
    if (url.indexOf("://") > -1) {
        domain = url.split('/')[2];
    }
    else {
        domain = url.split('/')[0];
    }
    domain = domain.split(':')[0];
    return domain;
}
window.onload = function() {
  var time = (Math.floor(Math.random() * 6) + 3) * 1000;
  if (window.location.host == "www.udemy.com") {
    setTimeout(function(){
          scrapeUdemy()
      }, time);
  }
  if (window.location.host == "www.edx.org") {
    scrapeEdx()
  }
}
function send(_data, saveUrl, url, currentPage, lastPage) {
  var form = document.createElement('form');
  form.method = 'post';
  form.action = saveUrl;
  var inputs = [];
  for (var i = 0; i < 4; i++) {
    inputs[i] = document.createElement('input');
  }
  inputs[0].name = 'data';
  inputs[0].value = _data;

  inputs[1].name = 'currentPage';
  inputs[1].value = currentPage;

  inputs[2].name = 'lastPage';
  inputs[2].value = lastPage;

  inputs[3].name = 'url';
  inputs[3].value = url;

  for (var i = inputs.length - 1; i >= 0; i--) {
    form.appendChild(inputs[i]);
  }
  form.style.display = 'none';
  document.body.appendChild(form);
  form.submit();
}
