/*
##########################################################################################################
###### Project   : Continuous rss scrolling  														######
###### File Name : continuous rss scrolling.js                   									######
###### Purpose   : This javascript is to scroll the announcement.  									######
###### Created   : Sep 4th 2010                  													######
###### Modified  : Jan 9th 2011                  													######
###### Author    : Gopi.R (http://www.gopiplus.com/work/)                       					######
###### Link      : http://www.gopiplus.com/work/2010/09/05/continuous-rss-scrolling/        		######
##########################################################################################################
*/
	

function crs_scroll() {
	crs_obj.scrollTop = crs_obj.scrollTop + 1;
	crs_scrollPos++;
	if ((crs_scrollPos%crs_heightOfElm) == 0) {
		crs_numScrolls--;
		if (crs_numScrolls == 0) {
			crs_obj.scrollTop = '0';
			crs_content();
		} else {
			if (crs_scrollOn == 'true') {
				crs_content();
			}
		}
	} else {
		setTimeout("crs_scroll();", 10);
	}
}

var crs_Num = 0;
/*
Creates amount to show + 1 for the scrolling ability to work
scrollTop is set to top position after each creation
Otherwise the scrolling cannot happen
*/
function crs_content() {
	var tmp_vsrp = '';

	w_vsrp = crs_Num - parseInt(crs_numberOfElm);
	if (w_vsrp < 0) {
		w_vsrp = 0;
	} else {
		w_vsrp = w_vsrp%crs_array.length;
	}
	
	// Show amount of vsrru
	var elementsTmp_vsrp = parseInt(crs_numberOfElm) + 1;
	for (i_vsrp = 0; i_vsrp < elementsTmp_vsrp; i_vsrp++) {
		
		tmp_vsrp += crs_array[w_vsrp%crs_array.length];
		w_vsrp++;
	}

	crs_obj.innerHTML 	= tmp_vsrp;
	
	crs_Num 			= w_vsrp;
	crs_numScrolls 	= crs_array.length;
	crs_obj.scrollTop 	= '0';
	// start scrolling
	setTimeout("crs_scroll();", 2000);
}

