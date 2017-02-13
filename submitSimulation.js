var mutants=1;
function addMutantField(){
  mutants++;
  var newInput = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\"" +
  " id=\"mutant" + mutants +"\" aria-describedby=\"mutantHelp\" placeholder=\"mutant "+ mutants +"\"></div></div>";
  $("#mutantList").append(newInput);
}

$(document).ready(function(){
  $("#pdbUpload").prop("disabled", true);

  //Toggle PDB Options
    $("#pdbSearchOption").on('change',function(){
        console.log("Search Selected");
        $("#pdbSearch").prop("disabled", false);
        $("#searchButton").prop("disabled", false);
        $("#pdbUpload").prop("disabled", true);
    });
    $("#pdbUploadOption").on('change',function(){
        console.log("Upload Selected");
        $("#pdbSearch").prop("disabled", true);
        $("#searchButton").prop("disabled", true);
        $("#pdbUpload").prop("disabled", false);
    });

    //Toggle List and Range Options
    //Disable Range
    $("#mutant").prop("disabled", true);
    $("#residueRangeStart").prop("disabled", true);
    $("#residueRangeEnd").prop("disabled", true);
    $("#mutantListOption").on('change',function(){
        console.log("List Selected");
        //Disable Range
        $("#mutant").prop("disabled", true);
        $("#residueRangeStart").prop("disabled", true);
        $("#residueRangeEnd").prop("disabled", true);
        //Enable List
        $("#mutant1").prop("disabled", false);
        $("#addMutantButton").prop("disabled", false);
    });
    $("#mutantRangeOption").on('change',function(){
        console.log("Range Selected");
        //Enable Range
        $("#mutant").prop("disabled", false);
        $("#residueRangeStart").prop("disabled", false);
        $("#residueRangeEnd").prop("disabled", false);
        //Disable List
        $("#mutant1").prop("disabled", true);
        $("#addMutantButton").prop("disabled", true);
    });
});
