
function getGETArray(s_url){
	if(s_url === '') return [];
	
	var a_result = [], a_exp;
	var a_params = s_url.slice(1).split('&');
	for(var k in a_params){
		a_exp = a_params[k].split('=');
		if(a_exp.length > 1){
			var key = a_exp.splice(0, 1, a_exp);
			var value = a_exp.join('=')
			a_result[key] = value.substr(1, value.length-1);
		} 
	}
	return a_result;
}

function getParam(k, df = ''){
	return _GET[k] != undefined ? _GET[k] : df;
}

function getTextbyStartAndEnd_string(s_text, s_start, s_end, i_start = 0, b_save = false){
	i_start = s_text.indexOf(s_start, i_start);
	if(i_start === -1) return '';
	i_start += s_start.length;
	i_end = s_text.indexOf(s_end, i_start);
	if(i_end === -1) return '';
	if(b_save){
		return s_start+ s_text.substr(i_start, i_end-i_start) +s_end;
	}else{
		return s_text.substr(i_start, i_end-i_start);
	}
}

function local_saveJson(key, data) {
    if (window.localStorage) {
        key = g_localKey + key;
        data = JSON.stringify(data);
        if(data == undefined) data = '[]';
        return localStorage.setItem(key, data);
    }
    return false;
}

function local_readJson(key, defaul = '') {
    if(!window.localStorage) return defaul;
    key = g_localKey + key;
    var r = JSON.parse(localStorage.getItem(key));
    return r === null ? defaul : r;
}

function getLocalItem(key, defaul = '') {
    var r = null;
    if(window.localStorage){
        r = localStorage.getItem(g_localKey + key);
    }
    return r === null ? defaul : r;
}

function setLocalItem(key, value) {
    if(window.localStorage){
       return localStorage.setItem(g_localKey + key, value);
    }
    return false;
}