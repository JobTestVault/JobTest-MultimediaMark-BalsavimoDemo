window.Control = function Control(params) {
	var obj = jQuery('#' + params['id']);
	obj.params = params;
	obj.getParams = function getParams() {
		return obj.params;
	}
	obj.execFunc = function execFunc(action, params, onfinish) {
		if (jQuery.isFunction(params)) {
			params = params();
		}
		jQuery.post('index.php?' + obj.params.control_type + ':' + action, 
					{control_type:obj.params.control_type, control_action:action, control_data:obj.params.control_data, control_params:params},
					function(result) { 
						if (!result) return;
						obj.html(result.control_output);
						var lxx = 'control_';
						for (var x in result) {
							if (x.substr(0, lxx.length) == lxx || x == 'length') {
								continue;
							}
							obj.params[x] = result[x];
						}
						if (jQuery.isFunction(onfinish)) {
							setTimeout(onfinish, 10);
						}
					}, 'json'
					);
	}
	obj.timers = new function () {
		var timers = [];
		this.add = function add(action, interval, forever, jsArgsGetter) {
			var timer = {action:func = function() {
								if (obj.is(':hidden')) return;
								obj.execFunc(action, jsArgsGetter);
							},
						 interval:interval,
						 forever:forever,
						 id:timers.length};
			timer.remove = function () {
				clearInterval(timer.obj);
				timers[timer.id] = null;
			}
			timer.reset = function () {
				if (timer.obj) {
					clearInterval(timer.obj);
				}
				if (timer.forever) {
					timer.obj = setInterval(timer.action, timer.interval);
				} else {
					timer.obj = setTimeout(timer.action, timer.interval);
				}
				timers[timer.id] = timer;
			}
			timer.reset();
		}
	}
	obj.showObjectContent = function (obj) {
		var parse_obj = function parse_obj(obj, level) {
			var rez = '';
			for (var x in obj)	{
				rez += x + ":" + obj[x] + "\n";
			}	
			return rez;
		}
		alert(parse_obj(obj, 0));
	}
	return obj;
}