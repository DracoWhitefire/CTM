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
    var success;

    if (classArray.indexOf(name) === -1) {
        if (mode === "add") {
            classArray.push(name);
            success = true;
        } else {
            success = false;
        }
    } else if (mode === "del") {
        classArray.splice(classArray.indexOf(name), 1);
        success = true;
    } else if (mode === "find") {
        success = true;
    } else {
        success = false;
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
    if (dayLinks instanceof HTMLCollection) {
        var i;
        for (i = 0; i < dayLinks.length; i++) {
            dayLinks[i].setAttribute("href", "#");
        }
        return true;
    }
}

/**
 * addHiddenDateInputs
 * Adds hidden inputs for POST requests;
 * @param {node} node - the parent node
 * @param {date} date - set date
 */
function addHiddenDateInputs(node, date) {
    var ySelect = document.createElement("input");
    ySelect.setAttribute("type", "hidden");
    ySelect.setAttribute("name", "y");
    ySelect.setAttribute("value", date.getFullYear());
    node.appendChild(ySelect);
    var mSelect = document.createElement("input");
    mSelect.setAttribute("type", "hidden");
    mSelect.setAttribute("name", "m");
    mSelect.setAttribute("value", date.getMonth() + 1);
    node.appendChild(mSelect);
    var dSelect = document.createElement("input");
    dSelect.setAttribute("type", "hidden");
    dSelect.setAttribute("name", "d");
    dSelect.setAttribute("value", date.getDate());
    node.appendChild(dSelect);
}

/**
 * getSelectedDay
 * Date based on calCur_div;
 * @returns {Date}
 */
function getSelectedDay() {
    var selectedDateDiv = document.getElementById("calCur_div");
    var selectedDateArray = selectedDateDiv.textContent.split(" ");
    var selectedDay = selectedDateArray[0];
    var selectedMonth = selectedDateArray[1];
    var selectedYear = selectedDateArray[2];
    var date = new Date(selectedDay + " " + selectedMonth + " " + selectedYear);
    return date;
}

/**
 * changeSelectedDay
 * Unsets selectedDay CSS class on all days and sets it on clicked day;
 * @param {HTMLCollection} dayLinks - the links to be changed
 * @returns {changeSelectedDay}
 */
function changeSelectedDay(dayLinks) {
    var i;
    var linkDivs;
    var selectedDateDiv;
    var date;
    var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    for (i = 0; i < dayLinks.length; i++) {
        dayLinks[i].onclick = function () {
            for (i = 0; i < dayLinks.length; i++) {
                linkDivs = this.parentNode.parentNode.parentNode.getElementsByTagName("div");
                for (i = 0; i < linkDivs.length; i++) {
                    findClass(linkDivs[i], "selectedDay", "del");
                }
            }
            findClass(this.parentNode, "selectedDay", "add");
            selectedDateDiv = document.getElementById("calCur_div");
            date = getSelectedDay();
            date.setDate(this.textContent);
            selectedDateDiv.textContent = date.getDate() + " " + months[date.getMonth()] + " " + date.getFullYear();
        };
    }
}

/**
 * addSubmit
 * Adds submit input as a child node of parentNode;
 * @param {node} parentNode - the parent node
 * @param {type} name - the input's name
 * @param {type} value - the input's value
 * @returns {addSubmit.submitButton|Element} - the created input
 */
function addSubmit(parentNode, name, value) {
    var submitButton = document.createElement("input");

    submitButton.setAttribute("type", "submit");
    submitButton.setAttribute("value", value);
    submitButton.setAttribute("name", name);
    parentNode.appendChild(submitButton);
    return submitButton;
}


/**
 * changeMonthNav
 * changes month selector links to submit inputs
 * @param {node} node
 */
function changeMonthNav(node) {
    var splitString = node.id.replace(/([A-Z])/, "_$1");
    var nameArray = splitString.split("_");
    var date = getSelectedDay();
    var submitButton = addSubmit(node, nameArray[1], nameArray[1]);
    var anchor = node.getElementsByTagName("a");
    var i;

    if (nameArray[1] === "Next") {
        submitButton.onclick = function () {
            date.setMonth(date.getMonth() + 1);
            addHiddenDateInputs(node, date);
        };
    } else if (nameArray[1] === "Prev") {
        submitButton.onclick = function () {
            date.setMonth(date.getMonth() - 1);
            addHiddenDateInputs(node, date);
        };
    }
    for (i = 0; i < anchor.length; i++) {
        node.removeChild(anchor[i]);
    }
}

/**
 * disableOtherMonthDays
 * Changes anchors in divs with prevMonth and nextMonth classes to static div text;
 * @param {HTMLCollection} dayLinks - the links to be searched
 */
function disableOtherMonthDays(dayLinks) {
    var i;
    var foundNode;
    var dayNumber;
    var dayTextNode;

    for (i = 0; i < dayLinks.length; i++) {
        if (findClass(dayLinks[i].parentNode, "prevMonth", "find") || findClass(dayLinks[i].parentNode, "nextMonth", "find")) {
            foundNode = dayLinks[i];
            dayNumber = dayLinks[i].textContent;
            dayTextNode = document.createTextNode(dayNumber);
            foundNode.parentNode.appendChild(dayTextNode);
            foundNode.parentNode.removeChild(foundNode);
            i--;
        }
    }
}

/**
 * convertToForm
 * Created a form as child of parentNode and attaches node's children to it;
 * @param {node} parentNode - the parent node
 * @returns {convertToForm.selForm|Element} - the created form
 */
function convertToForm(parentNode) {
    var selForm = document.createElement("form");
    var moveNodes = [];
    var i;
    var childNode;

    selForm.setAttribute("id", "dateSelector_form");
    selForm.setAttribute("method", "POST");
    selForm.setAttribute("action", document.location.href);
    for (i = 0; i < parentNode.childNodes.length; i++) {
        moveNodes.push(parentNode.childNodes[i]);
    }
    for (i = 0; i < moveNodes.length; i++) {
        childNode = parentNode.removeChild(moveNodes[i]);
        selForm.appendChild(childNode);
    }
    parentNode.appendChild(selForm);
    return selForm;
}



/**
 * addChooser
 * Adds submit input as a child node of parentNode;
 * @param {node} parentNode - the parent node
 * @returns {addSubmit.submitButton|Element|Node|addChooser.submitButton} - the created input
 */
function addChooser(parentNode) {
    var submitButton = addSubmit(parentNode, "dateSubmit", "Choose");
    submitButton.onclick = function () {
        var date = getSelectedDay();
        addHiddenDateInputs(parentNode, date);
    };
    return submitButton;
}



window.onload = function () {
    var dateSelectorDiv = document.getElementById("calendar_div");
    var daySelectorDiv = document.getElementById("daySelect_div");
    var dayLinks = daySelectorDiv.getElementsByTagName("a");
    var calPrevDiv = document.getElementById("calPrev_div");
    var calNextDiv = document.getElementById("calNext_div");
    var selectionForm = convertToForm(dateSelectorDiv);

    changeAnchorTargets(dayLinks);
    changeSelectedDay(dayLinks);
    disableOtherMonthDays(dayLinks);
    changeMonthNav(calPrevDiv);
    changeMonthNav(calNextDiv);
    addChooser(selectionForm);
};