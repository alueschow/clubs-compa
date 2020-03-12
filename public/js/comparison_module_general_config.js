// show tie label if ties are allowed
var middle_button_div = document.getElementById('app_configuration_middleButton');
var tie_checkbox = document.getElementById('app_configuration_allowTie');
var showTieLabel = function(){
    if(tie_checkbox.checked) {
        middle_button_div.classList.remove('hidden');
    } else {
        middle_button_div.classList.add('hidden');
    }
};
tie_checkbox.onclick = showTieLabel;
showTieLabel();

// Show URL input when iFrame desired
var presentation_mode_frame = document.getElementById('app_configuration_presentationMode_0');
var presentation_mode_title = document.getElementById('app_configuration_presentationMode_1');
var presentation_mode_image = document.getElementById('app_configuration_presentationMode_2');
var presentation_fields_div = document.getElementById('app_configuration_presentationFields');
var url_div = document.getElementById('app_configuration_url');
var showUrl = function(){
    if(presentation_mode_frame.checked) {
        url_div.classList.remove('hidden');
        presentation_fields_div.classList.add('hidden');
    }
    if(presentation_mode_title.checked) {
        url_div.classList.add('hidden');
        presentation_fields_div.classList.remove('hidden');
    }
    if(presentation_mode_image.checked) {
        url_div.classList.remove('hidden');
        presentation_fields_div.classList.add('hidden');
    }
};
if (presentation_mode_frame != null) presentation_mode_frame.onclick = showUrl;
if (presentation_mode_title != null) presentation_mode_title.onclick = showUrl;
if (presentation_mode_image != null) presentation_mode_image.onclick = showUrl;
if (presentation_mode_frame != null && presentation_mode_title != null && presentation_mode_image != null) showUrl();

// Show URL input when iFrame desired
var name_field_1 = document.getElementById('app_configuration_presentationFieldName_1');
var name_field_2 = document.getElementById('app_configuration_presentationFieldName_2');
var name_field_3 = document.getElementById('app_configuration_presentationFieldName_3');
var name_field_4 = document.getElementById('app_configuration_presentationFieldName_4');
var presentation_field_1 = document.getElementById('app_configuration_presentationFields_0');
var presentation_field_2 = document.getElementById('app_configuration_presentationFields_1');
var presentation_field_3 = document.getElementById('app_configuration_presentationFields_2');
var presentation_field_4 = document.getElementById('app_configuration_presentationFields_3');
var showFieldNames = function(){
    if(presentation_field_1.checked) {
        name_field_1.classList.remove('hidden');
    } else {
        name_field_1.classList.add('hidden');
    }
    if(presentation_field_2.checked) {
        name_field_1.classList.remove('hidden');
        name_field_2.classList.remove('hidden');
    } else {
        name_field_2.classList.add('hidden');
    }
    if(presentation_field_3.checked) {
        name_field_1.classList.remove('hidden');
        name_field_2.classList.remove('hidden');
        name_field_3.classList.remove('hidden');
    } else {
        name_field_3.classList.add('hidden');
    }
    if(presentation_field_4.checked) {
        name_field_1.classList.remove('hidden');
        name_field_2.classList.remove('hidden');
        name_field_3.classList.remove('hidden');
        name_field_4.classList.remove('hidden');
    } else {
        name_field_4.classList.add('hidden');
    }
};
if (presentation_field_1 != null) presentation_field_1.onclick = showFieldNames;
if (presentation_field_2 != null) presentation_field_2.onclick = showFieldNames;
if (presentation_field_3 != null) presentation_field_3.onclick = showFieldNames;
if (presentation_field_4 != null) presentation_field_4.onclick = showFieldNames;
if (presentation_field_1 != null && presentation_field_2 != null && presentation_field_3 != null && presentation_field_4 != null) showFieldNames();

var group_by = document.getElementById('app_configuration_groupBy');
var group_by_div = document.getElementById('app_configuration_group_by_options');
var first_elem_group_by_category = document.getElementById('app_configuration_groupByCategory_0');
var randomization_div = document.getElementById('app_configuration_randomization_div');
var showGroupByOptions = function() {
    if(group_by.checked) {
        group_by_div.classList.remove('hidden');
        randomization_div.classList.remove('hidden');
    } else {
        group_by_div.classList.add('hidden');
        first_elem_group_by_category.checked = true;
        randomization_div.classList.add('hidden');
    }
};
if (group_by != null) showGroupByOptions();
if (group_by != null) group_by.onclick = showGroupByOptions;


var randomization = document.getElementById('app_configuration_randomization');
var document_order_div = document.getElementById('app_configuration_document_order');
var first_elem_document_order = document.getElementById('app_configuration_documentOrder_0');
var showDocumentOrder = function() {
    if(!randomization.checked) {
        document_order_div.classList.remove('hidden');
    } else {
        document_order_div.classList.add('hidden');
        first_elem_document_order.checked = true;
    }
};
randomization.onclick = showDocumentOrder;
showDocumentOrder();


var useBaseWebsite = document.getElementById('app_configuration_useBaseWebsite');
var showRandomization = function() {
    if(useBaseWebsite.checked) {
        randomization_div.classList.remove('hidden');
    } else {
        randomization_div.classList.add('hidden');
    }
};
if (useBaseWebsite != null) showRandomization();
if (useBaseWebsite != null) useBaseWebsite.onclick = showRandomization;





