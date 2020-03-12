function buttonClick(theButton, theOtherButton){
    // set hidden field value to value of clicked button
    document.getElementById('selection').value = theButton.value;
    // set hidden field value to value of button that was not clicked
    document.getElementById('other_document').value = document.getElementById(theOtherButton).value;
    return true;
}

function tieButtonClick(tieButton, leftSide, rightSide){
    // set hidden field value to value of left button
    document.getElementById('selection').value = document.getElementById(leftSide).value;
    // set hidden field value to value of right button
    document.getElementById('other_document').value = document.getElementById(rightSide).value;
    // set hidden tie field
    document.getElementById('tie').value = tieButton.value;
    return true;
}