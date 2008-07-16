
YAHOO.widget.SortableList=function(elm,cfg){this.elm=$(elm);this.drop=null;this.lastTarget=false;this._tagName='li';if(cfg){if(cfg.tagname){this._tagName=cfg.tagname;}}
if(!this.elm){return false;}
this.onInit=new YAHOO.util.CustomEvent('oninit',this);}
YAHOO.widget.SortableList.prototype.init=function(){this._setupList();this.onInit.fire();}
YAHOO.widget.SortableList.prototype._setupList=function(){this.lis=this.elm.getElementsByTagName(this._tagName);$D.generateId(this.lis,'ysortable');for(var i=0;i<this.lis.length;i++){var ID=this.lis[i].id;this.lis[i]._yuiGroup=this.elm.id;new YAHOO.util.DDTarget(ID);var tmp=new YAHOO.util.DD(ID,this.elm.id);tmp.onDragDrop=this.onDragDrop;tmp.onDragOver=this.onDragOver;tmp.onMouseUp=this.onMouseUp;}}
YAHOO.widget.SortableList.prototype.onDragDrop=function(ev,id){var tar=$E.getTarget(ev);if(this.lastTarget&&(id===this.lastTarget)&&(tar.id!=id)){$D.removeClass(this.lastTarget,'yui-sortover');var tmp=$(id);if(tmp.previousSibling!=tar){tar.parentNode.removeChild(tar);tmp.parentNode.insertBefore(tar,tmp);}else{tar.parentNode.removeChild(tar);tmp.parentNode.insertBefore(tar,tmp.nextSibling);}}}
YAHOO.widget.SortableList.prototype.onDragOver=function(ev,id){if(this.lastTarget){$D.removeClass(this.lastTarget,'yui-sortover');}
this.lastTarget=id;$D.addClass(id,'yui-sortover');}
YAHOO.widget.SortableList.prototype.onMouseUp=function(ev){var tar=$E.getTarget(ev);if(this.lastTarget){$D.removeClass(this.lastTarget,'yui-sortover');}
$D.setStyle(tar,'position','static');$D.setStyle(tar,'top','');$D.setStyle(tar,'left','');}