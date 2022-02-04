window.page = 1;
window.previousChosenCard = 0;
search(window.page);

function search(page) {
    let pathname = new URL(window.location.href).pathname;
    let searchInput = document.getElementById('searchInput');
    $.ajax({
        type: 'GET',
        url: pathname + '/list',
        data: {
            'page': page,
            'query': searchInput.value
        },
        success: function (response) {
            document.getElementById('collectionList').innerHTML = response;
            window.collection = JSON.parse(document.getElementById('divCollection').dataset.collection)['data'];
            window.lastPage = JSON.parse(document.getElementById('divCollection').dataset.collection)['last_page'];
            if (document.getElementById('divAvailabilityTemplateId')){
                window.availabilityCards = JSON.parse(document.getElementById('divAvailabilityTemplateId')
                    .dataset.availabilityTemplateId);
            }
            selectCard(0);
            showCardInformation();
        }
    });
}

function showCardInformation(){
    let cardSearch = document.getElementById('cardSearch');
    let cardFullDescription = document.getElementById('cardFullDescription');
    let cardBorderImage = document.getElementById('cardBorderImage');

    cardSearch.classList.remove('d-none');
    cardFullDescription.classList.remove('d-none');
    cardBorderImage.classList.remove('d-none');
}

function nextPage() {
    if (window.page < window.lastPage) {
        window.page++;
    }
    search(window.page);
}

function previousPage() {
    if (window.page > 1) {
        window.page--;
    }
    search(window.page);
}

function toPage(page) {
    window.page = page;
    search(window.page);
}

let paginator = document.getElementById('first-pagination');
paginator.oninput = function () {
    search(paginator.value);
};
