document.addEventListener('DOMContentLoaded', () => {
  const filterForm = document.getElementById('post-filter-form');
  const resultsContainer = document.getElementById('filter-results-container');
  const paginationContainer = document.getElementById('pagination-container');

  if (!filterForm || !resultsContainer || !paginationContainer) return;

  let currentPage = 1;
  let currentSearch = '';
  let currentCategory = '';

  const showLoading = () => {
    resultsContainer.innerHTML = '';
    const loadingEl = document.createElement('p');
    loadingEl.className = "text-center col-span-full";
    loadingEl.textContent = "Loading...";
    resultsContainer.appendChild(loadingEl);
  };

  // Update browser URL
  const updateUrl = () => {
    const params = new URLSearchParams();
    if (currentSearch) params.set('search', currentSearch);
    if (currentCategory) params.set('category', currentCategory); 
    if (currentPage > 1) params.set('paged', currentPage);

    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState(
      { search: currentSearch, category: currentCategory, paged: currentPage },
      '',
      newUrl
    );
  };

  // Fetch posts
  const fetchPosts = async (paged = 1, skipPush = false) => {
    currentPage = paged;
    filterForm.querySelector('input[name="paged"]').value = paged;

    const formData = new FormData(filterForm);
    currentSearch = formData.get('search') || '';
    currentCategory = formData.get('category') || ''; 

    showLoading();

    try {
      const response = await fetch(filter_params.ajax_url, {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Replace posts safely
        const tempResults = document.createElement('div');
        tempResults.innerHTML = result.data.html || '<p class="col-span-full text-center">No posts found.</p>';
        resultsContainer.replaceChildren(...tempResults.children);

        // Replace pagination safely
        const tempPagination = document.createElement('div');
        tempPagination.innerHTML = result.data.pagination || '';
        paginationContainer.replaceChildren(...tempPagination.children);

        if (!skipPush) updateUrl();
      } else {
        resultsContainer.replaceChildren(Object.assign(document.createElement('p'), {
          className: "col-span-full text-center",
          textContent: "An error occurred."
        }));
      }
    } catch (error) {
      console.error('Error:', error);
      resultsContainer.replaceChildren(Object.assign(document.createElement('p'), {
        className: "col-span-full text-center",
        textContent: "An error occurred."
      }));
    }
  };

  // Form submit
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchPosts(1);
  });

  // Pagination clicks
  paginationContainer.addEventListener('click', (e) => {
    if (e.target.tagName === 'A' && e.target.closest('.pagination-links')) {
      e.preventDefault();
      const pageMatch = e.target.href.match(/paged=(\d+)/);
      if (pageMatch && pageMatch[1]) {
        fetchPosts(parseInt(pageMatch[1], 10));
      }
    }
  });

  // Back/forward navigation
  window.addEventListener('popstate', (e) => {
    if (e.state) {
      currentSearch = e.state.search || '';
      currentCategory = e.state.category || ''; 
      currentPage = e.state.paged || 1;

      filterForm.querySelector('[name="search"]').value = currentSearch;
      filterForm.querySelector('[name="category"]').value = currentCategory;
      filterForm.querySelector('input[name="paged"]').value = currentPage;

      fetchPosts(currentPage, true);
    }
  });

  // Initial load from URL params
  const urlParams = new URLSearchParams(window.location.search);
  currentSearch = urlParams.get('search') || '';
  currentCategory = urlParams.get('category') || ''; 
  currentPage = parseInt(urlParams.get('paged') || '1', 10);

  filterForm.querySelector('[name="search"]').value = currentSearch;
  filterForm.querySelector('[name="category"]').value = currentCategory;
  filterForm.querySelector('input[name="paged"]').value = currentPage;
});
