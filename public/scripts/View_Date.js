/**
 * findClass
 * Checks whether element hass CSS class; optionally adds or deletes it;
 * @param {node} node - the DOM node to check
 * @param {string} name - the name of the class to look for
 * @param {string} mode - should the function add, delete or just find?
 * @returns {Boolean} - did the operation succeed?
 */
function findClass(node, name, mode) {
    var classNames = String(node.className);
    var classArray = classNames.split(" ");
    if(classArray.indexOf(name) === -1) {
        if(mode === "add") {
            classArray.push(name);
            var success = true;
        } else {
            var success = false;
        }
    } else if(mode === "del") {
        classArray.splice(classArray.indexOf(name), 1);
        var success = true;
    } else if(mode === "find") {
        var success = true;
    } else {
        var success = false;
    }
    node.className = classArray.join(" ");
    return success;
}

/**
 * changeAnchorTargets
 * Changes anchor targets from GET request to #;
 * @param {HTMLCollection} dayLinks - the anchors to be changed
 * @returns {bool} - did the operation succeed?
 */
function changeAnchorTargets(dayLinks) {
    if(dayLinks instanceof HTMLCollection) {
        for(i = 0; i < dayLinks.length; i++) {
            dayLinks[i].setAttribute("href", "#");
        }
        return true;
    }
}

function addHiddenDateInputs(node, date) {
    ySelect = document.createElement("input");
    ySelect.setAttribute("type", "hidden");
    ySelect.setAttribute("name", "y");
    ySelect.setAttribute("value", date.getFullYear());
    node.appendChild(ySelect);
    mSelect = document.createElement("input");
    mSelect.setAttribute("type", "hidden");
    mSelect.setAttribute("name", "m");
    mSelect.setAttribute("value", date.getMonth() + 1);
    node.appendChild(mSelect);
    dSelect = document.createElement("input");
    dSelect.setAttribute("type", "hidden");
    dSelect.setAttribute("name", "d");
    dSelect.setAttribute("value", date.getDate());
    node.appendChild(dSelect);
}

/**
 * changeSelectedDay
 * Unsets selectedDay CSS class on all days and sets it on clicked day;
 * @param {HTMLCollection} dayLinks - the links to be changed
 * @returns {changeSelectedDay}
 */
function changeSelectedDay(dayLinks) {
    for(i = 0; i < dayLinks.length; i++) {
        dayLinks[i].onclick = function() {
            for(i = 0; i < dayLinks.length; i++) {
                var linkDivs = this.parentNode.parentNode.parentNode.getElementsByTagName("div");
                for(i = 0; i < linkDivs.length; i++) {
                    findClass(linkDivs[i], "selectedDay", "del");
                }
            }
            findClass(this.parentNode, "selectedDay", "add");
            selectedDateDiv = document.getElementById("calCur_div");
            date = getSelectedDay();
            date.setDate(this.textContent);
            //addHiddenDateInputs(this, date);
            var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
            selectedDateDiv.textContent = date.getDate() + " " + months[date.getMonth()] + " " + date.getFullYear();
        };
    }
}

/**
 * getSelectedDay
 * Date based on calCur_div;
 * @returns {Date}
 */
function getSelectedDay() {
    selectedDateDiv = document.getElementById("calCur_div");
    selectedDateArray = selectedDateDiv.textContent.split(" ");
    selectedDay = selectedDateArray[0];
    selectedMonth = selectedDateArray[1];
    selectedYear = selectedDateArray[2];
    var date = new Date(selectedDay + " " + selectedMonth + " " + selectedYear);
    return date;
}

function changeMonthNav(node, mode) {
    splitString = node.id.replace(/([A-Z])/, "_$1");
    nameArray = splitString.split("_");
    date = getSelectedDay();
    submitButton = addSubmit(node, nameArray[1], nameArray[1]);
    if(nameArray[1] === "Next") {
        submitButton.onclick = function() {
            date.setMonth(date.getMonth() + 1);
            addHiddenDateInputs(node, date);
        };
    } else if(nameArray[1] === "Prev") {
        submitButton.onclick = function() {
            date.setMonth(date.getMonth() - 1);
            addHiddenDateInputs(node, date);
        };
    }
    anchor = node.getElementsByTagName("a");
    for(i = 0; i < anchor.length; i++) {
        node.removeChild(anchor[i]);
    }
}

/**
 * disableOtherMonthDays
 * Changes anchors in divs with prevMonth and nextMonth classes to static div text;
 * @param {HTMLCollection} dayLinks - the links to be searched
 * @returns {null}
 */
function disableOtherMonthDays(dayLinks) {
    for(i = 0; i < dayLinks.length; i++) {
        if(findClass(dayLinks[i].parentNode, "prevMonth", "find") 
        || findClass(dayLinks[i].parentNode, "nextMonth", "find")) {
            foundNode = dayLinks[i];
            var dayNumber = dayLinks[i].textContent;
            var dayTextNode = document.createTextNode(dayNumber);
            foundNode.parentNode.appendChild(dayTextNode);
            foundNode.parentNode.removeChild(foundNode);
            i--;
        }
    }
}

function convertToForm(node) {
    var selForm = document.createElement("form");
    selForm.setAttribute("id", "date_selector");
    selForm.setAttribute("method", "POST");
    selForm.setAttribute("action", document.location.href);
    var moveNodes = [];
    for(i = 0; i < node.childNodes.length; i++) {
        moveNodes.push(node.childNodes[i]);
    }
    for(i = 0; i < moveNodes.length; i++) {
        childNode = node.removeChild(moveNodes[i]);
        selForm.appendChild(childNode);
    }
    node.appendChild(selForm);
    return selForm;
}

function addSubmit(parentName, name, value) {
    var submitButton = document.createElement("input");
    submitButton.setAttribute("type", "submit");
    submitButton.setAttribute("value", value);
    submitButton.setAttribute("name", name);
    parentName.appendChild(submitButton);
    return submitButton;
}

function addChooser(node) {
    submitButton = addSubmit(node, "dateSubmit", "Choose");
    submitButton.onclick = function() {
        date = getSelectedDay();
        addHiddenDateInputs(node, date);
    };
}

var dateSelectorDiv = document.getElementById("calendar_div");
var daySelectorDiv = document.getElementById("daySelect_div");
var dayLinks = daySelectorDiv.getElementsByTagName("a");
var calPrevDiv = document.getElementById("calPrev_div");
var calNextDiv = document.getElementById("calNext_div");

window.onload = function() {
    changeAnchorTargets(dayLinks);
    changeSelectedDay(dayLinks);
    disableOtherMonthDays(dayLinks);
    selectionForm = convertToForm(dateSelectorDiv);

    changeMonthNav(calPrevDiv);
    changeMonthNav(calNextDiv);
    addChooser(selectionForm);
};