<style>
 .ui-widget-overlay {
 	z-index: 10000 !important;
 	position: fixed !important;
 }
 .ui-dialog {
 	z-index: 10001 !important;
 }
#popup_yw_submit {
	width: auto;
	background-color: #6EA9D3;
	color: #FFF;
	padding: 8px;
	border: none;
	border-radius: 4px;
}
#popup_yw_submit:hover {
	cursor: pointer;
} 
#popup_yw_link {
	width: 250px;
}   
#popup_yw_show {
	width: 250px;
} 
</style>
<div id="popup_yw_dialog" title="<?php _e("Yada Wiki Link", 'yada_wiki_domain'); ?>" class="popup_dialog">
    <form id="popup_yw_form">
        <table class="form-table">
            <tr>
                <th><label for="popup_yw_link"><?php _e("Link:", 'yada_wiki_domain'); ?></label></th>
                <td>
                	<input type="text" name="value" id="popup_yw_link" onkeyup="javascript:doYWLookup(this.value);" size="30" />
                	<div style="font-style:italic;font-size:12px;padding-left:-10px;"><?php _e("(The title of the page to which you are linking.)", 'yada_wiki_domain'); ?></div>
                </td>
            </tr>
            <tr>
                <th><label for="popup_yw_show"><?php _e("Show:", 'yada_wiki_domain'); ?></label></th>
                <td>
                	<input type="text" name="show" id="popup_yw_show" onkeyup="javascript:checkYWSubmit(event);" size="30" />
                	<div style="font-style:italic;font-size:12px;padding-left:-10px;"><?php _e("(If blank then the value in the Link field is used.)", 'yada_wiki_domain'); ?></div>
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>  
                <td align="right"><input type="button" onclick="javascript:doYWSubmit();" name="popup_yw_submit" id="popup_yw_submit" value="<?php _e('Insert Shortcode', 'yada_wiki_domain'); ?>" /></td>
            </tr>
        </table>
    </form>
</div>
