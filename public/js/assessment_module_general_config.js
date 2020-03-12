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
presentation_mode_frame.onclick = showUrl;
presentation_mode_title.onclick = showUrl;
presentation_mode_image.onclick = showUrl;
showUrl();


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
presentation_field_1.onclick = showFieldNames;
presentation_field_2.onclick = showFieldNames;
presentation_field_3.onclick = showFieldNames;
presentation_field_4.onclick = showFieldNames;
showFieldNames();


var query_style_only_query = document.getElementById('app_configuration_queryStyle_0');
var query_style_only_topic = document.getElementById('app_configuration_queryStyle_1');
var query_style_both = document.getElementById('app_configuration_queryStyle_2');
var query_heading_name = document.getElementById('app_configuration_queryHeadingName');
var topic_heading_name = document.getElementById('app_configuration_topicHeadingName');
var showQueryStyle = function(){
    if (query_style_only_query.checked) {
        query_heading_name.classList.remove('hidden');
        topic_heading_name.classList.add('hidden');
    } else if (query_style_only_topic.checked) {
        query_heading_name.classList.add('hidden');
        topic_heading_name.classList.remove('hidden');
    } else if (query_style_both.checked) {
        query_heading_name.classList.remove('hidden');
        topic_heading_name.classList.remove('hidden');
    }
};
if (query_style_only_query != null ) query_style_only_query.onclick = showQueryStyle;
if (query_style_only_topic != null ) query_style_only_topic.onclick = showQueryStyle;
if (query_style_both != null ) query_style_both.onclick = showQueryStyle;
if (query_style_only_query != null && query_style_only_topic != null && query_style_both != null) {
    showQueryStyle();
}



var document_heading = document.getElementById('app_configuration_documentHeading');
var document_heading_name = document.getElementById('app_configuration_documentHeadingName');
var showDocumentHeadingName = function(){
    if(document_heading.checked) {
        document_heading_name.classList.remove('hidden');
    } else {
        document_heading_name.classList.add('hidden');
    }
};
document_heading.onclick = showDocumentHeadingName;
showDocumentHeadingName();


var group_by = document.getElementById('app_configuration_groupBy');
var group_by_div = document.getElementById('app_configuration_group_by_options');
var showGroupByOptions = function() {
    if(group_by.checked) {
        group_by_div.classList.remove('hidden');
    } else {
        group_by_div.classList.add('hidden');
    }
};
if (group_by != null) showGroupByOptions();
if (group_by != null) group_by.onclick = showGroupByOptions;


var skipping_allowed = document.getElementById('app_configuration_skippingAllowed');
var skipping_options_div = document.getElementById('app_configuration_skipping_options');
var showSkippingOptions = function() {
    if(skipping_allowed.checked) {
        skipping_options_div.classList.remove('hidden');
    } else {
        skipping_options_div.classList.add('hidden');
    }
};
showSkippingOptions();

var skipping_options_reject = document.getElementById('app_configuration_skippingOptions_0');
var skipping_options_postpone = document.getElementById('app_configuration_skippingOptions_1');
var skipping_options_both = document.getElementById('app_configuration_skippingOptions_2');
var skipping_options_comment_div = document.getElementById('app_configuration_skipping_comment');
var showSkippingOptionsComment = function() {
    if(skipping_options_reject.checked && !skipping_options_div.classList.contains('hidden')) {
        skipping_options_comment_div.classList.remove('hidden');
    }
    else if(skipping_options_postpone.checked) {
        skipping_options_comment_div.classList.add('hidden');
    }
    else if (skipping_options_both.checked && !skipping_options_div.classList.contains('hidden')) {
        skipping_options_comment_div.classList.remove('hidden');
    } else {
        skipping_options_comment_div.classList.add('hidden');
    }
};
skipping_options_reject.onclick = showSkippingOptionsComment;
skipping_options_postpone.onclick = showSkippingOptionsComment;
skipping_options_both.onclick = showSkippingOptionsComment;
showSkippingOptionsComment();

skipping_allowed.onclick = function() {
    showSkippingOptions(); showSkippingOptionsComment();
};
