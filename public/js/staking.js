window.counter = JSON.parse(document.getElementById('divCounter').dataset.counter);
window.collection = JSON.parse(document.getElementById('divCollection').dataset.collection);

function burnCard(){

    let card = window.collection[previousChosenCard];
    console.log(card)
    burn(card['asset_id'], card['template_id'])

}
