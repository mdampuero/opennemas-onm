
(function($){
    jQuery.fn.serializeTree = function(attribute,levelString,exclude){
        var dataString='';
        var elems;
        if(exclude==undefined)elems=this.children();
        else elems=this.children().not(exclude);
        if(elems.length>0){
            elems.each(function(){
                var curLi=$(this);
                var toAdd='';
                if(curLi.find('ul').length>0){
                    levelString+='['+curLi.attr(attribute)+']';
                    toAdd=$('ul:first',curLi).serializeTree(attribute,levelString,exclude);
                    levelString=levelString.replace(/\[[^\]\[]*\]$/,'');
                }else if(curLi.find('ol').length>0){
                    levelString+='['+curLi.attr(attribute)+']';
                    toAdd=$('ol:first',curLi).serializeTree(attribute,levelString,exclude);
                    levelString=levelString.replace(/\[[^\]\[]*\]$/,'');
                }else{
                    dataString+='&'+levelString+'[]='+curLi.attr(attribute);
                }

                if(toAdd)dataString+=toAdd;
            });
      }else{
         dataString+='&'+levelString+'['+this.attr(attribute)+']=';
      }
      if(dataString)return dataString;
      else return false;};
})(jQuery);