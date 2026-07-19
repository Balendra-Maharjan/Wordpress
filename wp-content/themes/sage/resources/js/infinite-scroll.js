const initInfiniteScroll = () => {
  const container = document.getElementById('infinite-posts-container');
  const anchor = document.getElementById('infinite-scroll-anchor');

  if (!container || !anchor) return;

  let isLoading = false;
  let currentPage = parseInt(anchor.dataset.page);
  const maxPages = parseInt(anchor.dataset.maxPages);
  const spinner = anchor.querySelector('.loader-spinner');

  const loadMorePosts = async () => {
    if (isLoading || currentPage > maxPages) return;

    isLoading = true;
    spinner.classList.remove('hidden');

    const formData = new FormData();
    formData.append('action', 'load_more_projects');
    formData.append('paged', currentPage);

    try {
      const response = await fetch(filter_params.ajax_url, {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        container.insertAdjacentHTML('beforeend', result.data.html);
        
        currentPage++;
        anchor.dataset.page = currentPage;

        if (!result.data.has_more) {
          observer.unobserve(anchor);
          anchor.innerHTML = '<p class="text-gray-500 italic py-4">No more posts to load.</p>';
        }
      }
    } catch (error) {
      console.error('Infinite Scroll Error:', error);
    } finally {
      // Add a small delay before allowing the next load to prevent "runaway" loading
      setTimeout(() => {
        isLoading = false;
        spinner.classList.add('hidden');
      }, 500);
    }
  };

  const observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      loadMorePosts();
    }
  }, {
    rootMargin: '100px', 
  });

  observer.observe(anchor);
};

document.addEventListener('DOMContentLoaded', initInfiniteScroll);
document.addEventListener('theme:load', initInfiniteScroll);
