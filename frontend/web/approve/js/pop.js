(function(){
	var popFlag = false;

	window.popInfo = function (info,type){
		if(!popFlag){
			creatPop();
			popFlag = true;
		}

		var popImg = document.getElementById('popImg'),
			popInfo = document.getElementById('popInfo');

		if(type==0){
			popImg.style.display = "none";
		}else if(type==1){
			popImg.style.display = "block";
		}else{
			alert("类型参数错误");
		}
		document.getElementById('popText').innerHTML = info;
		popInfo.setAttribute("class","pop-info");
		popInfo.style.display = "block";

		setTimeout(function(){
			popInfo.setAttribute("class","pop-info pop-hid")
			setTimeout(function(){
				popInfo.style.display = "none";
			},800);
		},1200);
	}

	function creatPop(){
		var popEl = document.createElement("div");
		var popHTML = '<img id="popImg" src="images/alert.png"><p id="popText"></p>';

		popEl.setAttribute("class","pop-info");
		popEl.setAttribute("id","popInfo");
		popEl.innerHTML = popHTML;
		document.body.appendChild(popEl);	
	}


})();