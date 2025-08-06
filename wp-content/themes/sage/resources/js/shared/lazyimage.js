export const imageLazyLoading = () => {
    const lazyImages = document.querySelectorAll("[loading='lazy']");

    if (lazyImages.length == 0) return;

    lazyImages.forEach((lazyImage) => {
      if (lazyImage.complete) {
        lazyImage.classList.add('is-loaded');
      } else {
        lazyImage.addEventListener(
          'load',
          () => {
            lazyImage.classList.add('is-loaded');
          },
          { once: true }
        );
      }
    });

    lazyLoadImageAll();
  };

  const lazyLoadImageAll = () => {
    const lazyImages = document.querySelectorAll('.js-lazy-load-all');
    if (lazyImages.length == 0) {
      return;
    }

    const checkIntersection = (entries, observer) => {
      entries.forEach((entry) => {
        let lazyImageWrapper = entry.target;
        if (entry.isIntersecting) {
          observer.unobserve(lazyImageWrapper);

          let images = lazyImageWrapper.querySelectorAll('img[loading="lazy"]');
          if (images.length == 0) {
            return;
          }
          removeLoadingAttributes(images);
        }
      });
    };
    const removeLoadingAttributes = (images) => {
      images.forEach((image) => {
        image.removeAttribute('loading');
      });
    };

    if ('IntersectionObserver' in window) {
      let options = {
        root: null,
        rootMargin: '200px',
        threshold: 0.5,
      };

      let lazyImageObserver = new IntersectionObserver(checkIntersection, options);

      lazyImages.forEach((lazyImage) => {
        lazyImageObserver.observe(lazyImage);
      });
    } else {
      lazyImages.forEach((lazyImage) => {
        let images = lazyImage.querySelectorAll('img[loading="lazy"]');
        if (images.length == 0) {
          return;
        }
        removeLoadingAttributes(images);
      });
    }
  };
