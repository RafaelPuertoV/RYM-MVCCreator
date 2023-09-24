
var webURL = "";
var apiURL = "./api/";

function setWebUrl(weburl){
    webURL = weburl;
    apiURL = webURL + 'api/'
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


function viewEdit(view_method,paramskeyId){
    let paramsURL = '';
    for (const key in listMainInfo[paramskeyId]){
        paramsURL+=key+'/'+listMainInfo[paramskeyId][key];
    }
    //console.log(webURL+view_method+'/show/'+paramsURL);
    window.location = webURL+view_method+'/show/'+paramsURL;
}


function apiGetList(view_method,tableDest) {
    loading_show(tableDest);

    const xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        var tableHeader = ''; 
        var tableBody = ''; 
        listMainInfo = [];
        for (const key in repponseJson.data){
            tableHeader = '';
            tableBody+="<tr>";
            let keyId = 0;
            let params = [];
            listMainInfo[key] = {};
            for (const colname in repponseJson.data[key]){
                let tdClass= '';
                for (const pkey in primaryKeyFields){
                    if(primaryKeyFields[pkey] == colname){
                        tdClass= 'bg-primary';
                        listMainInfo[key][colname] = repponseJson.data[key][colname];
                        break;
                    }
                }
                tableHeader+='<th class="'+tdClass+'">'+colname+'</th>';
                tableBody+='<td >'+repponseJson.data[key][colname]+'</td>';
            }
            tableBody+='<td> <a class="btn btn-default" href="#" onclick="viewEdit(\''+view_method+'\',\''+key+'\')"> Edit </a> | ';
            tableBody+=' <a class="btn btn-danger" href="#" onclick="apiDelete(\''+view_method+'\',\''+key+'\')"> Delete </td>';
            tableBody+="</tr>";
        }
        tableHeader += '<th class="td-actions"></th>';
        document.getElementById(tableDest).innerHTML = '<thead><tr>'+tableHeader+' </tr></thead><tbody>' + tableBody +'</tbody>';
    }
    xmlhttp.open("GET", apiURL + view_method +"/index/"+apiURL);
    xmlhttp.send();

}


function apiDelete(api_method,keyId){
 /*
    var form = document.createElement("form");
    form.setAttribute("method", "post");
    form.setAttribute("action", "./?method="+view_method+"&action=delete&actionType=viewForm&"+fieldName+"="+keyId);
 
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
            apiGetList(api_method,'table-list');
        }else{
            alert('Operation fail.')
        }
    }
    xmlhttp.open("POST", apiURL+"/"+api_method+"/destroy");
    
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
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