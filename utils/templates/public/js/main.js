
var webURL = "";
var apiURL = "./api/";
var mvcMethod = "";

function setWebUrl(weburl){
    webURL = weburl;
    apiURL = webURL + 'api/'
}


function setMethod(pMethod){
    mvcMethod = pMethod;
}

var primaryKeyFields = [];

var listMainInfo = [];
function setPrimaryKeyField(primaryField){
    primaryKeyFields.push(primaryField);
}

var loadingMsg = `
    <div style="border:1px gray solid;">
    <div id="loadin-msg" style="
        background: blue;
        height: 24px;
        width: 10px;
    "></div>
    </div>`;

let progress;
let timerId;

function loading_show(show_in){
    document.getElementById(show_in).innerHTML = loadingMsg;
    progress=0;
    setInterval(() => {
        loadinDiv = document.getElementById('loadin-msg');
        if(loadinDiv!=undefined){
            updateProgressBar(loadinDiv);
        }
    }, 40);
}

function updateProgressBar(loadinDiv) { 
    progress = (progress+1)%100;
    loadinDiv.style.width = progress + "%";
} 


function viewAdd(){
    window.location = webURL+mvcMethod+'/create/';
}

function viewEdit(paramskeyId){
    let paramsURL = '';
    for (const key in listMainInfo[paramskeyId]){
        paramsURL+=key+'/'+listMainInfo[paramskeyId][key];
    }
    window.location = webURL+mvcMethod+'/edit/'+paramsURL;
}

function viewShow(paramskeyId){
    let paramsURL = '';
    for (const key in listMainInfo[paramskeyId]){
        paramsURL+=key+'/'+listMainInfo[paramskeyId][key];
    }
    window.location = webURL+mvcMethod+'/show/'+paramsURL;
}


function apiGetList(view_method,tableDest) {
    loading_show(tableDest);
	setMethod(view_method)
    const xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        var tableHeader = ''; 
        var tableBody = ''; 
        listMainInfo = [];
        for (const key in repponseJson.data){
            tableHeader = '';
            tableRow = "";
            let keyId = 0;
            let params = [];
            listMainInfo[key] = {};
            for (const colname in repponseJson.data[key]){
                let tdClass= '';
                for (const pkey in primaryKeyFields){
                    if(primaryKeyFields[pkey] == colname){
                        tdClass= 'bg-primary';
                        listMainInfo[key][colname] = repponseJson.data[key][colname];
                        tableHeader = '<th class="'+tdClass+'">'+colname+'</th>'+ tableHeader;
                        tableRow='<td>'+repponseJson.data[key][colname]+'</td>' + tableRow;
                        break;
                    }
                }
                if (tdClass==''){
                    tableHeader+='<th class="'+tdClass+'">'+colname+'</th>';
                    tableRow+='<td >'+repponseJson.data[key][colname]+'</td>';
                }
            }
            tableRow+='<td> <a class="btn btn-primary" href="#" onclick="viewShow(\''+key+'\')"> <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View </a> ';
            tableRow+=' <a class="btn btn-default" href="#" onclick="viewEdit(\''+key+'\')"> <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit </a>  ';
            tableRow+=' <a class="btn btn-danger" href="#" onclick="apiDelete(\''+key+'\')"> <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete </td>';
            tableBody+= '<tr>'+tableRow+'</tr>';
        }
        tableHeader += '<th class="td-actions"></th>';
        document.getElementById(tableDest).innerHTML = '<thead><tr>'+tableHeader+' </tr></thead><tbody>' + tableBody +'</tbody>';
    }
    xmlhttp.open("GET", apiURL + mvcMethod +"/index/");
    xmlhttp.send();

}


function apiDelete(keyId){
 /*
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "./?method="+mvcMethod+"&action=delete&actionType=viewForm&"+fieldName+"="+keyId);
 
    // Create an input element for Full Name
    var FN = document.createElement("input");
    FN.setAttribute("type", "hidden");
    FN.setAttribute("name", fieldName );
    FN.setAttribute("placeholder", keyId);
    FN.setAttribute("value", "Submit");
    form.appendChild(FN);
    form.submit();*/
    const xmlhttp = new XMLHttpRequest();
    let params = '';
    for (const pKey in listMainInfo[keyId]){
        if(params!=''){
            params += '&';
        }
        params += pKey + "=" +listMainInfo[keyId][pKey];
    }

    xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        if(repponseJson.status==200){
            alert(repponseJson.message);
            apiGetList(mvcMethod,'table-list');
        }else{
            alert('Operation fail.')
        }
    }
    xmlhttp.open("POST", apiURL+"/"+mvcMethod+"/destroy");
    
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}


function apiUpdate(){
    const formElement = document.getElementById("form-"+mvcMethod);
    const formData = new FormData(formElement);

    const xmlhttp = new XMLHttpRequest();

    xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        if(repponseJson.status==200){
            alert(repponseJson.message);
        }else{
            alert('Operation fail.')
        }
    }

    xmlhttp.open(formElement.method, apiURL+"/"+mvcMethod+"/update");
    xmlhttp.send(formData);
}

function loadCatalog(){

    var catalogTable = document.getElementsByClassName('catalog-item')
    var catalog = [];
    if (catalogTable != undefined && catalogTable.length > 0 ){
        for (let item of catalogTable ) {
            catalog.push( $( item ).text() );
        }
        localStorage.setItem('catalog', JSON.stringify(catalog));

    }else{
        tmpJSON = localStorage.getItem('catalog');
        
        catalog = JSON.parse(tmpJSON);
    }

    if(catalog == undefined){
        window.location = webURL;
    }
    var navList = document.getElementById('catalog-list');

    for (let method of catalog ) {
        let li = '<li><a href="'+webURL+method+'/index/" > '+method+' </a></li>';
        navList.innerHTML = navList.innerHTML + li   ;
    }
}