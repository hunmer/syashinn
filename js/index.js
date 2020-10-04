var g_viewing = {
	id: 0,
	json: undefined,
	dom: undefined,
	warp: undefined
};

var g_page = {
	"-1": {
		sid: 0,
		url: '',
		page: 1
	}
};
$(function() {
	M.AutoInit();
	//return viewList();

	window.history.pushState(null, null, "#");
     window.addEventListener("popstate", function(event) {
        window.history.pushState(null, null, "#");
        event.preventDefault(true);
		event.stopPropagation();
		$('#modal1').modal('close');
     });

	$(document).on('click', '#tabs-swipe-demo li', function(event) {
		var id = $(this).attr('data-id');
		setHost_byId(id);
	});
	loadData();

	g_timer = setInterval(function(){
	   // 取屏幕中间元素
		var d = $(document.elementFromPoint($(this).width() / 2, $(this).height() / 2));
		var sid = d.attr('data-sid');
		if(sid === undefined) return;

		if(!d.hasClass('._active')){
			$('._active').removeClass('._active');
			d.addClass('._active');
			console.log(sid);
		}

		if(d.attr('loaded') == undefined){
			d.attr('loaded', 1);
			//return;
			var v = g_viewing;
			var p = getPage_byId(v.id);
			var url = './php/api.php?id='+v.id+'&sid='+sid+'&url=&page=1';
			console.log(url);

			var html = '';
			//setLoading(true);
			$.getJSON(url,  function(json, textStatus) {
				  console.log(json);
				  var dom = $('#warp-'+json.id).find('div[data-sid='+json.sid+']');
				  //console.log(`#('warp-`+json.id+`').find(div[data-sid=`+json.sid+'])');
				  if(dom.length === 0) return;

				  dom.find('.progress').hide();
				  for(var d of json['res']){
				  	//console.log(d.cover);
				  	html = html + `<a class="carousel-item" href="javascript: viewList(`+json.id+`,`+json.sid+`, '`+d.cover+`', '`+d.url+`')"><img src="`+d.cover+`"></a>`;
				  }
				  if(html != ''){
				  	$('<div class="carousel">'+html+'</div>').appendTo(dom.find('._album')).carousel({
				  		indicators: true,
				  		dist: 0,
				  		fullWidth: true
				  	});
				}
			}).always(function(){
				//setLoading(false);
			});
		}
	}, 500);

	// $(window).scroll(function(){
		
	// });
});

var g_clipboard;
 function openCopyDialog(content, title = ''){
   var h = `<div class="modal-content">
   <h3>`+title+`</h3>
    <div class="input-field col s12">
      <textarea id="textarea2" class="materialize-textarea" data-length="9999">`+content+`</textarea>
    </div>
  </div>
   <div class="modal-footer">
      <a href="javascript: void(0)" id='copy' class="modal-close waves-effect waves-red btn-flat" data-clipboard-target="#textarea2">Copy</a>
      <a href="javascript: void(0)" class="modal-close waves-effect waves-red btn-flat">Cancel</a>
      </div></div>`;
  $('#modal2').html(h).modal('open');
  M.textareaAutoResize($('#textarea2'));

  // if (g_clipboard != undefined) {
  //     g_clipboard.destroy();
  // }

  // g_clipboard = new Clipboard('#copy');
  // g_clipboard.on('success', function(e) {
  //     e.clearSelection();
  //     alert('Copied!');
  // });
  // g_clipboard.on('error', function(e) {
  //     alert($(e.trigger).attr('data-clipboard-text'));
  // });
}

function viewList(id, sid, img = '', url = ''){
	var get = './php/getPage.php?id='+id+'&url='+encodeURIComponent(url)+'&page=1';
	console.log(get);

	if(img == '') img = 'img/download.jpg';
	// <img class="col s4 scale-transition" src="img/1.jpg">
	// <img class="col s4 scale-transition" src="img/2.jpg">		
	// <img class="col s4 scale-transition" src="img/3.jpg">
	// <img class="col s4 scale-transition" src="img/1.jpg">
	// <img class="col s4 scale-transition" src="img/2.jpg">		

	$('#modal1').html(`
	<div class="parallax-container">
		<i onclick="javascript: $('#modal1').modal('close');" class='material-icons btn_large waves-effect waves-light _manu_btn _p_left large '>arrow_back</i>
		<i onclick="javascript: showLinks();" class='material-icons right btn_large waves-effect waves-light _manu_btn _p_right medium cyan pulse'>file_download</i>
      <div class="parallax"><img src="`+img+`"></div>
    </div>
	<div class="row view_content">
		<div class="progress">
	      <div class="indeterminate"></div>
	   </div>
	</div>
		`).modal('open').css(
	{
		width: '100%',
		height: '100%',
		// top: $(this).height()+'px !important',
   		maxHeight: 'unset'
	}).animate({
		top: 0,
	}, 500);
	$('.parallax').parallax();

	$.getJSON(get,  function(json, textStatus) {
		  console.log(json);
		  var pics = '';
		  g_s_urls = '';
		  for(var d of json['cover']){
		  	g_s_urls = g_s_urls + "\r\n" + d;
		  	pics = pics + `<img class="col s4 scale-transition" src="`+d+`">`;
		  }
		$('#modal1').find('.view_content').html(pics);
	}).always(function(){
		$('#modal1').find('.progress').hide();
	});
}


