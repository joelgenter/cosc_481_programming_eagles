var i=2;
function addMutantField(){
  var newInput = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\"" +
  " id=\"mutant" + i +"\" aria-describedby=\"mutantHelp\" placeholder=\"mutant "+ i +"\"></div></div>";
  $("#mutantList").append(newInput);
  i++;
}
