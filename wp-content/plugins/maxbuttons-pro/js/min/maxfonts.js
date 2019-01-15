var maxFonts;jQuery(document).ready(function(t){(maxFonts=function(){}).prototype={webFonts:!1,userFonts:!1,usedFonts:!1,systemFonts:["","Arial","Courier New","Georgia","Tahoma","Times New Roman","Trebuchet MS","Verdana"],delay:null,googleBaseURL:"//fonts.googleapis.com/css?family=",displayOnLoad:!0,fontsLoaded:[]},maxFonts.prototype.load=function(){this.userFonts||this.loadUserFonts(),!1===this.webFonts?this.parseWebFonts():this.showFonts("web")},maxFonts.prototype.close=function(){window.maxFoundry.maxmodal.close()},maxFonts.prototype.checkFonts=function(){var o=this;t(".mb-text, .mb-text2").each(function(){var e=t(this).css("fontFamily"),n=t(this).css("fontWeight"),a=o.getWebFamily(e,n);!1!==a&&t("head").append("<link rel='stylesheet' type='text/css' href='"+a+"' />")})},maxFonts.prototype.getWebFamily=function(o,e){if(this.systemFonts.indexOf(o)>=0)return!1;var n=JSON.parse(mb_font_options.combined_fonts),a=this,s=!1;return t.each(n,function(t,e){if(e==o){var n=o;return n=n.replace("'",""),(n=n.replace(/\s/g,"+")).indexOf(",",n)>=0?!1:!(a.fontsLoaded.indexOf(n)>=0)&&(a.fontsLoaded.push(n),s=a.googleBaseURL+n,!1)}}),s},maxFonts.prototype.delay=function(){var t={};return function(o,e,n){n=n||"defaultTimer",clearTimeout(t[n]||0),t[n]=setTimeout(o,e)}}(),maxFonts.prototype.loadDone=function(){t(".max-modal.add-fonts").find(".loading, .loading_overlay").hide(),t(".max-modal.add-fonts .fontcount").text(this.webFonts.length),t(".max-modal.add-fonts .font_search").off("keyup"),t(".max-modal.add-fonts .font_search").on("keyup",t.proxy(function(){this.delay(t.proxy(function(){this.searchkw()},this),300,"search")},this)),t(".max-modal.add-fonts .font_manager .items input").off("click"),t(".max-modal.add-fonts .font_manager .items input").on("click",t.proxy(this.renderExample,this)),t('.max-modal.add-fonts button[name="save_fonts"]').off("click"),t('.max-modal.add-fonts button[name="save_fonts"]').on("click",t.proxy(this.saveFonts,this))},maxFonts.prototype.searchkw=function(){var o=t(".max-modal .font_search input").val();t(".max-modal .font_search input").prop("disabled",!0),o=o.toLowerCase(),t(".max-modal .font_manager .items li label input").each(function(e,n){""==o||t(this).val().toLowerCase().indexOf(o)>=0?t(this).parents("li").show():t(this).parents("li").hide()}),t(".max-modal .font_search input").prop("disabled",!1)},maxFonts.prototype.renderExample=function(o){var e=o.target;"LI"!==e.nodeName&&(e=t(o.target).parents("li"));var n=t(e).find("input").val();t(".font_example .example_text span").html(n),family=this.getWebFamily(n),family&&t("head").append("<link rel='stylesheet' type='text/css' href='"+family+"' />"),t(".font_example .placeholder").hide(),t(".font_example .example_text").show().css("fontFamily",n)},maxFonts.prototype.saveFonts=function(){var o=t(".max-modal .font_manager .items li input:checked"),e={};t(o).each(function(){val=t(this).val(),e[val]=val});var n={action:"font_manager",font_action:"save",fonts:e};t.ajax({type:"POST",url:ajaxurl,data:n,success:t.proxy(this.saveDone,this)})},maxFonts.prototype.saveDone=function(o){var e=JSON.parse(o),n=e.fonts,a=e.usedfonts,s=t('#maxbuttons select[name="font"], #maxbuttons select[name="font2"]'),i=t(s).find(":selected");t(s).children("option").remove(),t.each(n,function(o,e){t(s).append('<option value="'+o+'">'+e+"</option>")}),t(s[0]).find('option[value="'+t(i[0]).val()+'"]').attr("selected","selected"),t(s[1]).find('option[value="'+t(i[1]).val()+'"]').attr("selected","selected"),mb_font_options.used_fonts=a,this.loadUserFonts(),this.showFonts("web"),this.close()},maxFonts.prototype.parseWebFonts=function(){var o=[];t.getJSON(mb_font_options.webfonts,t.proxy(function(e){t(e.items).each(function(){this.variants;var t={};t.family=this.family,t.variants=this.variants,o.push(t)}),this.webFonts=o,this.displayOnLoad&&this.showFonts("web")},this))},maxFonts.prototype.loadUserFonts=function(){var o=[],e=mb_font_options.user_fonts;""!==e?(t.each(e,function(t,e){""!==e&&o.push(e)}),this.userFonts=o):this.userFonts=[]},maxFonts.prototype.loadUsedFonts=function(){var o=[],e=mb_font_options.used_fonts;""!==e?(t.each(e,function(t,e){""!==e&&o.push(e)}),this.usedFonts=o):this.usedFonts=[]},maxFonts.prototype.showFonts=function(o){if(userfonts=this.userFonts,"web"!==o)return!1;fonts=this.webFonts;var e=".max-modal .font_manager .font_left",n=".max-modal .font_manager .font_right";t(".max-modal .font_manager").find('input[type="checkbox"]').attr("checked",!1),t(e).find(".items li").remove(),t(n).find(".items li").remove();var a,s=fonts.length,r=Math.ceil(s/2),l=e,f=!1;for(i=0;i<fonts.length;i++){i>=r&&(l=n),a=fonts[i].family,f=t.inArray(a,userfonts)>=0?"checked":"";var m=a.toLowerCase().replace(" ","");t(l+" .items").append('<li><input type="checkbox" id="'+m+'" name="userfonts[]" value="'+a+'"'+f+'><label for="'+m+'"><span>'+a+"</span></label></li>")}this.loadDone()}});