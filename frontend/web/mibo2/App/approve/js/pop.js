!function(){function t(){var t=document.createElement("div"),e='<img id="popImg" src="http://mblive.oss-cn-hangzhou.aliyuncs.com/mblive/alert.png"><p id="popText"></p>';t.setAttribute("class","pop-info"),t.setAttribute("id","popInfo"),t.innerHTML=e,document.body.appendChild(t)}var e=!1;window.popInfo=function(n,o){e||(t(),e=!0);var p=document.getElementById("popImg"),i=document.getElementById("popInfo");0==o?p.style.display="none":1==o?p.style.display="block":alert("类型参数错误"),document.getElementById("popText").innerHTML=n,"pop-info"==i.getAttribute("class")&&setTimeout(function(){i.style.display="none",i.setAttribute("class","pop-info")},2e3),i.style.display="block",i.setAttribute("class","pop-info pop-hid")}}();