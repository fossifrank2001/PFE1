import { Dom, Toast, LoderImage } from "../functions/dom.js";
// import {LoderImage1 } from "./functions/dom1.js";

//........ Uploading Image for Movies
// new LoderImage1(document.querySelector('.loader_image_2'), '#custom-btn')
new LoderImage(document.querySelector('.loader_image_1'),  '#custom-btn1')
new LoderImage(document.querySelector('.loader_image_2'),  '#custom-btn2')
//............ Setting type writer
setTimeout( () => {
    Dom.typeWriter(document.querySelector('.wrapper__type__writing .title__typed'), 'Welcome to STUDIO e dashbord admin', 0, 50)
}, 500);




// const links = document.querySelectorAll('.nav-item');

// links.forEach(link => {
//     link.addEventListener('click', e => {
//         e.preventDefault();
//         links.forEach(link => {
//             link.classList.remove('active');
//         });
//         e.target.classList.add('active');
        
//     // Charger une nouvelle page ici
//     window.location = e.target.href;
//   });
// });

// // Ajouter la classe "active" au lien correspondant à la page actuelle
// const currentPage = window.location.hash;
// const activeLink = document.querySelector(`a[href="${currentPage}"]`);
// console.log(currentPage);
// if (activeLink) {
// //   activeLink.parentElement.classList.add('active');
// }
        //....
        document.querySelector('.icon__mode__screen').addEventListener('click', e=>{
            const id = e.currentTarget.id
            const text = e.currentTarget.innerText
            new Toast(id, `${text} Mode actived`, {y:66})
        })
