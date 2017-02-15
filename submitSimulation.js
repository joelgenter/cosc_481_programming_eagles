var mutants = 1;

function addMutantField() {
    mutants++;
    var newInput = "<div class=\"form-group\"><input type=\"text\" class=\"form-control\"" +
        " id=\"mutant" + mutants + "\" aria-describedby=\"mutantHelp\" placeholder=\"mutant " + mutants + "\"></div></div>";
    $("#mutantList").append(newInput);
}

$(document).ready(function() {
    $("#pdbUpload").prop("disabled", true);

    //Toggle PDB Options
    $("#pdbSearchOption").on('change', function() {
        console.log("Search Selected");
        $("#pdbSearch").prop("disabled", false);
        $("#searchButton").prop("disabled", false);
        $("#pdbUpload").prop("disabled", true);
    });
    $("#pdbUploadOption").on('change', function() {
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
    $("#mutantListOption").on('change', function() {
        console.log("List Selected");
        //Disable Range
        $("#mutant").prop("disabled", true);
        $("#residueRangeStart").prop("disabled", true);
        $("#residueRangeEnd").prop("disabled", true);
        //Enable List
        $("#mutant1").prop("disabled", false);
        $("#addMutantButton").prop("disabled", false);
    });
    $("#mutantRangeOption").on('change', function() {
        console.log("Range Selected");
        //Enable Range
        $("#mutant").prop("disabled", false);
        $("#residueRangeStart").prop("disabled", false);
        $("#residueRangeEnd").prop("disabled", false);
        //Disable List
        mutants = 1;
        $("#mutantList").html("<div class=\"form-group\">" +
          "<input type=\"text\" class=\"form-control\" id=\"mutant1\" aria-describedby=\"mutantHelp\" placeholder=\"mutant 1\">" +
          "<small id=\"mutantHelp\" class=\"form-text text-muted\">Example \"Y213A, Y216A, F144A\"</small></div>");
        $("#mutant1").prop("disabled", true);
        $("#addMutantButton").prop("disabled", true);
    });
});

//Saves selected option to database
function submitPdbSearch(){
var content = "<iframe src=\"http://www.rcsb.org/pdb/results/results.do?tabtoshow=Current&qrid=D4A306CD\" width=\"100%\" height=\"500px\"></iframe>";
  BootstrapDialog.show({
            title: 'Select PDB File',
            message: content,
            cssClass: 'login-dialog',
            buttons: [{
                label: 'Submit',
                cssClass: 'btn-primary',
                action: function(dialog){
                  document.getElementById("myForm").submit();
                    dialog.close();
                }
            },
            {
                label: 'Cancel',
                cssClass: 'btn-primary',
                action: function(dialog){
                    dialog.close();
                }
            }
          ]


        });
}
