// Hide @K field when checkbox is not checked
var checkbox = document.getElementById('metric_forCompleteList');
var k_div = document.getElementById('metric_k');
var showHiddenDiv = function(){
    if(checkbox.checked) {
        k_div.classList.add('hidden');
    } else {
        k_div.classList.remove('hidden');
    }
};
checkbox.onclick = showHiddenDiv;
showHiddenDiv();

// Hide checkbox when R-precision is selected
var metric_name = document.getElementById("metric_name");
var cl = document.getElementById('metric_completeList');
metric_name.onchange = showHiddenConfig = function() {
    var selectedString = metric_name.options[metric_name.selectedIndex].value;
    if (selectedString === 'R-precision') {
        cl.classList.add('hidden');
        k_div.classList.add('hidden');
    } else {
        cl.classList.remove('hidden');
        if(checkbox.checked) {
            k_div.classList.add('hidden');
        } else {
            k_div.classList.remove('hidden');
        }
    }
};
showHiddenConfig();