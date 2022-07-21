const completePreferedOrders = () => {
  if(document.getElementById('game_preferedValues')) {
    fillFormWithSelectedArray('game_preferedColors','.colors');
    fillFormWithSelectedArray('game_preferedValues','.values');
  }
}

const fillFormWithSelectedArray = (target, selector) => {
  let selected = document.querySelectorAll(selector);
  selected=Array.from(selected).map(s=> {
      return s.id;
      });
    document.getElementById(target).value=selected;
}

document.addEventListener('DOMContentLoaded', (event) => {

    function handleDragStart(e) {
      this.style.opacity = '0.4';

      dragSrcEl = this;

      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/html', this.innerHTML);
    }
  
    function handleDragEnd(e) {
      this.style.opacity = '1';
 
      items.forEach(function (item) {
        item.style.opacity = '1';
        item.classList.remove('over');
      });
    }
  
    function handleDragOver(e) {
      e.preventDefault();
      return false;
    }
  
    function handleDragEnter(e) {
      this.classList.add('over');
    }
  
    function handleDragLeave(e) {
      this.classList.remove('over');
    }

    function handleDrop(e) {
      e.stopPropagation();
    
      if (dragSrcEl !== this) {
        
        dragSrcEl.innerHTML = this.innerHTML;
        let id = this.id;
        this.id = dragSrcEl.id;
        dragSrcEl.id = id;
        if(!document.getElementById('game_preferedValues')) { //only for cards in game

          let t1 = dragSrcEl.title;
          let t2 = e.target.title;
          let class1=dragSrcEl.classList.toString();
          let class2=e.target.classList.toString();

          dragSrcEl.classList=class1.replace(t1,t2);
          this.classList=class2.replace(t2,t1);

          this.title = t1;
          dragSrcEl.title = t2;
          if(dragSrcEl.title===this.title){
            document.querySelectorAll('[title="'+this.title+'"')
                    .forEach(el => { 
                      el.parentElement.remove()
                    });
          }
        }
        this.innerHTML = e.dataTransfer.getData('text/html');


        completePreferedOrders();
      }
    
      return false;
    }

  let items = document.querySelectorAll('.container-drop .box');
  items.forEach(function(item) {
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragover', handleDragOver);
    item.addEventListener('dragenter', handleDragEnter);
    item.addEventListener('dragleave', handleDragLeave);
    item.addEventListener('dragend', handleDragEnd);
    item.addEventListener('drop', handleDrop);
  });
});

document.addEventListener("DOMContentLoaded", function(event) {
  completePreferedOrders()
});
