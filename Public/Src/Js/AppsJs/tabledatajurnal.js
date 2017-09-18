var tabledata = function(){
  this.url = null;  
  this.data = {};
  this.mssg = "";
  this.env = {};
  this.http = function(){
    if(window.XMLHttpRequest){
      return new XMLHttpRequest();
    }else{
      return new ActiveXObject("Microsoft.XMLHTTP");
    }
  };    
};


tabledata.prototype.initTable = function(data){
  this.env=  data;      
  return this;
};

/*
      gunakan struktur ini
      a.initTable({
          "element":{
            "name":"TABLE",
            
            "attr" : {
              "id":"tableaaa",
              "class":"scrollbar_X txt-jurnal bordered highlight responsive-table",
              "style":"padding:5px;"},

            "column": [
                {"id":"testes","class":"","text":"Hari"},
                {"id":"","class":"","text":"Hari"}
            ]
          }
*/
tabledata.prototype.createTable = function(id){
  this.url=this.env.element.baseurl;
  var y = document.createElement(this.env.element.name.toUpperCase());
  var attr_length = this.env.element.attr;

  for(property in attr_length){
        y.setAttribute(property,attr_length[property]);
  }

  document.getElementById(id).appendChild(y);

  var tableId = this.env.element.attr.id;
  var table_object = document.getElementById(tableId);
  var header = table_object.createTHead();
  var row = header.insertRow(0);
  var column = this.env.element.column;

  for(var i = 0; i < column.length;i++){
        var cell = row.insertCell(i);
        for(property in column[i]){
              cell.setAttribute(property,column[i][property]);
              cell.innerHTML = "<b>"+column[i]['text']+"</b>"; 
        }
  }     

  return this;
};

tabledata.prototype.getData = function(url=null){
  this.url = this.url+url;
  if(this.url == null && url == null){
    var a = prompt("you may set url origin data for this table yet, input url below ");
    this.url = this.url+a;
    alert(this.url);
    if(this.data != null){
          console.log("data is valid");
    }else{
         var a = prompt("you may set page number for this table yet, input it below ");
    this.data = a; 
    }
  }
  else if(url == null){

  }
  else if(this.url == null){

  }
  return this;
};

tabledata.prototype.setUrl = function(url){
  this.url = url;
  return this;
};

tabledata.prototype.setData = function(data){
  this.data = data;
  return this;
};

