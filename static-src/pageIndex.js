import './pageIndex.less';

window.addEventListener('load', () => {
    var cardContainer = document.querySelector('.help-cards');
    var masonry = new Masonry(cardContainer, {
        // options
        columnWidth: '.help-card',
        itemSelector: '.help-card',
        percentPosition: true,
    });
});