document.addEventListener('DOMContentLoaded', () => {
  const filterForm = document.getElementById('post-filter-form');
  const resultsContainer = document.getElementById('filter-results-container');
  const paginationContainer = document.getElementById('pagination-container');

  if (!filterForm || !resultsContainer || !paginationContainer) return;

  let currentPage = 1;
  let currentSearch = '';
  let currentCategory = '';

  // Function to update URL parameters without reloading
  const updateUrl = () => {
    const params = new URLSearchParams();
    if (currentSearch) {
      params.set('search', currentSearch);
    }
    if (currentCategory) {
      params.set('category', currentCategory);
    }
    if (currentPage > 1) {
      params.set('paged', currentPage);
    }
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({ search: currentSearch, category: currentCategory, paged: currentPage }, '', newUrl);
  };

  const fetchPosts = async (paged = 1) => {
    currentPage = paged;

    const formData = new FormData(filterForm);
    
    currentSearch = formData.get('search');
    currentCategory = formData.get('category');

    const ajaxData = new FormData();
    document.querySelector('input[name="paged"]').value = paged;
    ajaxData.append('action', 'filter_and_paginate');
    ajaxData.append('nonce', filter_params.nonce);
    ajaxData.append('search', currentSearch);
    ajaxData.append('category_slug', currentCategory); // Changed to category_slug
    ajaxData.append('paged', currentPage);

    try {
      const response = await fetch(filter_params.ajax_url, {
        method: 'POST',
        body: ajaxData,
      });

      const result = await response.json();

      if (result.success) {
        resultsContainer.innerHTML = result.data.html || '<p>No posts found.</p>';
        paginationContainer.innerHTML = result.data.pagination || '';
        updateUrl(); // Update URL after successful fetch
      } else {
        resultsContainer.innerHTML = '<p>Something went wrong.</p>';
        paginationContainer.innerHTML = '';
      }
    } catch (error) {
      console.error('Error:', error);
      resultsContainer.innerHTML = '<p>An error occurred.</p>';
      paginationContainer.innerHTML = '';
    }
  };

  // Handle form submission
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    fetchPosts(1); // Reset to first page on new filter/search
  });

  // Handle pagination clicks (delegated event listener)
  paginationContainer.addEventListener('click', (e) => {
    if (e.target.tagName === 'A' && e.target.closest('.pagination-links')) {
      e.preventDefault();
      const pageMatch = e.target.href.match(/paged=(\d+)/);
      if (pageMatch && pageMatch[1]) {
        fetchPosts(parseInt(pageMatch[1]));
      }
    }
  });

  // Initial fetch on page load based on URL parameters
  const params = new URLSearchParams(window.location.search);
  currentSearch = params.get('search') || '';
  currentCategory = params.get('category') || '';
  currentPage = parseInt(params.get('paged')) || 1;

  // Set initial form values from URL
  if (currentSearch) {
    filterForm.querySelector('#search').value = currentSearch;
  }
  if (currentCategory) {
    filterForm.querySelector('#category').value = currentCategory;
  }

  fetchPosts(1); // Fetch posts on initial load
  

});


