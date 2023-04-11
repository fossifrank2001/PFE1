/**
 * 
 * @param {String} url 
 * @param {Object} options 
 * @returns {JSON}
 */
export async function fecthJson(url, options={}){
    const  headers = {Accept:'application/json', ...options.headers}
    const r = await fetch(url, headers)
    if(r.ok){
        return r.json()
    }
    throw new Error('404 Content not found', {cause: r});
    
}
/**
 * https://chakap.tv/api
 * @returns {String}
 */
export const base = () =>'https://chakap.tv/api'
/**
 * https://chakap.tv
 * @returns {String}
 */
export const baseUrl = () =>'https://chakap.tv'
/**
 * http://127.0.0.1:8880/
 * @returns {String}
 */
export const backLink = () =>'http://127.0.0.1:8880/'