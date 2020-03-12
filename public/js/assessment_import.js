$(document).ready(function() {
    // Trigger for submission
    var startUpload = function () {
        event.preventDefault();
        document.getElementById('submit').click();
    };
    var submitButton = document.getElementById('submit-button');
    submitButton.onclick = startUpload;

    // Check for file format
    // var formButton = document.getElementById('form-button');
    // var error_div_1 = document.getElementById('labelError1');
    // var error_div_2 = document.getElementById('labelError2');
    // var modal = document.getElementById('importModal');
    // var patt = new RegExp(".*\.csv$");

    // formButton.onclick = function () {
    //     var file = document.getElementById('file').files[0];
    //     if (file == null) {
    //         error_div_1.classList.remove('hidden');
    //         error_div_2.classList.add('hidden');
    //     }
    //     if (!patt.test(file.name)) {
    //         error_div_1.classList.add('hidden');
    //         error_div_2.classList.remove('hidden');
    //     } else {
    // $('#importModal').modal('show');
    // jQuery not working
    // }
    // };
});


// show more or less document entries
var more_docs_entries = document.getElementsByClassName('more-docs-entries');
var more_docs_button = document.getElementById('more-docs-button');
var less_docs_button = document.getElementById('less-docs-button');
var showMoreDocs = function() {
    for (var i = 0; i < more_docs_entries.length; i++) {
        more_docs_entries[i].classList.remove('hidden');
    }
    more_docs_button.classList.add('hidden');
    less_docs_button.classList.remove('hidden');
};
var showLessDocs = function(){
    for (var i = 0; i < more_docs_entries.length; i++) {
        more_docs_entries[i].classList.add('hidden');
    }
    more_docs_button.classList.remove('hidden');
    less_docs_button.classList.add('hidden');
};
if (more_docs_button != null) {
    more_docs_button.onclick = showMoreDocs;
}
if (less_docs_button != null) {
    less_docs_button.onclick = showLessDocs;
}

var more_queries_entries = document.getElementsByClassName('more-queries-entries');
var more_queries_button = document.getElementById('more-queries-button');
var less_queries_button = document.getElementById('less-queries-button');
var showMoreQueries = function() {
    for (var i = 0; i < more_queries_entries.length; i++) {
        more_queries_entries[i].classList.remove('hidden');
    }
    more_queries_button.classList.add('hidden');
    less_queries_button.classList.remove('hidden');
};
var showLessQueries = function(){
    for (var i = 0; i < more_queries_entries.length; i++) {
        more_queries_entries[i].classList.add('hidden');
    }
    more_queries_button.classList.remove('hidden');
    less_queries_button.classList.add('hidden');
};
if (more_queries_button != null) {
    more_queries_button.onclick = showMoreQueries;
}
if (more_queries_button != null) {
    less_queries_button.onclick = showLessQueries;
}

var more_sr_entries = document.getElementsByClassName('more-sr-entries');
var more_sr_button = document.getElementById('more-sr-button');
var less_sr_button = document.getElementById('less-sr-button');
var showMoreSearchResults = function() {
    for (var i = 0; i < more_sr_entries.length; i++) {
        more_sr_entries[i].classList.remove('hidden');
    }
    more_sr_button.classList.add('hidden');
    less_sr_button.classList.remove('hidden');
};
var showLessSearchResults = function(){
    for (var i = 0; i < more_sr_entries.length; i++) {
        more_sr_entries[i].classList.add('hidden');
    }
    more_sr_button.classList.remove('hidden');
    less_sr_button.classList.add('hidden');
};
if (more_sr_button != null) {
    more_sr_button.onclick = showMoreSearchResults;
}
if (more_sr_button != null) {
    less_sr_button.onclick = showLessSearchResults;
}

var more_dq_entries = document.getElementsByClassName('more-dq-entries');
var more_dq_button = document.getElementById('more-dq-button');
var less_dq_button = document.getElementById('less-dq-button');
var showMoreDQCombinations = function() {
    for (var i = 0; i < more_dq_entries.length; i++) {
        more_dq_entries[i].classList.remove('hidden');
    }
    more_dq_button.classList.add('hidden');
    less_dq_button.classList.remove('hidden');
};
var showLessDQCombinations = function(){
    for (var i = 0; i < more_dq_entries.length; i++) {
        more_dq_entries[i].classList.add('hidden');
    }
    more_dq_button.classList.remove('hidden');
    less_dq_button.classList.add('hidden');
};
if (more_dq_button != null) {
    more_dq_button.onclick = showMoreDQCombinations;
}
if (more_dq_button != null) {
    less_dq_button.onclick = showLessDQCombinations;
}