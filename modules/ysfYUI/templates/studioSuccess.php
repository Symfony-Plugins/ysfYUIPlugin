<?php ysfYUI::addComponent('tabview'); ysfYUI::addEvent('studio', 'ready', "var studioTabs = new YAHOO.widget.TabView('studio');"); ?>

<!--

http://developer.yahoo.com/yui/resize/

http://developer.yahoo.com/yui/container/panel/
http://developer.yahoo.com/yui/container/tooltip/
http://developer.yahoo.com/yui/container/dialog/
http://developer.yahoo.com/yui/container/module/

http://developer.yahoo.com/yui/examples/container/simpledialog-quickstart.html
http://developer.yahoo.com/yui/examples/container/panel-loading.html
http://developer.yahoo.com/yui/examples/container/tooltip.html

http://developer.yahoo.com/yui/datatable/
http://developer.yahoo.com/yui/treeview/
http://developer.yahoo.com/yui/tabview/

http://developer.yahoo.com/yui/layout/

http://developer.yahoo.com/yui/charts/
http://developer.yahoo.com/yui/examples/uploader/index.html

http://developer.yahoo.com/yui/examples/imagecropper/conn_crop.html

-->

<div class="yui-g" id="yui-content" style="text-align: center; padding: 20px; border: 1px solid #ccc; margin: 10px;"> (placeholder) </div>

<div class="yui-g">

	<div id="studio" class="yui-navset">
	  <ul class="yui-nav">
	    <li><a href="#ajax"><em>Links, Buttons, and AJAX</em></a></li>
	    <li class="selected"><a href="#forms"><em>Forms</em></a></li>
	    <li><a href="#dialogs"><em>Dialogs, Modals, and Panels</em></a></li>
			<li><a href="#effects"><em>Elements / Effects / DragDrop</em></a></li>
	  </ul>
	  <div class="yui-content">
	    <div id="ajax">
		    <ul>
		      <li><?php echo link_to('alert via link + onclick', 'content/about', array('onclick' => 'alert("xss");')); ?></li>
		      <li><?php echo yui_link_to_function('yui_link_to_function', "alert('demo');") ?></li>
		      <li><?php echo yui_link_to_remote('yui_link_to_remote', array('url' => url_for('ysfYUI/ajaxContent'), 'update' => 'yui-content')) ?></li>

		      <li><?php echo yui_button('yui_button'); ?></li>
		      <li><?php echo yui_button_to('yui_button_to', 'http://yahoo.com/'); ?></li>
		      <li><?php echo yui_button_to_function('yui_button_to_function', "alert('demo');") ?></li>
		      <li><?php echo yui_button_to_remote('yui_button_to_remote', array('url' => url_for('ysfYUI/ajaxContent'), 'update' => 'yui-content')) ?></li>
		      <li>yui_periodically_call_function (background) - <?php yui_periodically_call_function('function() { alert("yui_periodically_call_function"); }', array('frequency' => 360)); ?></li>
		      <li>yui_periodically_call_remote (background) - <?php yui_periodically_call_remote(array('url' => url_for('ysfYUI/ajaxContent?yui_periodically_call_remote=yui_periodically_call_remote'), 'update' => 'yui-content', 'frequency' => 30)); ?></li>
		    </ul>

		    <ul>
		      <li>
		        <?php echo yui_form_remote_tag(array('url' => 'ysfYUI/ajaxContent', 'update' => 'yui-content')) ?>
		        <input type="hidden" name="yui_form_remote_tag" value="" />
		        <?php echo yui_submit_tag('yui_form_remote_tag'); ?>
		        </form>
		      </li>
		      <li>
		        <form action="/ysfYUI/ajaxContent" method="post">
		          <input type="hidden" name="yui_submit_to_remote" value="" />
		          <?php echo yui_submit_to_remote('yui_submit_remote_tag', 'yui_submit_remote_tag', array('url' => 'ysfYUI/ajaxContent', 'update' => 'yui-content')) ?>
		        </form>
		      </li>
		      <li>
		        <form action="/ysfYUI/ajaxContent" method="post">
		          <input type="hidden" name="yui_submit_image_to_remote_tag" value="" />
		          <?php echo yui_submit_image_to_remote('yui_submit_image_to_remote_tag', '/static/ysf/symfony/images/icons/ok48.png', array('url' => 'ysfYUI/ajaxContent', 'update' => 'yui-content')) ?>
		        </form>
		      </li>
		    </ul>
	    </div>
	    <div id="forms">
	      <ul>
	        <li>
			      <?php echo yui_form_remote_tag(array('url' => 'ysfYUI/ajaxContent', 'update' => 'yui-content')) ?>
			      <table>
			        <?php echo $form; ?>
			        <tr><td colspan="2"><?php echo yui_submit_tag('save'); ?></td></tr>
			      </table>
			      </form>
	        </li>
	        <li>
	          yui_input_in_place_editor_tag
	        </li>
	      </ul>


	    </div>
	    <div id="dialogs">
	      <ul>
	      	<li><?php echo yui_link_to_dialog('yui_link_to_dialog'); ?></li>
	      	<li><?php echo yui_link_to_confirm_dialog('yui_link_to_confirm_dialog'); ?></li>
	        <li>yui_link_to_modal</li>
	        <li>yui_link_to_panel</li>
	        <li><span id="yui_tooltip" title="y! tooltip">yui_tooltip</span><?php yui_tooltip('yui_tooltip', 'yahoo! tooltip'); ?></li>
	      </ul>
	    </div>

	    <div id="effects">
		    <ul>
		      <li>
		        yui_visual_effect - <?php // visual_effect('appear', 'yui-content'); ?>
		      </li>
		      <li>
		        yui_update_element_function - update <?php yui_update_element_function('yui-content', array('action' => 'update', 'content' => 'some new content')); ?>
		      </li>
		      <li>
		        yui_update_element_function - empty <?php yui_update_element_function('yui-content', array('action' => 'empty')); ?>
		      </li>
		      <li>
		        yui_drop_receiving_element - <div id="dropzone" style="width: 180px; height: 90px; background-color: #eee; display: block;"><?php yui_draggable_element('dragdrop1'); ?><img src="/static/ysf/symfony/images/icons/cancel48.png" id="dragdrop1" /></div>
		        <?php yui_drop_receiving_element('dropzone', array('url' => 'ysfYUI/ajaxContent', 'update' => 'yui-content')); ?>
		      </li>
		    </ul>
	    </div>
	  </div>
	</div>

</div>