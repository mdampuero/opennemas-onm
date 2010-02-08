//configuration
OAS_url ='http://publ.planetaads.com/RealMedia/ads/';
OAS_listpos = 'Top,Middle';
OAS_query = '?';
OAS_sitepage = 'xornal.com/RG';
//end of configuration
OAS_version = 10;
OAS_rn = '001234567890'; OAS_rns = '1234567890';
OAS_rn = new String (Math.random()); OAS_rns = OAS_rn.substring (2, 11);
function OAS_NORMAL(pos) {
document.write('<A HREF="' + OAS_url + 'click_nx.ads/' + OAS_sitepage +	'/1' + OAS_rns + '@' + OAS_listpos + '!' + pos + OAS_query + '" TARGET=_top>');
document.write('<IMG SRC="' + OAS_url + 'adstream_nx.ads/' + OAS_sitepage + '/1' + OAS_rns + '@' + OAS_listpos + '!' + pos + OAS_query + '" BORDER=0 ALT="Click!"></A>');
}

OAS_version = 11;
if (navigator.userAgent.indexOf('Mozilla/3') != -1)
OAS_version = 10;
if (OAS_version >= 11)
document.write('<SC'+'RIPT LANGUAGE=JavaScript1.1 SRC="' + OAS_url + 	'adstream_mjx.ads/' + OAS_sitepage + '/1' + OAS_rns + '@' + OAS_listpos + 	OAS_query + '"><\/SCRIPT>');


 document.write('');

function OAS_AD(pos) {
if (OAS_version >= 11 && typeof(OAS_RICH)!='undefined')
  OAS_RICH(pos);
else
  OAS_NORMAL(pos);
}