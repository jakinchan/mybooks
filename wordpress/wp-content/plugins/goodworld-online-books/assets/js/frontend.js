(function () {
  function normalize(value) {
    return (value || '').toString().toLowerCase();
  }

  document.querySelectorAll('.gwob-bookshelf').forEach(function (shelf) {
    var search = shelf.querySelector('.gwob-search');
    var category = shelf.querySelector('.gwob-category-filter');
    var cards = Array.prototype.slice.call(shelf.querySelectorAll('.gwob-card'));

    function applyFilters() {
      var query = normalize(search ? search.value : '');
      var selected = category ? category.value : '';

      cards.forEach(function (card) {
        var titleMatch = normalize(card.dataset.title).indexOf(query) !== -1;
        var categoryMatch = !selected || normalize(card.dataset.categories).split(' ').indexOf(selected) !== -1;
        card.hidden = !(titleMatch && categoryMatch);
      });
    }

    if (search) {
      search.addEventListener('input', applyFilters);
    }

    if (category) {
      category.addEventListener('change', applyFilters);
    }
  });
})();
