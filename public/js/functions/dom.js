import { base } from "./api.js"
export class Dom{

    static changeBackgroundColor(){

    }
    /**
     * 
     * @param {String} tagName 
     * @param {Object} attributes 
     * @return {HTMLElement}
     */
    static createElement(tagName, attributes = {}){
        const element = document.createElement(tagName)
        for(const [key, val] of Object.entries(attributes)){
            element.setAttribute(key, val)
        }
        return element
    }
    
    /**
     * 
     * @param {Element} element 
     * @param {Array | String} infos 
     * @param {Number} start
     * @param {Number} speed 
     * @param {Boolean} infinite
     */
    static typeWriter(element, infos, start=0, speed = 250, infinite=false){
        if(Array.isArray(infos)){
            infos.forEach(info => {

                if(start < info.length){
                    setTimeout(() => {
                        element.innerHTML+=`<span>${info[start]}</span>`
                        this.typeWriter(element, info, start + 1)
                    }, speed)
                }
            })
        }else{
            if(start < infos.length){
                setTimeout(() => {
                    element.innerHTML+=`<span>${infos[start]}</span>`
                    this.typeWriter(element, infos, start + 1)
                }, speed)
            }
        }
        
    }
}

//....
export class LoderImage{

    #regExp = /[0-9a-zA-Z\^\&\'\@\{\}\[\]\,\$\=\!\-\#\(\)\.\%\+\_]+$/
    
    /**
     * 
     * @param {Element | null} element 
     * @param {String} btnSelector 
     */
    constructor(element, btnSelector){
        // this.cancelBtn = element.querySelector('#cancel-btn')
        if(element === null){
            return
        }
        this.customBtn = element.querySelector(btnSelector)
        this.wrapper = element.querySelector('.wrapper')
        this.fileName  = this.wrapper.querySelector('.file-name')
        this.defaultBtn = element.querySelector('#default-btn')
        this.img = this.wrapper.querySelector('img')

        //...
        this.defaultBtn.addEventListener('change', e => this.#onchange(e))
        this.customBtn.addEventListener('click', ()=>{
            this.#defaultBtnActive()
        })
        // this.cancelBtn.addEventListener('click', e =>this.#click(e))
    }
    /**
     * 
     */
    #defaultBtnActive(){
        this.defaultBtn.click()
    }
    /**
     * 
     * @param {Event} e
     */
    #onchange(e){
        const file = e.currentTarget.files[0]
        if(file){
            const reader = new FileReader()
            console.log(reader)
            reader.onload = ()=>{
                this.img.src = reader.result
                this.wrapper.classList.add('active')
            }
            reader.readAsDataURL(file)
        }
        //  if(e.currentTarget.value){
        //     this.fileName.textContent = e.currentTarget.value.match(this.#regExp) 
        //  }
    }
}

export class Toast{

    #notificationTags={
        'error' :   '<i class="fa-solid fa-circle-xmark"></i>',
        'success' :   '<i class="fa-solid fa-circle-check"></i>',
        'info' :   '<i class="fa-solid fa-circle-info"></i>',
        'warning' :   '<i class="fa-solid fa-triangle-exclamation"></i>',
    }
    /** 
     * 
     * @param {String} indicator 
     * @param {String} message 
     * @param {Object} position
     */
    constructor(indicator ='', message, position={}){
        this.position = Object.assign({}, {x:0, y:0}, position)
        this.#createToast(indicator, message, this.#notificationTags[indicator], this.position )
    }

    #createToast(id, message, iTag, pos){
        const toast = Dom.createElement('div', {
            class: `notif ${id}  bg-white fs-6 position-absolute`, 
            style: `top:${pos.y}px; right:${pos.x}`
        })
        toast.innerHTML= `
            <div class="column">
                ${iTag}
                <span class="me-2">${id} : ${message}</span>
            </div>
        `
        const btn = Dom.createElement('i', {
            class:'fa-solid fa-xmark'
        })
        toast.append(btn)
        document.body.prepend(toast)
        btn.addEventListener('click', () => this.#removeToast(toast))
        setTimeout(() => this.#removeToast(toast), 5000) 
    }
    /**
     * 
     * @param {Element} item 
     */

    #removeToast(item){
        item.classList.add('hide')
        setTimeout(() => item.remove(), 500)
    }
}
