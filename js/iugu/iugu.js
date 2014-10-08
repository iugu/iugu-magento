/**
 *
 * @category   Inovarti
 * @package    Inovarti_Iugu
 * @author     Suporte <suporte@inovarti.com.br>
 */

var Iugu = {
    load: function(url){
        new Ajax.Request(url, {
            onSuccess: function(response) {
                var response = response.responseText.evalJSON();
                if (response.success) {
                    var win = new Window('iugu', {className:'magento', title:'Iugu', width:600, height:370, zIndex:1000, opacity:1, destroyOnClose:true, draggable: false, showEffect: Element.show});
                    win.setHTMLContent(response.content_html);
                    win.showCenter(true);
                } else {
                    alert(response.error_message);
                }
            }
        });
        return false;
    }
}
