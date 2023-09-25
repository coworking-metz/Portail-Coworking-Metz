document.addEventListener('DOMContentLoaded', () =>{
    let menu = document.querySelector('.myaccount-menu');

    if(!menu) return;

    let current = menu.querySelector('.active .item-label').innerHTML;
    
    let burger = document.createElement('li')
    burger.className = 'burger';
    burger.innerHTML = `<button><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg">
    <rect x="4" y="4" rx="2" ry="2" width="16" height="2"/>
    <rect x="4" y="11" rx="2" ry="2" width="16" height="2"/>
    <rect x="4" y="18" rx="2" ry="2" width="16" height="2"/>
  </svg><span>${current}</span></button>
  
  `
    menu.prepend(burger);


    document.querySelector('.myaccount-menu .burger').addEventListener('click',( ) => {
        if(document.body.dataset.menuCompteOuvert) {
            delete document.body.dataset.menuCompteOuvert;
        } else {
            document.body.dataset.menuCompteOuvert=true;
        }
    })
})