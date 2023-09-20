
var apiURL = "./api.php";
var primaryKeyField = '';

function setPrimaryKeyField(primaryField){
    primaryKeyField = primaryField;
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


function viewEdit(view_method,keyId){
    window.location = './?method='+view_method+'&action=show&actionType=viewForm&'+primaryKeyField+'='+keyId;
}


function apiGetList(view_method,tableDest) {
    loading_show(tableDest);

    const xmlhttp = new XMLHttpRequest();
        xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        console.log(repponseJson.data);
        var tableHeader = ''; 
        var tableBody = ''; 
        for (const key in repponseJson.data){
            tableHeader = '';
            tableBody+="<tr>";
            let keyId = 0;
            for (const colname in repponseJson.data[key]){
                let tdClass= '';
                if(primaryKeyField == colname){
                    tdClass= 'bg-primary';
                    keyId = repponseJson.data[key][colname];
                }
                tableHeader+='<th class="'+tdClass+'">'+colname+'</th>';
                tableBody+='<td >'+repponseJson.data[key][colname]+'</td>';
            }
            tableBody+='<td> <a class="btn btn-default" href="#" onclick="viewEdit(\''+view_method+'\',\''+keyId+'\')"> Edit </a> | ';
            tableBody+=' <a class="btn btn-danger" href="#" onclick="apiDelete(\''+view_method+'\',\''+keyId+'\')"> Delete </td>';
            tableBody+="</tr>";
        }
        tableHeader += '<th class="td-actions"></th>';
        document.getElementById(tableDest).innerHTML = '<thead><tr>'+tableHeader+' </tr></thead><tbody>' + tableBody +'</tbody>';
    }
    xmlhttp.open("GET", apiURL+"?method="+view_method+"&action=index&actionType=API1.1");
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
    var params = primaryKeyField+"="+keyId;

    xmlhttp.onload = function() {
        let repponseJson =  JSON.parse(xmlhttp.responseText) ;
        console.log(repponseJson.data);
        if(repponseJson.status==200){
            alert(repponseJson.message);
            apiGetList(api_method,'table-list');
        }else{
            alert('Operation fail.')
        }
    }
    xmlhttp.open("POST", apiURL+"?method="+api_method+"&action=destroy&actionType=API1.1");
    
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}