function showLinks(){
	openCopyDialog(g_s_urls, "ImageLinks");
}

var g_s_urls = '';

function setLoading(b){
	$('#loading').css('display', b ? 'block' : 'none');
}

function setHost_byId(id){
	var dom = $('li[data-id="'+id+'"]');
	g_v_config.lastID = id;
	local_saveJson('config', g_v_config);

	g_viewing.dom = dom;
	g_viewing.id = id;
	g_viewing.warp = $('#warp-'+id);
	var j = getJson_wen_byId(id);
	g_viewing.json = j;
	if(j === undefined) return;
	console.log(id, dom.find('a').html());

	if(g_page[id] == undefined){
		g_page[id] = {
			'page': 1,
			'sid': 0,
			'url': ''
		};

		var i = 0;
		while(j['label-'+i] != undefined){
			for(var s of arr = j['label-'+i].split(';')){
				$(`
					<div class='col s12 teal lighten-2 _card' data-sid="`+i+`">
						<h2>`+s+`</h2>
						<div class="_album">
							<div class="progress _loading">
						      <div class="indeterminate"></div>
						  </div>

						</div>
					</div>
					`).appendTo(g_viewing.warp);
				i++;
			}
		}
		return loadPage();
	}

	if(g_s_sid != ''){
		var d = $('#warp-'+id).find('div[data-sid='+g_s_sid+']');
		if(d.length > 0) $('html,body').animate({ scrollTop: d.offset().top}, 500);
		g_s_sid = '';
	}
	console.log(g_viewing);
}

function initLables(){
	var res = [];
	for(var j of g_v_webs){
		var i = 0;
		while(j['label-'+i] != undefined){
			var a_i = j['labelid-'+i].split(';');
			var a_n = j['label-'+i].split(';');
			for(var c=0;c<a_n.length;c++){
				if(a_i[c] != undefined){
					if(res[a_n[c]] == undefined) res[a_n[c]] = [];
					res[a_n[c]].push([j.webname, j.id, c]);
				}
			}
			i++;
		}
	}

	var html = '';
	for(var d in res){
		if(d !== ''){
			html = html + `
			<li>
				<div class="collapsible-header">`+d+`</div>
				<div class="collapsible-body">
					<div class="collection">`;
			for(var d1 of res[d]){
				html = html + `<a href="javascript: toHost(`+d1[1]+`, `+d1[2]+`)" class="collection-item">`+d1[0]+`</a>`;
			}
			html = html + '</div></div></li>';
		}
	}
	$('#slide-out .collapsible').html(html).collapsible();
}


function addHost(j){
	var warp = $('<div id="warp-'+j.id+`" class="col s12">
		<div class="grid__sizer"></div>
	</div>`);
	$('#warps').append(warp);

	$('#tabs-swipe-demo').append('<li class="tab col s3" data-id="'+j.id+'"><a href="#warp-'+j.id+'">'+j.webname+'</a></li>');

	if(j.id == g_v_config.lastID){
		setHost_byId(j.id);
	}
}



function loadData(){
	if(true || g_s_md5 != g_v_config.md5){
		$.getJSON('./php/web.json',  function(json, textStatus) {
			console.log('loadData!');
			g_v_webs = json;
			g_v_config.md5 = g_s_md5;
			local_saveJson('config', g_v_config);
			local_saveJson('webs', g_v_webs);
			initData();
		});
	}else{
		initData();
	}
}

var g_s_sid;
function toHost(id, sid = ''){
	$('.sidenav').sidenav('close');
	$('li[data-id='+id+']').click();
	g_s_sid = sid;
}

function initData(){
	var i = 0;
	var a = ["169", "8", "151"];
	for(var d of g_v_webs){
		// if(++i >= 20) break;
		//if(a.indexOf(d.id) !== -1){
			addHost(d);
		//}
	}
	initLables();

	// if(g_viewing.warp === undefined){
	// 	$('#tabs-swipe-demo li').click();
	// 	console.log('改为默认站点');
	// }
}

function loadPage(){
	return;
	var v = g_viewing;
	var p = getPage_byId(v.id);
	var url = './php/api.php?id='+v.id+'&sid='+p.sid+'&url='+p.url+'&page='+p.page;
	console.log(url);

	var img_loaded = 0;
	$.getJSON(url,  function(json, textStatus) {
		  console.log(json);
		  var container = g_viewing.warp;
		  for(var d of json['res']){
		  	console.log(d.cover);
	  		var dom = $(
				`<div class="_item"> 
					<img class='grid__img' src='`+ d.cover + `' data-src="`+d.cover +`" alt='' style="height:`+getClientHeight() / 3+`px;border:1px solid white;" 
					>
				</div>
			`);
		  }
	});
}

function nextPage(){
}

function getJson_wen_byId(id){
	for(var d of g_v_webs){
		if(d.id == id){
			return d;
		}
	}
}

function getPage_byId(id){
	return g_page[id];
}

function getClientHeight(){
	return $(this).height();
}