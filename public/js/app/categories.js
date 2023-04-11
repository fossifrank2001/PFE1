import { fecthJson } from "../functions/api.js";
import { Dom } from "../functions/dom.js";
try {
    const categories = await fecthJson('https://chakap.tv/api/category', {
        method:'GET',
        mode:'cors',
        headers:{
        }
    })
    console.log(categories)
    const list = document.querySelector('.list-categories')
    const listCatLabel = document.querySelector('.list-cat-label')
    categories.forEach(category => {
        
        list.insertAdjacentHTML("afterbegin", `
            <div class="categorie card" style=" width:40%; height: 250px">
                <div class="card-body bg-dark" style="">
                    <div class="content d-flex w-100 h-100 flex-column justify-content-between pb-2">
                        <div class="icon"><i class="fa text-white fa-television fa-3x"></i></div>
                        <div class="text-description fs-4 text-white fw-bolder">${category.name}</div>
                    </div>
                </div>
                <div class="card-footer w-75 mx-auto d-flex justify-content-between">
                    <a href="" class="btn btn-info"><i class="fa-solid fa-pen"></i></a>
                    <a href="" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a>
                    <a href="" class="btn btn-primary"><i class="fa-solid fa-angle-up"></i></a>
                    <a href="" class="btn btn-primary"><i class="fa-solid fa-angle-down"></i></a>
                </div>
            </div>
        `)

        // listCatLabel.insertAdjacentHTML("afterbegin", `
        //     <div class="form-check form-check-inline">
        //         <input class="form-check-input" type="checkbox" id="${category.name}" value="${category.name}">
        //         <label class="form-check-label" for="${category.name}">${category.name}</label>
        //     </div>
        // `)
    });
    
} catch (e) {
    // console.error(e);
    const alertElement =Dom.createElement('div', {
        class: 'alert w-50 mx-auto text-center alert-danger',
        role: 'alert'
    })
    alertElement.innerText = 'Impossible to load content'
    document.querySelector('.section_bloc').prepend(alertElement); 
    // console.error(e)
}