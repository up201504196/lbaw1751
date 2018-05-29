'use strict'

document.getElementById("questionSearchBar").addEventListener("keyup", handleUserSearchInput);

function handleUserSearchInput(event)
{
    let defaultContentDiv = document.getElementById('contentID');
    let searchedQuestionsDiv = document.getElementById('changedContentID');
    let userInputText = event.target.value.trim();
    if (userInputText == "")
    {
        defaultContentDiv.hidden = false;
        defaultContentDiv.style.display = 'block';
        searchedQuestionsDiv.hidden = true;
        searchedQuestionsDiv.style.display = 'none';
        return;
    }

    defaultContentDiv.hidden = true;
    defaultContentDiv.style.display = 'none';
    searchedQuestionsDiv.hidden = false;
    searchedQuestionsDiv.style.display = 'block';
    let keywordsArr = userInputText.split(" ");
    //Get array with tags
    let tagsArr = userInputText.match(/#\S+/g);

    //Get array with keyword, i.e., everything except the tags
    for (let i = 0; i < tagsArr.length; i++)
        removeStringFromArray(keywordsArr, tagsArr[i]);

    removeHashTagsFromBeggingOfEachTagOnArray(tagsArr);


    //console.log(tagsArr);
    //console.log(keywordsArr);

    let tagsArrEncoded = JSON.stringify(tagsArr);
    let keywordsArrEncoded = JSON.stringify(keywordsArr);
    //console.log(JSON.parse(tagsArrEncoded));

    //get csrf token
    let csrfToken = document.getElementById("csrf-token").innerHTML;

    let ajaxRequest = new XMLHttpRequest();
    ajaxRequest.addEventListener("load", searchResultsArrived);
    ajaxRequest.open("POST", "/search/question", true);
    ajaxRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxRequest.setRequestHeader("X-CSRF-Token", csrfToken);
    ajaxRequest.send(encodeForAjax({tags: tagsArrEncoded, keywords: keywordsArrEncoded}));
}

function removeHashTagsFromBeggingOfEachTagOnArray(tags)
{
    for (let i = 0; i < tags.length; i++)
        tags[i] = tags[i].substr(1, tags[i].length);
}

function removeStringFromArray(arr, what)
{
    var found = arr.indexOf(what);

    while (found !== -1)
    {
        arr.splice(found, 1);
        found = arr.indexOf(what);
    }
}

function searchResultsArrived()
{
    console.log("searchResultsArrived");
    console.log(this.responseText);

}

function getElementInSearch(element, userInputText, endedSearchString)
{
    let request = new XMLHttpRequest();
    request.addEventListener("load", function receiveServerResponse()
    {

        let elements = JSON.parse(this.responseText);
        if (elements.length == 0)
            return;

        let searchResultOptionsGroup = document.createElement('optgroup');
        searchResultOptionsGroup.label = element;
        let innerHTML = ``;
        for (let i = 0; i < elements.length; i++)
        {
            innerHTML += `<option class="` + elements[i]['id'] +
                    `" onclick="handle` + element + `Selection(event)" >` + elements[i]['name'] + `</option>`;
        }

        searchResultOptionsGroup.innerHTML = innerHTML;

        let elementsList = document.getElementById('resultsList');
        elementsList.insertAdjacentElement('beforeend', searchResultOptionsGroup);
    });
    request.open("GET", "../search/getUser" + element + ".php?" +
            encodeForAjax({name: userInputText, endedOption: endedSearchString}), true);
    request.send();
}

function searchAndReplaceInString(string, expression)
{
    let found_expression = false;
    if (string.toLowerCase().includes(expression))
    {
        found_expression = true;
        string = string.toLowerCase().replace(expression, "");
    }
    string = string.trim();

    return [found_expression, string];
}
