
function addElement(parentId, elementTag, elementId, html) {
    var p = document.getElementById(parentId);
    var newElement = document.createElement(elementTag);
    newElement.setAttribute('id', elementId);
    newElement.innerHTML = html;
    p.appendChild(newElement);
}

function removeElement(elementId) {
    var element = document.getElementById(elementId);
    element.parentNode.removeChild(element);
}

function skill(){

}
function add_skills_form(id){
    html ='<div class="uk-inline"><span class="uk-form-icon" uk-icon="icon: hashtag"></span><input class="uk-input" type="text" name="skill'+i+'" list="skills_list" placeholder="Select or add your own skill"></div><datalist id="skills_list"><option value="Boston"><option value="Cambridge"></datalist><input type="button" class="uk-button uk-button-default requester" onclick="removeElement(\'skill'+i+'\');" value="Remove">';
    addElement(id, 'div', 'skill'+i, html);
    i++;
}